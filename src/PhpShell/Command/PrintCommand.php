<?php

namespace Hytmng\PhpShell\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Hytmng\PhpShell\Command\Command;
use Hytmng\PhpShell\Command\CommandResults;

class PrintCommand extends Command
{
	protected function configure(): void
	{
		$this
			->setName('print')
			->setDescription('print a message')
			->addArgument('message', InputArgument::REQUIRED, 'A message to print')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$message = $input->getArgument('message');

		$output->writeln($message);
		return CommandResults::SUCCESS;
	}
}
