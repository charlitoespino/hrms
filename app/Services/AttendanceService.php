<?php
declare(strict_types=1);

final class AttendanceService
{
    public function __construct(private Attendance $model, private Holiday $holidays) {}

    public function clockIn(int $employeeId): array
    {
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');

        if ($this->holidays->isHoliday($today)) {
            return ['ok' => false, 'error' => 'Today is a holiday.'];
        }

        $record = $this->model->findByEmployeeAndDate($employeeId, $today);
        if ($record && $record['time_in']) {
            return ['ok' => false, 'error' => 'You have already clocked in today.'];
        }

        $scheduledIn = strtotime($today . ' 09:00:00'); // 9 AM default
        $lateMinutes = max(0, (int) round(($now ? strtotime($now) - $scheduledIn : 0) / 60));

        if ($record) {
            $upd = Database::pdo()->prepare(
                'UPDATE attendance SET time_in = :ti, late_minutes = :lm, status = :s WHERE id = :id'
            );
            $upd->execute([':ti' => $now, ':lm' => $lateMinutes, ':s' => $lateMinutes > 0 ? 'Late' : 'Present', ':id' => $record['id']]);
        } else {
            $ins = Database::pdo()->prepare(
                'INSERT INTO attendance (employee_id, attendance_date, time_in, late_minutes, status)
                 VALUES (:e, :d, :ti, :lm, :s)'
            );
            $ins->execute([
                ':e' => $employeeId, ':d' => $today, ':ti' => $now,
                ':lm' => $lateMinutes, ':s' => $lateMinutes > 0 ? 'Late' : 'Present',
            ]);
        }
        Audit::log('attendance.clock_in', 'attendance', (string) $employeeId);
        return ['ok' => true];
    }

    public function clockOut(int $employeeId): array
    {
        $today = date('Y-m-d');
        $record = $this->model->findByEmployeeAndDate($employeeId, $today);
        if (!$record || !$record['time_in']) {
            return ['ok' => false, 'error' => 'You have not clocked in yet today.'];
        }
        if ($record['time_out']) {
            return ['ok' => false, 'error' => 'You have already clocked out today.'];
        }

        $now = date('Y-m-d H:i:s');
        $in = strtotime($record['time_in']);
        $out = strtotime($now);
        $hours = max(0, round(($out - $in) / 3600, 2));

        $scheduledOut = strtotime($today . ' 18:00:00');
        $undertime = max(0, (int) round(($scheduledOut - $out) / 60));

        $upd = Database::pdo()->prepare(
            'UPDATE attendance SET time_out = :to, hours_worked = :hw, undertime_minutes = :ut WHERE id = :id'
        );
        $upd->execute([':to' => $now, ':hw' => $hours, ':ut' => $undertime, ':id' => $record['id']]);
        Audit::log('attendance.clock_out', 'attendance', (string) $employeeId);
        return ['ok' => true];
    }
}