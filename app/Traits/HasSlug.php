<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    /**
     * Boot the trait.
     */
    protected static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = $model->generateSlug();
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty($model->getSlugSourceColumn()) && empty($model->slug)) {
                $model->slug = $model->generateSlug();
            }
        });
    }

    /**
     * Generate a unique slug.
     */
    public function generateSlug(): string
    {
        $slug = Str::slug($this->{$this->getSlugSourceColumn()});
        $originalSlug = $slug;
        $count = 1;

        while ($this->slugExists($slug)) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    /**
     * Check if slug exists.
     */
    protected function slugExists(string $slug): bool
    {
        $query = static::where('slug', $slug);

        if ($this->exists) {
            $query->where($this->getKeyName(), '!=', $this->getKey());
        }

        return $query->exists();
    }

    /**
     * Get the column name to generate slug from.
     */
    protected function getSlugSourceColumn(): string
    {
        return $this->slugSourceColumn ?? 'title';
    }

    /**
     * Find model by slug.
     */
    public static function findBySlug(string $slug)
    {
        return static::where('slug', $slug)->first();
    }

    /**
     * Find model by slug or fail.
     */
    public static function findBySlugOrFail(string $slug)
    {
        return static::where('slug', $slug)->firstOrFail();
    }
}
