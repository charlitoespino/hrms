<?php
declare(strict_types=1);

/**
 * Payroll processing service.
 *
 * Contribution/tax rates are pulled from configurable tables.
 * Sample rates in seed.sql are illustrative only and MUST be verified
 * against current official Philippine government rules before production.
 */
final class PayrollService
{
    public function __construct(
        private PDO $db,
        private GovernmentContributionService $gov,
        private TaxService $tax
    ) {}

    /**
     * Compute payroll for all active employees in a period.
     * Uses a DB transaction — rolls back on any error.
     */
    public function process(int $periodId): array
    {
        $period = $this->loadPeriod($periodId);
        if (!$period) {
            throw new RuntimeException('Payroll period not found.');
        }
        if (!in_array($period['status'], ['Draft','Processing'], true)) {
            throw new RuntimeException('Period cannot be processed in its current state.');
        }

        $this->db->beginTransaction();
        try {
            $this->db->prepare('UPDATE payroll_periods SET status = "Processing" WHERE id = :id')
                     ->execute([':id' => $periodId]);

            $employees = $this->activeEmployees();
            $created = 0;

            foreach ($employees as $emp) {
                $this->computeForEmployee($period, $emp);
                $created++;
            }

            $this->db->prepare('UPDATE payroll_periods SET status = "For Review" WHERE id = :id')
                     ->execute([':id' => $periodId]);

            $this->db->commit();
            Audit::log('payroll.processed', 'payroll', (string) $periodId, null, "employees=$created");
            return ['ok' => true, 'processed' => $created];
        } catch (Throwable $e) {
            $this->db->rollBack();
            Logger::error('Payroll processing failed: ' . $e->getMessage(), ['period' => $periodId]);
            throw new RuntimeException('Payroll processing failed. Please try again.');
        }
    }

    public function approve(int $periodId): void
    {
        $this->db->beginTransaction();
        try {
            $this->db->prepare(
                'UPDATE payroll_periods SET status = "Approved", approved_by = :u, approved_at = NOW() WHERE id = :id'
            )->execute([':u' => Auth::id(), ':id' => $periodId]);
            $this->db->prepare('UPDATE payrolls SET status = "Approved" WHERE payroll_period_id = :id')
                     ->execute([':id' => $periodId]);
            $this->db->commit();
            Audit::log('payroll.approved', 'payroll', (string) $periodId);
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw new RuntimeException('Approval failed.');
        }
    }

    public function lock(int $periodId): void
    {
        $this->db->beginTransaction();
        try {
            $this->db->prepare(
                'UPDATE payroll_periods SET status = "Locked", locked_at = NOW() WHERE id = :id'
            )->execute([':id' => $periodId]);
            $this->db->prepare('UPDATE payrolls SET status = "Locked" WHERE payroll_period_id = :id')
                     ->execute([':id' => $periodId]);
            $this->generatePayslips($periodId);
            $this->db->commit();
            Audit::log('payroll.locked', 'payroll', (string) $periodId);
        } catch (Throwable $e) {
            $this->db->rollBack();
            Logger::error('Lock failed: ' . $e->getMessage());
            throw new RuntimeException('Lock failed.');
        }
    }

    private function generatePayslips(int $periodId): void
    {
        $items = $this->db->prepare('SELECT id, employee_id FROM payrolls WHERE payroll_period_id = :p');
        $items->execute([':p' => $periodId]);
        foreach ($items->fetchAll() as $row) {
            $exists = $this->db->prepare('SELECT id FROM payslips WHERE payroll_id = :pid');
            $exists->execute([':pid' => $row['id']]);
            if ($exists->fetch()) continue;

            $slip = 'PS-' . str_pad((string) $row['id'], 6, '0', STR_PAD_LEFT);
            $ins = $this->db->prepare(
                'INSERT INTO payslips (payroll_id, employee_id, slip_number, issued_at)
                 VALUES (:p, :e, :s, NOW())'
            );
            $ins->execute([':p' => $row['id'], ':e' => $row['employee_id'], ':s' => $slip]);
        }
    }

