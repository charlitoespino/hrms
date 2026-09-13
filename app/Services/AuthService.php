<?php
declare(strict_types=1);

final class AuthService
{
    public function __construct(private User $users) {}

    /**
     * @return array{ok:bool,error?:string,user?:array}
     */
    public function attempt(string $email, string $password): array
    {
        $config = require BASE_PATH . '/config/config.php';
        $maxAttempts    = (int) (new SystemSetting())->get('login_max_attempts', 5);
        $lockoutMinutes = (int) (new SystemSetting())->get('login_lockout_minutes', 15);

        $user = $this->users->findByEmail($email);
        if (!$user) {
            return ['ok' => false, 'error' => 'Invalid email or password.'];
        }
        if ((int) $user['is_active'] !== 1) {
            return ['ok' => false, 'error' => 'Your account has been deactivated. Contact the administrator.'];
        }
        if ($this->users->isLocked($user)) {
            return ['ok' => false, 'error' => 'Account temporarily locked due to failed login attempts. Try again later.'];
        }
        if (!Security::verifyPassword($password, $user['password_hash'])) {
            $this->users->registerFailedAttempt((int) $user['id'], $maxAttempts, $lockoutMinutes);
            return ['ok' => false, 'error' => 'Invalid email or password.'];
        }

        $this->users->resetFailedAttempts((int) $user['id'], Security::clientIp());
        $user['permissions'] = $this->users->permissionsForRole((int) $user['role_id']);
        return ['ok' => true, 'user' => $user];
    }
}