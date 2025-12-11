<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\ServiceExpert;
use App\Models\User;

class ServiceExpertPolicy
{
    /**
     * Determine if the user can view any service experts.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the service expert.
     */
    public function view(?User $user, ServiceExpert $expert): bool
    {
        return $expert->is_active || ($user && $this->isOwnerOrAdmin($user, $expert));
    }

    /**
     * Determine if the user can create service experts.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(UserRole::BUSINESS) || $user->hasRole(UserRole::ADMIN);
    }

    /**
     * Determine if the user can update the service expert.
     */
    public function update(User $user, ServiceExpert $expert): bool
    {
        return $this->isOwnerOrAdmin($user, $expert);
    }

    /**
     * Determine if the user can delete the service expert.
     */
    public function delete(User $user, ServiceExpert $expert): bool
    {
        return $this->isOwnerOrAdmin($user, $expert);
    }

    /**
     * Determine if the user can restore the service expert.
     */
    public function restore(User $user, ServiceExpert $expert): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    /**
     * Determine if the user can permanently delete the service expert.
     */
    public function forceDelete(User $user, ServiceExpert $expert): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    /**
     * Check if user is the owner or admin.
     */
    protected function isOwnerOrAdmin(User $user, ServiceExpert $expert): bool
    {
        return $user->hasRole(UserRole::ADMIN) || $expert->user_id === $user->id;
    }
}
