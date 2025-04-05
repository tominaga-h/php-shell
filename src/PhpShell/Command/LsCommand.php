<?php

namespace Hytmng\PhpShell\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\Table;
use Hytmng\PhpShell\Command\CommandResults;

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

		// ディレクトリ取得処理
		$directory = getcwd();
		if ($input->getArgument('path') !== '.') {
			$directory = $input->getArgument('path');
		}
		if (!\is_dir($directory)) {
			throw new \InvalidArgumentException("The directory not found: {$directory}");
		}

		// ファイル取得処理
		$files = scandir($directory);
		if ($files === false) {
			throw new \RuntimeException("Failed to read the directory: {$directory}");
		}

		$table = new Table($output);
		$table->setHeaders(['Permissions', 'Size', 'User', 'Group', 'Date', 'Name']);

		foreach ($files as $file) {

			$fullPath = $directory . DIRECTORY_SEPARATOR . $file;
			$styledName = $this->styledName($file, $directory, $output->isDecorated());

			if (!$isAll && $file[0] === '.') {
				continue;
			}

			if (!$isLong) {
				$output->write("{$styledName}    ");
				continue;
			}

			$table->addRow([
				$this->formatPermissions($fullPath, $output),
				$this->formatSize($fullPath),
				posix_getpwuid(fileowner($fullPath))['name'] ?? fileowner($fullPath),
				posix_getgrgid(filegroup($fullPath))['name'] ?? filegroup($fullPath),
				date('Y-m-d H:i:s', filemtime($fullPath)),
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

	private function formatPermissions(string $path, OutputInterface $output): string
    {
        $perms = fileperms($path);

        $types = [
            0xC000 => 's', // socket
            0xA000 => 'l', // symbolic link
            0x8000 => '-', // regular
            0x6000 => 'b', // block special
            0x4000 => 'd', // directory
            0x2000 => 'c', // character special
            0x1000 => 'p', // FIFO pipe
        ];

		$styleMap = [
			'-' => 'gray',
			'l' => 'yellow',
			'd' => 'blue',
			'r' => 'yellow',
			'w' => 'red',
			'x' => 'green',
		];

        $type = $types[$perms & 0xF000] ?? 'u';
		$style = $this->getStyleFn($styleMap, $output->isDecorated());

        $rwx = '';

		$rwx .= ($perms & 0x0100) ? $style('r') : $style('-'); // Owner read
		$rwx .= ($perms & 0x0080) ? $style('w') : $style('-'); // Owner write
		$rwx .= ($perms & 0x0040) ? $style('x') : $style('-'); // Owner exec

		$rwx .= ($perms & 0x0020) ? $style('r') : $style('-'); // Group read
		$rwx .= ($perms & 0x0010) ? $style('w') : $style('-'); // Group write
		$rwx .= ($perms & 0x0008) ? $style('x') : $style('-'); // Group exec

		$rwx .= ($perms & 0x0004) ? $style('r') : $style('-'); // Other read
		$rwx .= ($perms & 0x0002) ? $style('w') : $style('-'); // Other write
		$rwx .= ($perms & 0x0001) ? $style('x') : $style('-'); // Other exec

        return $style($type) . $rwx;
    }

    private function formatSize(string $path): string
    {
        $size = filesize($path);
        if ($size === false) return '0';

        $units = [' B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $size >= 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        return sprintf('%6.1f%s', $size, $units[$i]);
    }

	private function getStyleFn(array $styleMap, bool $decorated): \Closure
	{
		return function (string $target) use ($styleMap, $decorated) {
			if (!$decorated) {
				return $target;
			}
			return "<fg={$styleMap[$target]}>{$target}</>";
		};
	}

	private function styledName(string $file, string $directory, bool $decorated): string
	{
		if (!$decorated) {
			return $file;
		}

		$fullPath = $directory . DIRECTORY_SEPARATOR . $file;

		if (is_dir($fullPath)) {
			return "<fg=blue>{$file}/</>";
		}
		if (is_link($fullPath)) {
			return "<fg=yellow>{$file}@</>";
		}

		return $file;
	}

}
