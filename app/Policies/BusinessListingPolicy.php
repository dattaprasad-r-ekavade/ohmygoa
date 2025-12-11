<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\BusinessListing;
use App\Models\User;

class BusinessListingPolicy
{
    /**
     * Determine if the user can view any listings.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the listing.
     */
    public function view(?User $user, BusinessListing $listing): bool
    {
        return $listing->is_active || ($user && $this->isOwnerOrAdmin($user, $listing));
    }

    /**
     * Determine if the user can create listings.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(UserRole::BUSINESS) || $user->hasRole(UserRole::ADMIN);
    }

    /**
     * Determine if the user can update the listing.
     */
    public function update(User $user, BusinessListing $listing): bool
    {
        return $this->isOwnerOrAdmin($user, $listing);
    }

    /**
     * Determine if the user can delete the listing.
     */
    public function delete(User $user, BusinessListing $listing): bool
    {
        return $this->isOwnerOrAdmin($user, $listing);
    }

    /**
     * Determine if the user can restore the listing.
     */
    public function restore(User $user, BusinessListing $listing): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    /**
     * Determine if the user can permanently delete the listing.
     */
    public function forceDelete(User $user, BusinessListing $listing): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    /**
     * Check if user is the owner or admin.
     */
    protected function isOwnerOrAdmin(User $user, BusinessListing $listing): bool
    {
        return $user->hasRole(UserRole::ADMIN) || $listing->user_id === $user->id;
    }
}
