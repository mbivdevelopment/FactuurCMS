<?php

declare(strict_types=1);

namespace App\Core;

use App\Middleware\Throttle;
use App\Models\User;

final class Auth
{
    private static ?User $user = null;
    private static bool $resolved = false;

    public static function user(): ?User
    {
        if (self::$resolved) {
            return self::$user;
        }
        self::$resolved = true;
        $id = (int) Session::get('userId', 0);
        self::$user = $id > 0 ? User::find($id) : null;
        if (self::$user !== null && (int) self::$user->Actief !== 1) {
            self::logout();
        }

        return self::$user;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function id(): int
    {
        return self::user()?->id() ?? 0;
    }

    public static function login(User $user): void
    {
        Session::regenerate();
        Session::set('userId', $user->id());
        self::$user = $user;
        self::$resolved = true;
        $user->LaatsteLogin = now();
        $user->save();
        Log::db('login', (string) $user->Email, $user->id());
        Throttle::clear('login');
    }

    /** Beheerder/medewerker bekijkt het klantportaal als klant; terugkeren via stopImpersonating(). */
    public static function impersonate(User $staff, User $customer): void
    {
        Session::regenerate();
        Session::set('userId', $customer->id());
        Session::set('impersonatorId', $staff->id());
        self::$user = $customer;
        self::$resolved = true;
        Log::db('inloggen-als', (string) $customer->Email, $staff->id());
    }

    public static function impersonator(): ?User
    {
        $id = (int) Session::get('impersonatorId', 0);

        return $id > 0 ? User::find($id) : null;
    }

    public static function stopImpersonating(): ?User
    {
        $staff = self::impersonator();
        if ($staff === null || !$staff->isStaff()) {
            self::logout();

            return null;
        }
        Session::regenerate();
        Session::forget('impersonatorId');
        Session::set('userId', $staff->id());
        self::$user = $staff;
        self::$resolved = true;

        return $staff;
    }

    public static function logout(): void
    {
        self::$user = null;
        self::$resolved = true;
        Session::destroy();
    }

    public static function attempt(string $email, string $password): ?User
    {
        $user = User::firstWhere(['Email' => mb_strtolower(trim($email))]);
        if ($user === null || !password_verify($password, (string) $user->Wachtwoord)) {
            return null;
        }

        return $user;
    }

    public static function hash(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function homeFor(User $user): string
    {
        return $user->isStaff() ? '/beheer' : '/klant';
    }
}
