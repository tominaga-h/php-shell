<?php

namespace Tests\Prompt;

use PHPUnit\Framework\TestCase;
use Hytmng\PhpShell\Helper\FileSystem\Directory;

class DirectoryTest extends TestCase
{
	private string $tempDir;
	private Directory $directory;

	public function setUp(): void
	{
		// テスト用のディレクトリを作成
		$this->tempDir = sys_get_temp_dir() . '/testdir_' . uniqid();
		mkdir($this->tempDir);
		$this->directory = new Directory($this->tempDir);

		// テスト用ファイルを作成
		$tempFile = $this->tempDir . '/testfile.txt';
		file_put_contents($tempFile, 'test');
		chmod($tempFile, 0755);
	}

	public function tearDown(): void
	{
		if (!is_dir($this->tempDir)){
			return;
		}

		$this->deleteDirectory();
	}

	private function deleteDirectory()
	{
		$files = $this->directory->getFiles();
		foreach ($files as $file) {
			unlink($file->getPath());
		}
		rmdir($this->tempDir);
	}

	public function testPath()
	{
		$actual = $this->directory->getPath();
		$expected = $this->tempDir;
		$this->assertEquals($expected, $actual);
	}

	public function testDirName()
	{
		$actual = $this->directory->getDirName();
		$expected = basename($this->tempDir);
		$this->assertEquals($expected, $actual);
	}

	public function testFiles()
	{
		$files = $this->directory->getFiles();
		$this->assertCount(1, $files);

		$file = $files[0];
		$this->assertEquals('testfile.txt', $file->getFileName());
		$this->assertEquals('test', $file->getContents());
	}

	public function testExists()
	{
		$actual = $this->directory->exists();
		$this->assertTrue($actual);
	}

	public function testExists_false()
	{
		$this->deleteDirectory();
		$actual = $this->directory->exists();
		$this->assertFalse($actual);
	}

}
