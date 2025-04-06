<?php

namespace Hytmng\PhpShell\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\Table;
use Hytmng\PhpShell\Command\Command;
use Hytmng\PhpShell\Command\CommandResults;
use Hytmng\PhpShell\Helper\Styler;
use Hytmng\PhpShell\Helper\Formatter;
use Hytmng\PhpShell\Helper\FileSystem\Directory;


class LsCommand extends Command
{
	protected function configure(): void
	{
		$this
			->setName('ls')
			->setDescription('list directory contents')
			->addArgument('path', InputArgument::OPTIONAL, 'path to list', '.')
			->addOption('all', 'a', InputOption::VALUE_NONE, 'show hidden files')
			->addOption('long', 'l', InputOption::VALUE_NONE, 'show detailed information')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$isAll = $input->getOption('all');
		$isLong = $input->getOption('long');
		$targetPath = $input->getArgument('path');
		$styler = new Styler($output->isDecorated());
		$formatter = new Formatter();

		// ディレクトリ
		if ($targetPath !== '.') {
			$directory = new Directory($targetPath);
		} else {
			$directory = new Directory(getcwd());
		}

		// エラー処理
		if (!$directory->exists()) {
			throw new \InvalidArgumentException("The directory not found: {$directory}");
		}

		// ファイル
		$files = $directory->getFiles();

		$table = new Table($output);
		$table->setHeaders(['Permissions', 'Size', 'User', 'Group', 'Date', 'Name']);

		foreach ($files as $file) {

			$styledName = $styler->styleFileName($file);
			$styledPermission = $styler->stylePermissionString(
				$formatter->formatPermission($file->getPermissions())
			);

			if (!$isAll && $file->isHiddenFile()) {
				continue;
			}

			if (!$isLong) {
				$output->write("{$styledName}    ");
				continue;
			}

			$table->addRow([
				$styledPermission,
				$formatter->formatFileSize($file->getSize()),
				$file->getOwner(),
				$file->getGroup(),
				date('Y-m-d H:i:s', $file->getMTime()),
				$styledName,
			]);
		}

		if ($isLong) {
			$table->render();
		} else {
			$output->write("\n");
		}

		return CommandResults::SUCCESS;
	}

}
