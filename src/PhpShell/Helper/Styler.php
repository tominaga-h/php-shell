<?php

namespace Hytmng\PhpShell\Helper;

use Hytmng\PhpShell\Helper\Style\StringStyleMapper;
use Hytmng\PhpShell\Helper\FileSystem\File;

class Styler
{
	private bool $decorated;

	public function __construct(bool $decorated = false)
	{
		$this->decorated = $decorated;
	}

	public function stylePermissionString(string $perm): string
	{
		if ($this->decorated) {
			return $perm;
		}

		$map = [
			'-' => 'gray',
			'l' => 'yellow',
			'd' => 'blue',
			'r' => 'yellow',
			'w' => 'red',
			'x' => 'green',
		];

		$mapper = new StringStyleMapper($map);
		$styled = '';
		foreach (\str_split($perm) as $char) {
			$styled .= $mapper->apply($char);
		}
		return $styled;
	}

	public function styleFileName(File $file): string
	{
		$fileName = $file->getFileName();

		if ($this->decorated) {
			return $fileName;
		}

		if ($file->isDirectory()) {
			return "<fg=blue>{$fileName}/</>";
		}

		if ($file->isLink()) {
			return "<fg=yellow>{$fileName}@</>";
		}

		if ($file->isExecutable()) {
			return "<fg=green>{$fileName}*</>";
		}

		return $fileName;
	}
}
