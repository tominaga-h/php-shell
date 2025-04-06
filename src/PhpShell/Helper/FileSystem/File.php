<?php

namespace Hytmng\PhpShell\Helper\FileSystem;

/**
 * ファイル関連のヘルパークラス
 */
class File
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

	/**
	 * ファイルのパスを取得する
	 *
	 * @return string
	 */
    public function getPath(): string
    {
        return $this->path;
    }

	/**
	 * ファイル名を取得する
	 *
	 * @return string
	 */
    public function getFileName(): string
    {
        return basename($this->path);
    }

	/**
	 * ファイルの内容を取得する
	 *
	 * @return string
	 */
	public function getContents(): string
	{
		return file_get_contents($this->path);
	}

	/**
	 * ファイルのパーミッションを取得する
	 *
	 * @return int
	 * @throws \RuntimeException
	 */
    public function getPermissions(): int
    {
        $perms = @fileperms($this->path);
		if ($perms === false) {
			throw new \RuntimeException('Failed to get permissions for file: ' . $this->path);
		}
		return $perms;
    }

	/**
	 * ファイルの所有者を取得する
	 *
	 * @return string
	 */
    public function getOwner(): string
    {
        $uid = fileowner($this->path);
        if (function_exists('posix_getpwuid')) {
			return posix_getpwuid($uid)['name'] ?? (string) $uid;
		}
		return (string)$uid;
    }

	/**
	 * ファイルのグループを取得する
	 *
	 * @return string
	 */
    public function getGroup(): string
    {
        $gid = filegroup($this->path);
		if (function_exists('posix_getgrgid')) {
			return posix_getgrgid($gid)['name'] ?? (string) $gid;
		}
		return (string)$gid;
    }

	/**
	 * ファイルのサイズを取得する
	 *
	 * @return int
	 */
    public function getSize(): int
    {
        return filesize($this->path);
    }

	/**
	 * ファイルの更新時刻を取得する
	 *
	 * @return int
	 */
    public function getMTime(): int
    {
        return filemtime($this->path);
    }

	/**
	 * ファイルの拡張子を取得する
	 *
	 * @return string
	 */
    public function getExtension(): string
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

	/**
	 * ファイルがディレクトリかどうかを確認する
	 *
	 * @return bool
	 */
    public function isDirectory(): bool
    {
        return is_dir($this->path);
    }

	/**
	 * ファイルがシンボリックリンクかどうかを確認する
	 *
	 * @return bool
	 */
	public function isLink(): bool
	{
		return is_link($this->path);
	}

	/**
	 * ファイルが実行可能かどうかを確認する
	 *
	 * @return bool
	 */
    public function isExecutable(): bool
    {
		$perms = $this->getPermissions();

		$ownerExec  = ($perms & 0x0040) ? true : false; // 所有者 (S_IXUSR)
		$groupExec  = ($perms & 0x0008) ? true : false; // グループ (S_IXGRP)
		$otherExec  = ($perms & 0x0001) ? true : false; // その他 (S_IXOTH)

		return $ownerExec && $groupExec && $otherExec;
    }

	/**
	 * ファイルが隠しファイルかどうかを確認する
	 *
	 * @return bool
	 */
	public function isHiddenFile(): bool
	{
		return \str_starts_with($this->getFileName(), '.');
	}

	/**
	 * ファイルが存在するかどうかを確認する
	 *
	 * @return bool
	 */
	public function exists(): bool
	{
		return file_exists($this->path);
	}

}
