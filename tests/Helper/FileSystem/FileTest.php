<?php

namespace Tests\Prompt;

use PHPUnit\Framework\TestCase;
use Hytmng\PhpShell\Helper\FileSystem\File;

class FileTest extends TestCase
{
	private string $tempFile;
	private File $file;

	public function setUp(): void
	{
		// テスト用のファイルを作成
		$this->tempFile = tempnam(sys_get_temp_dir(), 'testfile_');
		file_put_contents($this->tempFile, 'test');
		chmod($this->tempFile, 0755);

		$this->file = new File($this->tempFile);
	}

	public function tearDown(): void
	{
		if (file_exists($this->tempFile)) {
			unlink($this->tempFile); // テスト用のファイルを削除
		}
	}

	public function testPath()
	{
		$actual = $this->file->getPath();
		$expected = $this->tempFile;
		$this->assertEquals($expected, $actual);
	}

	public function testFileName()
	{
		$actual = $this->file->getFileName();
		$expected = 'testfile_';
		$this->assertTrue(str_starts_with($actual, $expected));
	}

	public function testContents()
	{
		$actual = $this->file->getContents();
		$expected = 'test';
		$this->assertEquals($expected, $actual);
	}

	public function testPermissions()
	{
		$actual = $this->file->getPermissions();
		$expected = 33261;
		$this->assertEquals($expected, $actual);
	}

	public function testPermissions_throwException()
	{
		unlink($this->tempFile);
		$this->expectException(\RuntimeException::class);
		$this->expectExceptionMessage('Failed to get permissions for file: ' . $this->tempFile);
		$this->file->getPermissions();
	}

	public function testOwner()
	{
		$actual = $this->file->getOwner();
		$expected = 'root';
		$this->assertEquals($expected, $actual);
	}

	public function testGroup()
	{
		$actual = $this->file->getGroup();
		$expected = 'root';
		$this->assertEquals($expected, $actual);
	}

	public function testSize()
	{
		$actual = $this->file->getSize();
		$expected = 4;
		$this->assertEquals($expected, $actual);
	}

	public function testMTime()
	{
		$actual = date('Y-m-d', $this->file->getMTime());
		$expected = (new \DateTime())->format('Y-m-d');
		$this->assertEquals($expected, $actual);
	}

	public function testExtension()
	{
		$this->file = new File(__DIR__ . '/FileTest.php');
		$actual = $this->file->getExtension();
		$expected = 'php';
		$this->assertEquals($expected, $actual);
	}

	public function testType_link()
	{
		$linkFile = $this->tempFile . '.link';
		symlink($this->tempFile, $linkFile);
		$this->file = new File($linkFile);
		$actual = $this->file->getType();
		$expected = 'link';
		$this->assertEquals($expected, $actual);
		unlink($linkFile);
	}

	public function testType_dir()
	{
		$this->file = new File(sys_get_temp_dir());
		$actual = $this->file->getType();
		$expected = 'dir';
		$this->assertEquals($expected, $actual);
		$this->assertTrue($this->file->isDirectory());
	}

	public function testType_file()
	{
		$actual = $this->file->getType();
		$expected = 'file';
		$this->assertEquals($expected, $actual);
	}

	public function testExecutable()
	{
		chmod($this->tempFile, 0777);
		$actual = $this->file->isExecutable();
		$this->assertTrue($actual);
	}

	public function testExists()
	{
		$actual = $this->file->exists();
		$this->assertTrue($actual);
	}

	public function testExists_false()
	{
		unlink($this->tempFile);
		$actual = $this->file->exists();
		$this->assertFalse($actual);
	}

}
