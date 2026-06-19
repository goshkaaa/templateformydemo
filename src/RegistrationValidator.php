<?php
declare(strict_types=1);

final class RegistrationValidator
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function validate(array $values, string $password): array
    {
        $errors = [];

        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,40}$/', $values['login'])) {
            $errors['login'] = 'Логин: латинские буквы и цифры, минимум 6 символов.';
        }
        if ($values['full_name'] === '') {
            $errors['full_name'] = 'Введите ФИО.';
        }
        if (!$this->isValidPhone($values['phone'])) {
            $errors['phone'] = 'Введите телефон: от 10 до 15 цифр.';
        }
        if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Введите корректный e-mail.';
        }
        if (strlen($password) < 8) {
            $errors['password'] = 'Пароль должен быть не короче 8 символов.';
        }

        if (!isset($errors['login'], $errors['email'])) {
            $this->validateUniqueness($values['login'], $values['email'], $errors);
        } elseif (!isset($errors['login'])) {
            $this->validateLoginUniqueness($values['login'], $errors);
        } elseif (!isset($errors['email'])) {
            $this->validateEmailUniqueness($values['email'], $errors);
        }

        return $errors;
    }

    private function isValidPhone(string $phone): bool
    {
        if (!preg_match('/^\+?[0-9\s()\-]+$/', $phone)) {
            return false;
        }

        $digits = preg_replace('/\D/', '', $phone);
        return strlen($digits) >= 10 && strlen($digits) <= 15;
    }

    private function validateUniqueness(string $login, string $email, array &$errors): void
    {
        $stmt = $this->db->prepare('SELECT login, email FROM users WHERE login = ? OR email = ?');
        $stmt->execute([$login, $email]);

        foreach ($stmt->fetchAll() as $user) {
            if (strcasecmp($user['login'], $login) === 0) {
                $errors['login'] = 'Такой логин уже зарегистрирован.';
            }
            if (strcasecmp($user['email'], $email) === 0) {
                $errors['email'] = 'Такой e-mail уже зарегистрирован.';
            }
        }
    }

    private function validateLoginUniqueness(string $login, array &$errors): void
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM users WHERE login = ?');
        $stmt->execute([$login]);
        if ((int) $stmt->fetchColumn() > 0) {
            $errors['login'] = 'Такой логин уже зарегистрирован.';
        }
    }

    private function validateEmailUniqueness(string $email, array &$errors): void
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ((int) $stmt->fetchColumn() > 0) {
            $errors['email'] = 'Такой e-mail уже зарегистрирован.';
        }
    }
}
