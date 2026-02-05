<?php

namespace Edwinekr\OtelElkLaravel\Listeners;

use Edwinekr\OtelElkLaravel\Services\ActivityLogService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;

class AuthActivityListener
{
    public function __construct(private ActivityLogService $activityLog)
    {
    }

    public function handleLogin(Login $event): void
    {
        $this->activityLog->logAuth('login', $event->user->id, [
            'guard' => $event->guard,
            'remember' => $event->remember,
        ]);
    }

    public function handleLogout(Logout $event): void
    {
        $this->activityLog->logAuth('logout', $event->user?->id);
    }

    public function handleFailed(Failed $event): void
    {
        $this->activityLog->logAuth('failed', null, [
            'credentials' => array_keys($event->credentials),
        ]);
    }

    public function handleLockout(Lockout $event): void
    {
        $this->activityLog->logAuth('lockout', null, [
            'ip' => $event->request->ip(),
        ]);
    }

    public function handleRegistered(Registered $event): void
    {
        $this->activityLog->logAuth('registered', $event->user->id);
    }

    public function handlePasswordReset(PasswordReset $event): void
    {
        $this->activityLog->logAuth('password_reset', $event->user->id);
    }
}
