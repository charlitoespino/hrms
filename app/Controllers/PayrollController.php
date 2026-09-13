<?php
declare(strict_types=1);

final class PayrollController
{
    public function index(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('payroll.view');
        $page = max(1, Input::int('page', 'get', 1));
        $result = (new Payroll())->periodPaginate($page, 15);
        View::render('payroll/index', [
            'title'      => 'Payroll Periods',
            'periods'    => $result['items'],
            'pagination' => $result,
        ]);
    }

    public function create(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('payroll.process');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CSRF::verifyRequest();
            $data = [
                'period_name'   => Input::str('period_name'),
                'date_from'     => Input::str('date_from'),
                'date_to'       => Input::str('date_to'),
                'pay_date'      => Input::str('pay_date'),
                'pay_frequency' => Input::str('pay_frequency', 'post', 'Semi-Monthly'),
                'notes'         => Input::str('notes'),
            ];
            $v = (new Validator($data))
                ->required('period_name', 'Period Name')
                ->required('date_from', 'Start Date')->date('date_from', 'Start Date')
                ->required('date_to', 'End Date')->date('date_to', 'End Date')
                ->required('pay_date', 'Pay Date')->date('pay_date', 'Pay Date')
                ->custom('date_to', strtotime($data['date_to']) >= strtotime($data['date_from']), 'End date must be after start date.');
            if ($v->fails()) {
                Flash::error($v->firstError());
                Response::redirect('/payroll/create');
            }

            $data['status'] = 'Draft';
            $data['created_by'] = Auth::id();
            $id = (new Payroll())->insert($data);
            Audit::log('payroll.period_created', 'payroll', (string) $id, null, $data['period_name']);
            Flash::success('Payroll period created.');
            Response::redirect('/payroll/view?id=' . $id);
            return;
        }

        View::render('payroll/create', ['title' => 'New Payroll Period']);
    }

    public function view(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('payroll.view');
        $id = Input::int('id', 'get');
        $period = (new Payroll())->period($id);
        if (!$period) Response::abort(404);
        $items = (new Payroll())->itemsForPeriod($id);

        $totalGross = array_sum(array_column($items, 'gross_pay'));
        $totalNet   = array_sum(array_column($items, 'net_pay'));
        $totalDed   = array_sum(array_column($items, 'total_deductions'));

        View::render('payroll/view', [
            'title'      => 'Payroll: ' . $period['period_name'],
            'period'     => $period,
            'items'      => $items,
            'totalGross' => $totalGross,
            'totalNet'   => $totalNet,
            'totalDed'   => $totalDed,
        ]);
    }

    public function process(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('payroll.process');
        CSRF::verifyRequest();
        $id = Input::int('id');
        try {
            $result = (new PayrollService(Database::pdo(), new GovernmentContributionService(), new TaxService()))->process($id);
            Response::json(['ok' => true, 'processed' => $result['processed']]);
        } catch (Throwable $e) {
            Response::json(['ok' => false, 'error' => $e->getMessage()], 400);
        }
    }

    public function approve(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('payroll.approve');
        CSRF::verifyRequest();
        $id = Input::int('id');
        try {
            (new PayrollService(Database::pdo(), new GovernmentContributionService(), new TaxService()))->approve($id);
            Response::json(['ok' => true]);
        } catch (Throwable $e) {
            Response::json(['ok' => false, 'error' => $e->getMessage()], 400);
        }
    }

    public function lock(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('payroll.approve');
        CSRF::verifyRequest();
        $id = Input::int('id');
        try {
            (new PayrollService(Database::pdo(), new GovernmentContributionService(), new TaxService()))->lock($id);
            Response::json(['ok' => true]);
        } catch (Throwable $e) {
            Response::json(['ok' => false, 'error' => $e->getMessage()], 400);
        }
    }

    public function payslip(): void
    {
        AuthMiddleware::handle();
        $id = Input::int('id', 'get');
        $payroll = (new Payroll())->find($id);
        if (!$payroll) Response::abort(404);

        // Employees can only view their own payslip
        if (Auth::hasRole('employee') && !Auth::can('payroll.process')) {
            if ((int) $payroll['employee_id'] !== (int) Auth::employeeId()) {
                Response::abort(403);
            }
        } else {
            PermissionMiddleware::require('payroll.view');
        }

        $detail = (new Payroll())->findForEmployeePeriod((int) $payroll['employee_id'], $id);
        if (!$detail) Response::abort(404);

        $config = require BASE_PATH . '/config/config.php';
        $settings = new SystemSetting();

        View::render('payroll/payslip', [
            'title'    => 'Payslip ' . $detail['period_name'],
            'payroll'  => $detail,
            'items'    => (new Payroll())->items($id),
            'company'  => [
                'name'    => $settings->get('company_name', 'Demo Corp'),
                'address' => $settings->get('company_address', ''),
                'tin'     => $settings->get('company_tin', ''),
            ],
        ], 'blank');
    }

    public function myPayslips(): void
    {
        AuthMiddleware::handle();
        $employeeId = Auth::employeeId();
        if (!$employeeId) Response::abort(403);
        View::render('payroll/my-payslips', [
            'title'    => 'My Payslips',
            'payslips' => (new Payroll())->payslipsForEmployee($employeeId, 50),
        ]);
    }
}