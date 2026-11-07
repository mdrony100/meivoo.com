<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Schema;

/**
 * Mirror a pair of legacy/current columns when both exist, and drop the
 * attribute when the column is absent so a save does not reference it.
 */
trait GuardsMissingColumns
{
    /**
     * @param  array<int, array{0: string, 1: string}>  $pairs
     */
    protected function mirrorColumns(array $pairs): void
    {
        $columns = array_fill_keys(Schema::getColumnListing($this->getTable()), true);

        foreach ($pairs as [$left, $right]) {
            $hasLeft = isset($columns[$left]);
            $hasRight = isset($columns[$right]);

            if (! $hasLeft) {
                $this->offsetUnset($left);
            }

            if (! $hasRight) {
                $this->offsetUnset($right);
            }

            if (! $hasLeft || ! $hasRight) {
                continue;
            }

            $this->{$left} = $this->{$left} ?: $this->{$right};
            $this->{$right} = $this->{$right} ?: $this->{$left};
        }
    }
}
