<?php
declare(strict_types=1);

final class AuthController
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            Response::redirect('/dashboard');
        }
        View::render('auth/login', ['title' => 'Sign In'], 'blank');
    }

    public function login(): void
    {
        CSRF::verifyRequest();

        $email = Input::str('email');
        $password = (string) Input::post('password', '');

        $validator = (new Validator(['email' => $email, 'password' => $password]))
            ->required('email', 'Email')
            ->email('email', 'Email')
            ->required('password', 'Password');

        if ($validator->fails()) {
            Flash::error($validator->firstError());
            Response::redirect('/login');
        }

        $service = new AuthService(new User());
        $result = $service->attempt($email, $password);
        if (!$result['ok']) {
            Flash::error($result['error']);
            Response::redirect('/login');
        }

        Auth::login($result['user']);
        Audit::log('auth.login', 'auth', (string) $result['user']['id']);
        Flash::success('Welcome back, ' . $result['user']['full_name'] . '!');
        Response::redirect('/dashboard');
    }

    public function logout(): void
    {
        CSRF::verifyRequest();
        if (Auth::check()) {
            Audit::log('auth.logout', 'auth', (string) Auth::id());
        }
        Auth::logout();
        Flash::success('You have been signed out.');
        Response::redirect('/login');
    }
}