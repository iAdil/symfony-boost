<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install;

class MarkdownFormatter
{
    public static function normalize(string $content): string
    {
        // Normalize line endings
        $content = str_replace("\r\n", "\n", $content);

        // Remove excessive blank lines (3+ becomes 2)
        $content = preg_replace("/\n{3,}/", "\n\n", $content);

        // Ensure headings have a blank line before them
        $content = preg_replace('/([^\n])\n(#{1,6}\s)/', "$1\n\n$2", $content);

        // Ensure single trailing newline
        $content = rtrim($content)."\n";

        return $content;
    }
}
