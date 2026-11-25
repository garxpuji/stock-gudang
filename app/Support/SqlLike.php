<?php

namespace App\Support;

class SqlLike
{
    /**
     * Escape SQL LIKE wildcard characters (%, _, \) so user search input is matched literally.
     */
    public static function escape(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }

    /**
     * Wrap an escaped search term with leading and trailing wildcards.
     */
    public static function contains(string $value): string
    {
        return '%'.static::escape($value).'%';
    }
}
