<?php

namespace Hytmng\PhpShell\Helper\FileSystem;

use Hytmng\PhpShell\Helper\FileSystem\File;

class Directory
{
	private string $path;

	public function __construct(string $path)
	{
		$this->path = rtrim($path, DIRECTORY_SEPARATOR);
	}

	/**
	 * ディレクトリのパスを取得する
	 *
	 * @return string
	 */
	public function getPath(): string
	{
		return $this->path;
	}

	/**
	 * ディレクトリ名を取得する
	 */
	public function getDirName(): string
	{
		return basename($this->path);
	}

	/**
	 * ディレクトリ内のファイルを取得する
	 *
	 * @return File[] Fileオブジェクトの配列
	 */
	public function getFiles(): array
	{
		$items = scandir($this->path) ?: [];
		$files = [];

		foreach ($items as $name) {
			if ($name === '.' || $name === '..') {
				continue;
			}

			$path = $this->path . DIRECTORY_SEPARATOR . $name;
			$files[] = new File($path);
		}

		return $files;
	}

	/**
	 * ディレクトリが存在するかどうかを確認する
	 *
	 * @return bool
	 */
	public function exists(): bool
	{
		return is_dir($this->path);
	}

}
