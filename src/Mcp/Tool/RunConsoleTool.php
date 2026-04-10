<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Mcp\Tool;

use Mcp\Capability\Attribute\McpTool;
use Symfony\Component\Process\Process;

#[McpTool(name: 'run-console')]
class RunConsoleTool
{
    private const ALLOWED_COMMANDS = [
        'debug:router',
        'debug:container',
        'debug:config',
        'debug:autowiring',
        'debug:event-dispatcher',
        'debug:translation',
        'debug:twig',
        'debug:validator',
        'debug:form',
        'debug:messenger',
        'doctrine:schema:validate',
        'doctrine:mapping:info',
        'doctrine:query:dql',
        'doctrine:migrations:status',
        'doctrine:migrations:list',
        'lint:twig',
        'lint:yaml',
        'lint:container',
        'lint:xliff',
        'router:match',
        'about',
        'list',
        'cache:pool:list',
        'secrets:list',
    ];

    private const BLOCKED_COMMANDS = [
        'cache:clear',
        'cache:warmup',
        'doctrine:database:drop',
        'doctrine:database:create',
        'doctrine:schema:drop',
        'doctrine:schema:update',
        'doctrine:schema:create',
        'doctrine:migrations:migrate',
        'doctrine:migrations:execute',
        'doctrine:fixtures:load',
        'secrets:set',
        'secrets:remove',
        'secrets:decrypt-to-local',
        'messenger:consume',
        'server:start',
        'server:run',
        'asset-map:compile',
        'assets:install',
    ];

    public function __invoke(string $command, ?string $arguments = null): string
    {
        $commandName = trim(explode(' ', trim($command))[0]);

        // Check blocked first
        foreach (self::BLOCKED_COMMANDS as $blocked) {
            if ($commandName === $blocked) {
                return "Error: Command '{$commandName}' is blocked for safety. It could modify the application state.";
            }
        }

        // Check allowed
        $isAllowed = false;

        foreach (self::ALLOWED_COMMANDS as $allowed) {
            if ($commandName === $allowed || str_starts_with($commandName, 'debug:') || str_starts_with($commandName, 'lint:')) {
                $isAllowed = true;

                break;
            }
        }

        if (!$isAllowed) {
            return "Error: Command '{$commandName}' is not in the allowed list. Allowed commands: ".implode(', ', self::ALLOWED_COMMANDS);
        }

        $fullCommand = ['php', 'bin/console', $command];

        if ($arguments !== null && $arguments !== '') {
            $fullCommand = array_merge($fullCommand, explode(' ', $arguments));
        }

        $fullCommand[] = '--no-interaction';
        $fullCommand[] = '--ansi';

        try {
            $process = new Process($fullCommand);
            $process->setTimeout(30);
            $process->run();

            $output = $process->getOutput();
            $errorOutput = $process->getErrorOutput();

            if (!$process->isSuccessful()) {
                return "Command failed (exit code {$process->getExitCode()}):\n{$errorOutput}\n{$output}";
            }

            return $output ?: 'Command completed successfully with no output.';
        } catch (\Throwable $e) {
            return 'Error: '.$e->getMessage();
        }
    }
}
