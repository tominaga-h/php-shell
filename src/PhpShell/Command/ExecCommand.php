<?php

namespace Hytmng\PhpShell\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Hytmng\PhpShell\Command\Command;
use Hytmng\PhpShell\Command\CommandResults;

class ExecCommand extends Command
{
	protected function configure(): void
	{
		$this
			->setName('exec')
			->setDescription('execute an external command like `/bin/ls`')
			->setAsExternalCommand('cmd')
			->addArgument(
				'cmd',
				InputArgument::REQUIRED | InputArgument::IS_ARRAY,
				'A command to execute like `/bin/ls -la`'
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$command = $input->getArgument('cmd');
		// 最初の引数はコマンド名なので、1以降の引数を取得
		$process = new Process(\array_slice($command, 1));

		if (posix_isatty(STDIN)) {
			$process->setTty(true);
		}

		$process->run(function ($type, $buffer) use ($output) {
			$output->write($buffer);
		});
		$output->write("\n");

		if (!$process->isSuccessful()) {
			throw new ProcessFailedException($process);
		}

		return CommandResults::SUCCESS;
	}
}
