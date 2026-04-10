<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Console;

use IAdil\SymfonyBoostBundle\Install\Agent\AbstractAgent;
use IAdil\SymfonyBoostBundle\Install\AgentsDetector;
use IAdil\SymfonyBoostBundle\Install\GuidelineComposer;
use IAdil\SymfonyBoostBundle\Install\GuidelineWriter;
use IAdil\SymfonyBoostBundle\Install\SkillComposer;
use IAdil\SymfonyBoostBundle\Install\SkillWriter;
use IAdil\SymfonyBoostBundle\Support\Config;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'boost:update',
    description: 'Update Boost guidelines and skills for configured agents',
)]
class UpdateCommand extends Command
{
    public function __construct(
        private readonly string $projectDir,
        private readonly Config $config,
        private readonly AgentsDetector $agentsDetector,
        private readonly GuidelineComposer $guidelineComposer,
        private readonly GuidelineWriter $guidelineWriter,
        private readonly SkillComposer $skillComposer,
        private readonly SkillWriter $skillWriter,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Symfony Boost - Update');

        $configuredAgentNames = $this->config->get('agents', []);
        $features = $this->config->get('features', []);
        $packages = $this->config->get('packages', []);

        if (empty($configuredAgentNames)) {
            $io->warning('No agents configured. Run boost:install first.');

            return self::FAILURE;
        }

        $allAgents = $this->agentsDetector->getAllAvailableAgents();
        $agents = array_filter(
            $allAgents,
            fn (AbstractAgent $agent) => \in_array($agent->name(), $configuredAgentNames, true)
        );

        if (empty($agents)) {
            $io->warning('No matching agents found for configured names: '.implode(', ', $configuredAgentNames));

            return self::FAILURE;
        }

        if (\in_array('guidelines', $features, true)) {
            $io->section('Updating Guidelines');
            $guidelines = $this->guidelineComposer->compose($packages);

            foreach ($agents as $agent) {
                $this->guidelineWriter->write($agent, $guidelines);
                $io->text('  Updated guidelines for '.$agent->displayName());
            }
        }

        if (\in_array('skills', $features, true)) {
            $io->section('Updating Skills');
            $skills = $this->skillComposer->compose($packages);

            foreach ($agents as $agent) {
                $this->skillWriter->write($agent, $skills);
                $io->text('  Updated skills for '.$agent->displayName());
            }
        }

        $io->success('Boost updated successfully!');

        return self::SUCCESS;
    }
}
