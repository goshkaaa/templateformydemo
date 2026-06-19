<?php
declare(strict_types=1);

final class Auth
{
    private function __construct()
    {
    }

    public static function attempt(string $login, string $password): ?string
    {
        if ($login === ADMIN_LOGIN && $password === ADMIN_PASSWORD) {
            $_SESSION['is_admin'] = true;
            unset($_SESSION['user_id']);
            session_regenerate_id(true);
            return 'admin';
        }

        $stmt = Database::connection()->prepare('SELECT id, password_hash FROM users WHERE login = ?');
        $stmt->execute([$login]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return null;
        }

        $_SESSION['user_id'] = (int) $user['id'];
        unset($_SESSION['is_admin']);
        session_regenerate_id(true);
        return 'user';
    }

    public static function currentUser(): ?array
    {
        if (empty($_SESSION['user_id'])) {
            return null;
        }

        $stmt = Database::connection()->prepare(
            'SELECT id, login, full_name, phone, email FROM users WHERE id = ?'
        );
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public static function requireUser(): array
    {
        $user = self::currentUser();

        if (!$user) {
            redirect('login.php');
        }

        return $user;
    }

    public static function isAdmin(): bool
    {
        return !empty($_SESSION['is_admin']);
    }

    public static function requireAdmin(): void
    {
        if (!self::isAdmin()) {
            redirect('login.php');
        }
    }

    public static function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();
        session_start();
        session_regenerate_id(true);
    }
}
