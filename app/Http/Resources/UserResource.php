<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->when($this->shouldShowEmail($request), $this->email),
            'role' => $this->role,
            'is_active' => $this->is_active,
            'is_verified' => $this->is_verified,
            'avatar_url' => $this->avatar ? asset('storage/' . $this->avatar) : $this->getGravatarUrl(),
            'created_at' => $this->created_at?->toIso8601String(),
            'last_login_at' => $this->when($this->isCurrentUser($request), $this->last_login_at?->toIso8601String()),
        ];
    }

    /**
     * Determine if email should be shown.
     */
    private function shouldShowEmail(Request $request): bool
    {
        return $this->isCurrentUser($request) || $request->user()?->isAdmin();
    }

    /**
     * Check if this is the current user.
     */
    private function isCurrentUser(Request $request): bool
    {
        return $request->user()?->id === $this->id;
    }

    /**
     * Get Gravatar URL for the user.
     */
    private function getGravatarUrl(): string
    {
        $hash = md5(strtolower(trim($this->email)));
        return "https://www.gravatar.com/avatar/{$hash}?d=mp&s=200";
    }
}
