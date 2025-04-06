<?php

namespace Hytmng\PhpShell\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Hytmng\PhpShell\Command\CommandResults;

class PwdCommand extends Command
{
	protected function configure(): void
	{
		$this->setName('pwd')->setDescription('print working directory');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$output->writeln(getcwd());
		return CommandResults::SUCCESS;
	}
}