    private function loadPeriod(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM payroll_periods WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    private function activeEmployees(): array
    {
        return $this->db->query(
            "SELECT * FROM employees
             WHERE deleted_at IS NULL AND employment_status <> 'Separated'
             ORDER BY employee_code"
        )->fetchAll();
    }

    private function computeForEmployee(array $period, array $emp): void
    {
        // Remove existing computation for this period/employee (safe — same transaction)
        $del = $this->db->prepare('DELETE FROM payrolls WHERE payroll_period_id = :p AND employee_id = :e');
        $del->execute([':p' => $period['id'], ':e' => $emp['id']]);

        $monthlySalary = (float) $emp['basic_salary'];
        $semiMonthly   = round($monthlySalary / 2, 2);

        // Days worked within period
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS days FROM attendance
             WHERE employee_id = :e AND attendance_date BETWEEN :from AND :to
               AND status IN ('Present','Late','Official Business','Work From Home','Half Day')"
        );
        $stmt->execute([':e' => $emp['id'], ':from' => $period['date_from'], ':to' => $period['date_to']]);
        $daysWorked = (float) $stmt->fetchColumn();

        $basicPay = $semiMonthly;
        $allowance = 0.0;

        // Government contributions (based on monthly salary)
        $sss      = $this->gov->compute('SSS', $monthlySalary, $period['date_to']);
        $ph       = $this->gov->compute('PhilHealth', $monthlySalary, $period['date_to']);
        $pi       = $this->gov->compute('Pag-IBIG', $monthlySalary, $period['date_to']);

        // For semi-monthly payroll, split contributions in half
        $sssEmp = round($sss['employee'] / 2, 2);
        $phEmp  = round($ph['employee'] / 2, 2);
        $piEmp  = round($pi['employee'] / 2, 2);
        $sssEr  = round($sss['employer'] / 2, 2);
        $phEr   = round($ph['employer'] / 2, 2);
        $piEr   = round($pi['employer'] / 2, 2);

        $taxableIncome = max(0, $basicPay + $allowance - $sssEmp - $phEmp - $piEmp);
        $tax = $this->tax->compute('Semi-Monthly', $taxableIncome, $period['date_to']);

        $grossPay = round($basicPay + $allowance, 2);
        $totalDeductions = round($sssEmp + $phEmp + $piEmp + $tax, 2);
        $netPay = round($grossPay - $totalDeductions, 2);

        $insert = $this->db->prepare(
            'INSERT INTO payrolls
             (payroll_period_id, employee_id, basic_salary, days_worked, gross_pay, total_earnings,
              sss_employee, sss_employer, philhealth_employee, philhealth_employer,
              pagibig_employee, pagibig_employer, withholding_tax, other_deductions,
              total_deductions, net_pay, status)
             VALUES
             (:pp, :e, :bs, :dw, :gp, :te, :sse, :ssr, :phe, :phr, :pie, :pir, :tax, 0, :td, :np, "Computed")'
        );
        $insert->execute([
            ':pp' => $period['id'], ':e' => $emp['id'], ':bs' => $semiMonthly, ':dw' => $daysWorked,
            ':gp' => $grossPay, ':te' => $grossPay,
            ':sse' => $sssEmp, ':ssr' => $sssEr,
            ':phe' => $phEmp, ':phr' => $phEr,
            ':pie' => $piEmp, ':pir' => $piEr,
            ':tax' => $tax, ':td' => $totalDeductions, ':np' => $netPay,
        ]);
        $payrollId = (int) $this->db->lastInsertId();

        $this->insertItem($payrollId, 'Earning', 'BASIC', 'Basic Salary', $semiMonthly);
        $this->insertItem($payrollId, 'Deduction', 'SSS',  'SSS Contribution', $sssEmp);
        $this->insertItem($payrollId, 'Deduction', 'PH',   'PhilHealth Contribution', $phEmp);
        $this->insertItem($payrollId, 'Deduction', 'PI',   'Pag-IBIG Contribution', $piEmp);
        $this->insertItem($payrollId, 'Deduction', 'TAX',  'Withholding Tax', $tax);
    }

    private function insertItem(int $payrollId, string $type, string $code, string $name, float $amount): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO payroll_items (payroll_id, item_type, item_code, item_name, amount)
             VALUES (:p, :t, :c, :n, :a)'
        );
        $stmt->execute([':p' => $payrollId, ':t' => $type, ':c' => $code, ':n' => $name, ':a' => $amount]);
    }
}