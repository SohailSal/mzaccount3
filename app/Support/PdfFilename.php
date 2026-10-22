<?php

namespace App\Support;

class PdfFilename
{
    public static function sanitize(string $filename): string
    {
        return str_replace(['/', '\\'], '-', $filename);
    }
}
