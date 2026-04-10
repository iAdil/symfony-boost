---
name: create-command
description: Create a Symfony console command with progress bars, locking, signal handling, and styled output
---
# Create Console Command

## Template

```php
<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\SignalableCommandInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Lock\LockFactory;

#[AsCommand(
    name: 'app:{command-name}',
    description: 'Description of what this command does',
)]
final class {Name}Command extends Command implements SignalableCommandInterface
{
    private bool $shouldStop = false;

    public function __construct(
        private readonly LockFactory $lockFactory,
        // Inject services here
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Description')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Run without making changes')
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Max items to process', '100')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');

        // Prevent concurrent execution
        $lock = $this->lockFactory->createLock('app-{command-name}', 3600);

        if (!$lock->acquire()) {
            $io->warning('This command is already running.');

            return Command::FAILURE;
        }

        try {
            $io->title('Processing...');

            $items = $this->getItemsToProcess();
            $io->progressStart(\count($items));

            foreach ($items as $item) {
                if ($this->shouldStop) {
                    $io->warning('Received stop signal, finishing gracefully...');

                    break;
                }

                $this->processItem($item, $dryRun);
                $io->progressAdvance();
            }

            $io->progressFinish();

            if ($dryRun) {
                $io->note('Dry run — no changes were made.');
            }

            $io->success('Done!');

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        } finally {
            $lock->release();
        }
    }

    public function getSubscribedSignals(): array
    {
        return [\SIGINT, \SIGTERM];
    }

    public function handleSignal(int $signal, int|false $previousExitCode = 0): int|false
    {
        $this->shouldStop = true;

        return false;
    }
}
```

## Rules

- Use `#[AsCommand]` with name and description
- Use `SymfonyStyle` for all output
- Return `Command::SUCCESS` or `Command::FAILURE`
- Use `LockFactory` to prevent concurrent execution
- Implement `SignalableCommandInterface` for graceful shutdown
- Use progress bars for batch operations
- Support `--dry-run` for safe testing
- Use `InputArgument` for required params, `InputOption` for optional
