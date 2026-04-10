<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\EventListener;

use IAdil\SymfonyBoostBundle\Service\BrowserLogger;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class InjectBrowserLoggerListener
{
    public function __construct(
        private readonly BrowserLogger $browserLogger,
    ) {
    }

    public function __invoke(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();

        if (!$this->shouldInject($request, $response)) {
            return;
        }

        $content = $response->getContent();

        if ($content === false) {
            return;
        }

        $injectedContent = $this->injectScript($content);
        $response->setContent($injectedContent);
    }

    private function shouldInject($request, $response): bool
    {
        if ($request->headers->get('x-livewire-navigate') === '1') {
            return false;
        }

        if ($response instanceof StreamedResponse
            || $response instanceof BinaryFileResponse
            || $response instanceof JsonResponse
            || $response instanceof RedirectResponse
        ) {
            return false;
        }

        $contentType = (string) $response->headers->get('content-type', '');

        if (!str_contains($contentType, 'html')) {
            return false;
        }

        $content = $response->getContent();

        if ($content === false) {
            return false;
        }

        if (!str_contains($content, '<html') && !str_contains($content, '<head')) {
            return false;
        }

        return !str_contains($content, 'browser-logger-active');
    }

    private function injectScript(string $content): string
    {
        $script = BrowserLogger::getScript();

        if (str_contains($content, '</head>')) {
            return str_replace('</head>', $script."\n</head>", $content);
        }

        if (str_contains($content, '</body>')) {
            return str_replace('</body>', $script."\n</body>", $content);
        }

        return $content.$script;
    }
}
