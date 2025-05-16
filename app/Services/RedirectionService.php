<?php


namespace App\Services;

class RedirectionService
{
    public function getRedirectRouteForUser($user)
    {
        if ($user->hasRole('admin')) {
            return 'admin.dashboard';
        }

        if ($user->hasRole('manager')) {
            return 'manager.dashboard';
        }
        return 'user.dashboard';
    }
}
