<?php

namespace Hytmng\PhpShell\Helper;

class Formatter
{
	public function __construct()
	{

	}

	public function formatPermission(int $perms): string
	{
		$formatted = '';

		$types = [
            0xC000 => 's', // socket
            0xA000 => 'l', // symbolic link
            0x8000 => '-', // regular
            0x6000 => 'b', // block special
            0x4000 => 'd', // directory
            0x2000 => 'c', // character special
            0x1000 => 'p', // FIFO pipe
        ];

		$data = [
			'owner' => [
				'read'  => 0x0100,
				'write' => 0x0080,
				'exec'  => 0x0040,
			],
			'group' => [
				'read'  => 0x0020,
				'write' => 0x0010,
				'exec'  => 0x0008,
			],
			'other' => [
				'read'  => 0x0004,
				'write' => 0x0002,
				'exec'  => 0x0001,
			],
		];

		$formatted .= $types[$perms & 0xF000] ?? 'u';

		foreach ($data as $type => $values) {
			foreach ($values as $head => $value) {
				$symbol = match ($head) {
					'read'  => 'r',
					'write' => 'w',
					'exec'  => 'x',
				};

				$formatted .= ($perms & $value) ? $symbol : '-';
			}
		}

		return $formatted;
	}

	public function formatFileSize(int|false $size): string
	{
        if ($size === false) return '0';

        $units = [' B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $size >= 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        return sprintf('%6.1f%s', $size, $units[$i]);
	}
}
