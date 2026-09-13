<?php
declare(strict_types=1);

/**
 * Computes SSS / PhilHealth / Pag-IBIG contributions
 * from configurable government_contribution_tables.
 *
 * IMPORTANT: Rates in the database are illustrative SAMPLE data.
 * Update them according to current official rules before production use.
 */
final class GovernmentContributionService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::pdo();
    }

    /**
     * @return array{employee:float, employer:float}
     */
    public function compute(string $type, float $monthlySalary, string $asOfDate): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM government_contribution_tables
             WHERE contribution_type = :t
               AND is_active = 1
               AND effective_from <= :d
               AND (effective_to IS NULL OR effective_to >= :d)
               AND :s BETWEEN salary_min AND salary_max
             ORDER BY salary_min ASC LIMIT 1'
        );
        $stmt->execute([':t' => $type, ':d' => $asOfDate, ':s' => $monthlySalary]);
        $row = $stmt->fetch();

        if (!$row) {
            return ['employee' => 0.0, 'employer' => 0.0];
        }

        $base = min($monthlySalary, (float) $row['salary_max']);
        $employee = (float) $row['employee_fixed'] + ($base * (float) $row['employee_rate']);
        $employer = (float) $row['employer_fixed'] + ($base * (float) $row['employer_rate']);

        // Pag-IBIG: cap employee share at ₱100/month as per common practice
        if ($type === 'Pag-IBIG') {
            $employee = min($employee, 100.0);
            $employer = min($employer, 100.0);
        }

        return [
            'employee' => round($employee, 2),
            'employer' => round($employer, 2),
        ];
    }
}