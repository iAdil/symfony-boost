<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'boost:mcp',
    description: 'Starts Symfony Boost MCP server (usually from mcp.json)',
)]
class StartCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $mcpCommand = $this->getApplication()?->find('mcp:server');

        if ($mcpCommand === null) {
            $output->writeln('<error>MCP server command not found. Is symfony/mcp-bundle installed?</error>');

            return self::FAILURE;
        }

        return $mcpCommand->run(new ArrayInput([]), $output);
    }
}
