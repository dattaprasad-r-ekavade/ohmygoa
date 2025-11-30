<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class SlugHelper
{
    /**
     * Generate unique slug for a model.
     */
    public static function generate(string $title, string $model, int $id = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (self::slugExists($slug, $model, $id)) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Check if slug exists.
     */
    private static function slugExists(string $slug, string $model, int $id = null): bool
    {
        $query = $model::where('slug', $slug);

        if ($id) {
            $query->where('id', '!=', $id);
        }

        return $query->exists();
    }

    /**
     * Clean slug from special characters.
     */
    public static function clean(string $slug): string
    {
        $slug = preg_replace('/[^a-z0-9-]/', '', strtolower($slug));
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }
}
