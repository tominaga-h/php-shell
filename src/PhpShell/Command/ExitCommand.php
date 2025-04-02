<?php

namespace Hytmng\PhpShell\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Hytmng\PhpShell\Command\CommandResults;

class ExitCommand extends Command
{
	protected function configure(): void
	{
		$this
			->setName('exit')
			->setDescription('exit the shell')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		return CommandResults::EXIT;
	}
}
