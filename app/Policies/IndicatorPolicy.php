<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Indicator;

class IndicatorPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super_admin', 'system_admin', 'qa_admin', 'administration_admin']);
    }

    public function view(User $user, Indicator $indicator): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['super_admin', 'system_admin', 'qa_admin', 'administration_admin']);
    }

    public function update(User $user, Indicator $indicator): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, Indicator $indicator): bool
    {
        return $this->create($user);
    }
}
