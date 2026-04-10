<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Console;

use IAdil\SymfonyBoostBundle\Install\Agent\AbstractAgent;
use IAdil\SymfonyBoostBundle\Install\AgentsDetector;
use IAdil\SymfonyBoostBundle\Install\GuidelineComposer;
use IAdil\SymfonyBoostBundle\Install\GuidelineWriter;
use IAdil\SymfonyBoostBundle\Install\McpWriter;
use IAdil\SymfonyBoostBundle\Install\SkillComposer;
use IAdil\SymfonyBoostBundle\Install\SkillWriter;
use IAdil\SymfonyBoostBundle\Install\ThirdPartyPackage;
use IAdil\SymfonyBoostBundle\Support\Config;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'boost:install',
    description: 'Install Symfony Boost AI development assistant configuration',
)]
class InstallCommand extends Command
{
    /** @var AbstractAgent[] */
    private array $selectedAgents = [];

    /** @var string[] */
    private array $selectedFeatures = [];

    /** @var string[] */
    private array $selectedPackages = [];

    public function __construct(
        private readonly string $projectDir,
        private readonly AgentsDetector $agentsDetector,
        private readonly Config $config,
        private readonly GuidelineComposer $guidelineComposer,
        private readonly GuidelineWriter $guidelineWriter,
        private readonly SkillComposer $skillComposer,
        private readonly SkillWriter $skillWriter,
        private readonly McpWriter $mcpWriter,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('guidelines', null, InputOption::VALUE_NONE, 'Install AI guidelines')
            ->addOption('skills', null, InputOption::VALUE_NONE, 'Install agent skills')
            ->addOption('mcp', null, InputOption::VALUE_NONE, 'Install MCP server configuration')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Symfony Boost - Install');
        $io->text('Accelerate AI-assisted development for your Symfony application.');
        $io->newLine();

        $this->discoverEnvironment($io);
        $this->collectPreferences($input, $io);
        $this->performInstallation($io);
        $this->storeConfig();

        $io->success('Boost installed successfully! Enjoy the boost.');

        return self::SUCCESS;
    }

    private function discoverEnvironment(SymfonyStyle $io): void
    {
        $systemAgents = $this->agentsDetector->discoverSystemInstalledAgents();
        $projectAgents = $this->agentsDetector->discoverProjectInstalledAgents($this->projectDir);

        if (!empty($systemAgents)) {
            $io->text('Detected system-wide agents: '.implode(', ', $systemAgents));
        }

        if (!empty($projectAgents)) {
            $io->text('Detected project agents: '.implode(', ', $projectAgents));
        }
    }

    private function collectPreferences(InputInterface $input, SymfonyStyle $io): void
    {
        if ($input->getOption('guidelines') || $input->getOption('skills') || $input->getOption('mcp')) {
            $this->selectedFeatures = array_filter([
                $input->getOption('guidelines') ? 'guidelines' : null,
                $input->getOption('skills') ? 'skills' : null,
                $input->getOption('mcp') ? 'mcp' : null,
            ]);
        } else {
            $question = new ChoiceQuestion(
                'Which features would you like to install? (comma-separated)',
                ['guidelines', 'skills', 'mcp'],
                '0,1,2'
            );
            $question->setMultiselect(true);
            $this->selectedFeatures = $io->askQuestion($question);
        }

        if (\in_array('guidelines', $this->selectedFeatures, true) || \in_array('skills', $this->selectedFeatures, true)) {
            $packages = ThirdPartyPackage::discoverForSymfony($this->projectDir);

            if (!empty($packages)) {
                $packageNames = array_keys($packages);
                $question = new ChoiceQuestion(
                    'Select third-party packages to include (comma-separated, or press Enter for all):',
                    $packageNames,
                    implode(',', range(0, \count($packageNames) - 1))
                );
                $question->setMultiselect(true);
                $this->selectedPackages = $io->askQuestion($question);
            }
        }

        $agents = $this->agentsDetector->getAllAvailableAgents();

        if (!empty($agents)) {
            $agentNames = array_map(fn (AbstractAgent $agent) => $agent->displayName(), $agents);
            $question = new ChoiceQuestion(
                'Select AI agents to configure (comma-separated):',
                $agentNames,
                '0'
            );
            $question->setMultiselect(true);
            $selectedNames = $io->askQuestion($question);

            $this->selectedAgents = array_filter(
                $agents,
                fn (AbstractAgent $agent) => \in_array($agent->displayName(), $selectedNames, true)
            );
        }
    }

    private function performInstallation(SymfonyStyle $io): void
    {
        if (\in_array('guidelines', $this->selectedFeatures, true)) {
            $io->section('Installing Guidelines');
            $guidelines = $this->guidelineComposer->compose($this->selectedPackages);

            foreach ($this->selectedAgents as $agent) {
                $this->guidelineWriter->write($agent, $guidelines);
                $io->text('  Written guidelines for '.$agent->displayName());
            }
        }

        if (\in_array('skills', $this->selectedFeatures, true)) {
            $io->section('Installing Skills');
            $skills = $this->skillComposer->compose($this->selectedPackages);

            foreach ($this->selectedAgents as $agent) {
                $this->skillWriter->write($agent, $skills);
                $io->text('  Written skills for '.$agent->displayName());
            }
        }

        if (\in_array('mcp', $this->selectedFeatures, true)) {
            $io->section('Installing MCP Server Configuration');

            foreach ($this->selectedAgents as $agent) {
                $this->mcpWriter->write($agent);
                $io->text('  Written MCP config for '.$agent->displayName());
            }
        }
    }

    private function storeConfig(): void
    {
        $this->config->set('agents', array_map(
            fn (AbstractAgent $agent) => $agent->name(),
            $this->selectedAgents
        ));
        $this->config->set('features', $this->selectedFeatures);
        $this->config->set('packages', $this->selectedPackages);
    }
}
