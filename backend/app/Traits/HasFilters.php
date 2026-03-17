<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasFilters
{
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        foreach ($filters as $filter => $value) {
            if ($value !== null && method_exists($this, "filterBy" . ucfirst($filter))) {
                $this->{"filterBy" . ucfirst($filter)}($query, $value);
            }
        }

        return $query;
    }
}
