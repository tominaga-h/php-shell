<?php

namespace Tests\Helper;

use PHPUnit\Framework\TestCase;
use Hytmng\PhpShell\Helper\Formatter;

class FormatterTest extends TestCase
{
	private Formatter $formatter;
	private string $tempFile;

	public function setUp(): void
	{
		$this->formatter = new Formatter();

		// テスト用のファイルを作成
		$this->tempFile = tempnam(sys_get_temp_dir(), 'testfile_');
		file_put_contents($this->tempFile, 'test');
	}

	public function tearDown(): void
	{
		if (is_file($this->tempFile)){
			unlink($this->tempFile);
		}
	}

	public function testFormatPermission()
	{
		chmod($this->tempFile, 0644);
		$perms = fileperms($this->tempFile);
		$actual = $this->formatter->formatPermission($perms);
		$expected = '-rw-r--r--';
		$this->assertEquals($expected, $actual);
	}

	public function testFormatPermission_executableFile()
	{
		chmod($this->tempFile, 0755);
		$perms = fileperms($this->tempFile);
		$actual = $this->formatter->formatPermission($perms);
		$expected = '-rwxr-xr-x';
		$this->assertEquals($expected, $actual);
	}

	public function testFormatPermission_directory()
	{
		$perms = fileperms(sys_get_temp_dir());
		$actual = $this->formatter->formatPermission($perms);
		$this->assertTrue(str_starts_with($actual, 'd'));
	}

	public function testFormatFileSize()
	{
		$actual = $this->formatter->formatFileSize(4);
		$expected = '   4.0 B';
		$this->assertEquals($expected, $actual);
	}

	public function testFormatFileSize_length()
	{
		$actual = $this->formatter->formatFileSize(1023);
		$expected = '1023.0 B';
		$this->assertEquals($expected, $actual);
	}

	public function testFormatFileSize_false()
	{
		$actual = $this->formatter->formatFileSize(false);
		$expected = '0';
		$this->assertEquals($expected, $actual);
	}

	public function testFormatFileSize_large()
	{
		$size = 1024 * 1024 * 1024 * 1024;
		$actual = $this->formatter->formatFileSize($size);
		$expected = '   1.0TB';
		$this->assertEquals($expected, $actual);
	}

}
