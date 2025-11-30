<?php

namespace App\Traits;

trait Searchable
{
    /**
     * Scope for searching.
     */
    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        $searchableColumns = $this->getSearchableColumns();

        return $query->where(function ($q) use ($search, $searchableColumns) {
            foreach ($searchableColumns as $column) {
                $q->orWhere($column, 'LIKE', "%{$search}%");
            }
        });
    }

    /**
     * Get searchable columns.
     */
    protected function getSearchableColumns(): array
    {
        return $this->searchable ?? ['name', 'title', 'description'];
    }
}
