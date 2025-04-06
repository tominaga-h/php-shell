<?php

namespace Hytmng\PhpShell\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Hytmng\PhpShell\Command\Command;
use Hytmng\PhpShell\Command\CommandResults;

class GitCommand extends Command
{
	protected function configure(): void
	{
		$this
			->setName('git')
			->setDescription('execute git command')
			->setAsExternalCommand('gitArgs')
			->addArgument(
				'gitArgs',
				InputArgument::REQUIRED | InputArgument::IS_ARRAY,
				'git command arguments'
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$gitArg = $input->getArgument('gitArgs');
		$process = new Process($gitArg);
		$process->run(function ($type, $buffer) use ($output) {
			$output->write($buffer);
		});
		$output->write("\n");

		if (!$process->isSuccessful()) {
			return CommandResults::FAILURE;
		}
		return CommandResults::SUCCESS;
	}
}
