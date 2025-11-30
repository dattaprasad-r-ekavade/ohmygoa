<?php

namespace App\View\Composers;

use App\Models\Category;
use Illuminate\View\View;

class CategoryComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $categories = cache()->remember('global_categories', 3600, function () {
            return Category::with('children')
                ->whereNull('parent_id')
                ->where('status', 'active')
                ->orderBy('order_number')
                ->get();
        });

        $view->with('globalCategories', $categories);
    }
}
