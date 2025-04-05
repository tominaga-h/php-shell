<?php

namespace Hytmng\PhpShell\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Hytmng\PhpShell\Command\CommandResults;

class CdCommand extends Command
{
	protected function configure(): void
	{
		$this
			->setName('cd')
			->setDescription('change directory')
			->addArgument('directory', InputArgument::REQUIRED, 'directory to change')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$directory = $input->getArgument('directory');
		if (\is_dir($directory)) {
			chdir($directory);
		} else {
			throw new \RuntimeException("Directory is not found: {$directory}");
		}
		return CommandResults::SUCCESS;
	}
}
