<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Console;

use IAdil\SymfonyBoostBundle\Install\Agent\AbstractAgent;
use IAdil\SymfonyBoostBundle\Install\AgentsDetector;
use IAdil\SymfonyBoostBundle\Install\SkillWriter;
use IAdil\SymfonyBoostBundle\Skills\Remote\GitHubRepository;
use IAdil\SymfonyBoostBundle\Skills\Remote\GitHubSkillProvider;
use IAdil\SymfonyBoostBundle\Skills\Remote\SkillAuditor;
use IAdil\SymfonyBoostBundle\Support\Config;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'boost:add-skill',
    description: 'Install a remote skill from GitHub',
)]
class AddSkillCommand extends Command
{
    public function __construct(
        private readonly string $projectDir,
        private readonly Config $config,
        private readonly AgentsDetector $agentsDetector,
        private readonly SkillWriter $skillWriter,
        private readonly GitHubSkillProvider $gitHubSkillProvider,
        private readonly SkillAuditor $skillAuditor,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('repository', InputArgument::REQUIRED, 'GitHub repository (owner/repo or URL)')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite existing skills')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $repoInput = $input->getArgument('repository');
        $force = $input->getOption('force');

        $io->title('Symfony Boost - Add Skill');

        try {
            $repo = new GitHubRepository($repoInput);
        } catch (\InvalidArgumentException $e) {
            $io->error('Invalid repository: '.$e->getMessage());

            return self::FAILURE;
        }

        $io->text("Fetching skills from {$repo->owner()}/{$repo->repo()}...");

        try {
            $skills = $this->gitHubSkillProvider->fetchSkills($repo);
        } catch (\Throwable $e) {
            $io->error('Failed to fetch skills: '.$e->getMessage());

            return self::FAILURE;
        }

        if (empty($skills)) {
            $io->warning('No skills found in the repository.');

            return self::FAILURE;
        }

        $io->text(\count($skills).' skill(s) found.');

        // Audit
        $io->text('Running security audit...');

        try {
            $auditResult = $this->skillAuditor->audit($repo);

            if ($auditResult !== null) {
                $io->text("Risk level: {$auditResult->risk->label()}");

                if (!empty($auditResult->alerts)) {
                    $io->warning('Security alerts: '.implode(', ', $auditResult->alerts));
                }
            }
        } catch (\Throwable) {
            $io->text('Audit service unavailable, proceeding...');
        }

        // Install to agents
        $configuredAgentNames = $this->config->get('agents', []);
        $allAgents = $this->agentsDetector->getAllAvailableAgents();
        $agents = array_filter(
            $allAgents,
            fn (AbstractAgent $agent) => \in_array($agent->name(), $configuredAgentNames, true)
        );

        if (empty($agents)) {
            $io->warning('No agents configured. Run boost:install first.');

            return self::FAILURE;
        }

        foreach ($agents as $agent) {
            foreach ($skills as $skill) {
                $this->skillWriter->writeRemoteSkill($agent, $skill, $force);
                $io->text("  Installed skill '{$skill->name}' for {$agent->displayName()}");
            }
        }

        $io->success('Skill(s) installed successfully!');

        return self::SUCCESS;
    }
}
