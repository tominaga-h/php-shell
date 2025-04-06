<?php

namespace Hytmng\PhpShell\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\Table;
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

		$table = new Table($output);
		$table->setHeaders(['Name', 'Description']);

		foreach ($commands as $command) {
			$table->addRow([
				$command->getName(),
				$command->getDescription(),
			]);
		}

		$table->render();
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
