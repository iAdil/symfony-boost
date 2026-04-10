<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install;

use IAdil\SymfonyBoostBundle\Install\Agent\AbstractAgent;

class GuidelineWriter
{
    /**
     * @param string[] $guidelines
     */
    public function write(AbstractAgent $agent, array $guidelines): void
    {
        if (!$agent->supportsGuidelines()) {
            return;
        }

        $path = $agent->guidelinesPath();

        if ($path === '') {
            return;
        }

        $content = implode("\n\n---\n\n", $guidelines);
        $content = $agent->transformGuidelines($content);
        $content = MarkdownFormatter::normalize($content);

        if ($agent->frontmatter()) {
            $content = "---\n---\n\n".$content;
        }

        $dir = \dirname($path);

        if ($dir !== '.' && !is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($path, $content);
    }
}
