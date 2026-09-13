<?php
declare(strict_types=1);

final class HolidayController
{
    public function index(): void
    {
        AuthMiddleware::handle();
        $year = Input::int('year', 'get', (int) date('Y'));
        View::render('holidays/index', [
            'title'    => 'Holidays ' . $year,
            'holidays' => (new Holiday())->forYear($year),
            'year'     => $year,
        ]);
    }

    public function store(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('holidays.manage');
        CSRF::verifyRequest();

        $data = [
            'name'          => Input::str('name'),
            'holiday_date'  => Input::str('holiday_date'),
            'holiday_type'  => Input::str('holiday_type'),
            'description'   => Input::str('description'),
            'is_active'     => 1,
        ];
        $validator = (new Validator($data))
            ->required('name', 'Holiday Name')
            ->required('holiday_date', 'Date')
            ->date('holiday_date', 'Date')
            ->required('holiday_type', 'Holiday Type');
        if ($validator->fails()) {
            Flash::error($validator->firstError());
            Response::redirect('/holidays');
        }
        (new Holiday())->insert($data);
        Audit::log('holiday.created', 'holidays', null, null, $data['name']);
        Flash::success('Holiday added.');
        Response::redirect('/holidays');
    }

    public function update(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('holidays.manage');
        CSRF::verifyRequest();
        $id = Input::int('id');
        (new Holiday())->update($id, [
            'name'          => Input::str('name'),
            'holiday_date'  => Input::str('holiday_date'),
            'holiday_type'  => Input::str('holiday_type'),
            'description'   => Input::str('description'),
            'is_active'     => Input::bool('is_active') ? 1 : 0,
        ]);
        Audit::log('holiday.updated', 'holidays', (string) $id);
        Flash::success('Holiday updated.');
        Response::redirect('/holidays');
    }

    public function destroy(): void
    {
        AuthMiddleware::handle();
        PermissionMiddleware::require('holidays.manage');
        CSRF::verifyRequest();
        $id = Input::int('id');
        (new Holiday())->delete($id);
        Audit::log('holiday.deleted', 'holidays', (string) $id);
        Flash::success('Holiday deleted.');
        Response::redirect('/holidays');
    }
}