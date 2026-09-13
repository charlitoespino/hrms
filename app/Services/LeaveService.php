<?php
declare(strict_types=1);

final class LeaveService
{
    public function __construct(private Leave $model) {}

    public function approveByManager(int $requestId, int $managerId): array
    {
        return $this->transition($requestId, 'Manager Approved', $managerId, 'manager');
    }

    public function approveByHR(int $requestId, int $hrId): array
    {
        return $this->transition($requestId, 'Approved', $hrId, 'hr');
    }

    public function reject(int $requestId, int $userId, string $remarks): array
    {
        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('UPDATE leave_requests SET status = "Rejected", remarks = :r WHERE id = :id');
            $stmt->execute([':r' => $remarks, ':id' => $requestId]);
            $pdo->commit();
            Audit::log('leave.rejected', 'leave', (string) $requestId, null, $remarks);
            return ['ok' => true];
        } catch (Throwable $e) {
            $pdo->rollBack();
            return ['ok' => false, 'error' => 'Failed to reject request.'];
        }
    }

    private function transition(int $requestId, string $newStatus, int $userId, string $who): array
    {
        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            $req = $this->model->findDetailed($requestId);
            if (!$req) {
                throw new RuntimeException('Request not found.');
            }
            if ($who === 'manager' && $req['status'] !== 'Pending') {
                throw new RuntimeException('Only pending requests can be approved by manager.');
            }
            if ($who === 'hr' && !in_array($req['status'], ['Manager Approved','Pending'], true)) {
                throw new RuntimeException('Only pending or manager-approved requests can be approved.');
            }

            if ($who === 'manager') {
                $pdo->prepare('UPDATE leave_requests SET status = :s, manager_id = :u, manager_approved_at = NOW() WHERE id = :id')
                    ->execute([':s' => $newStatus, ':u' => $userId, ':id' => $requestId]);
            } else {
                $pdo->prepare('UPDATE leave_requests SET status = :s, hr_id = :u, hr_approved_at = NOW() WHERE id = :id')
                    ->execute([':s' => $newStatus, ':u' => $userId, ':id' => $requestId]);
                // Consume balance
                $year = (int) date('Y', strtotime($req['start_date']));
                $this->model->consumeBalance((int) $req['employee_id'], (int) $req['leave_type_id'], $year, (float) $req['days']);
            }

            $pdo->commit();
            Audit::log('leave.approved', 'leave', (string) $requestId, null, $newStatus);
            return ['ok' => true];
        } catch (Throwable $e) {
            $pdo->rollBack();
            Logger::error('Leave transition failed: ' . $e->getMessage());
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}