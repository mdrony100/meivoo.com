<?php

namespace App\Database;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Helpers for upgrades that must keep every existing row, primary key, and
 * foreign key. These methods never drop, rename, or replace a table.
 */
class SafeSchema
{
    /**
     * Add columns that are not already present.
     *
     * Each closure must add a nullable column, or a column with a default,
     * so a populated table stays valid. Existing column types are left alone.
     *
     * @param  array<string, Closure(Blueprint): void>  $columns
     */
    public static function addMissingColumns(string $table, array $columns): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $missing = [];

        foreach ($columns as $name => $definition) {
            if (! Schema::hasColumn($table, $name)) {
                $missing[$name] = $definition;
            }
        }

        foreach ($missing as $definition) {
            Schema::table($table, function (Blueprint $blueprint) use ($definition) {
                $definition($blueprint);
            });
        }
    }
}
