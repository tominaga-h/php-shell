<?php

namespace Hytmng\PhpShell\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Helper;
use Hytmng\PhpShell\Command\CommandResults;

class ListCommand extends Command
{
	protected function configure(): void
	{
		$this
			->setName('list')
			->setDescription('list all commands')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$app = $this->getApplication();
		$commands = \is_null($app) ? [] : $app->all();

		$output->writeln('<comment>Available commands:</comment>');
		$width = $this->getColumnWidth($commands);

		foreach ($commands as $command) {
			$commandName = $command->getName();
			$spacingWidth = $width - Helper::width($commandName);
			$space = str_repeat(' ', $spacingWidth);
			$output->writeln(sprintf('  <info>%s</info>%s%s', $commandName, $space, $command->getDescription()));
		}
		return CommandResults::SUCCESS;
	}

	/**
     * @param array<Command|string> $commands
     */
    private function getColumnWidth(array $commands): int
    {
        $widths = [];

        foreach ($commands as $command) {
            if ($command instanceof Command) {
                $widths[] = Helper::width($command->getName());
                foreach ($command->getAliases() as $alias) {
                    $widths[] = Helper::width($alias);
                }
            } else {
                $widths[] = Helper::width($command);
            }
        }

        return $widths ? max($widths) + 2 : 0;
    }
}
