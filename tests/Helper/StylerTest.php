<?php

namespace Tests\Helper;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\Mock;
use Hytmng\PhpShell\Helper\Styler;
use Hytmng\PhpShell\Helper\FileSystem\File;

class StylerTest extends TestCase
{
	private Styler $styler;

	public function setUp(): void
	{
		$this->styler = new Styler();
	}

	public function testStylePermissionString()
	{
		$map = [
			'-' => 'gray',
			'l' => 'yellow',
			'd' => 'blue',
			'r' => 'yellow',
			'w' => 'red',
			'x' => 'green',
		];

		foreach ($map as $symbol => $color) {
			$actual = $this->styler->stylePermissionString($symbol);
			$expected = "<fg={$color}>{$symbol}</>";
			$this->assertEquals($expected, $actual);
		}
	}

	public function testStylePermissionString_decorated()
	{
		$this->styler = new Styler(true);
		$actual = $this->styler->stylePermissionString('-');
		$expected = '-';
		$this->assertEquals($expected, $actual);
	}

	public function testStyleFileName_directory()
	{
		$fileMock = $this->getFileMock(true, false, false);
		$actual = $this->styler->styleFileName($fileMock);
		$expected = '<fg=blue>test/</>';
		$this->assertEquals($expected, $actual);
	}

	public function testStyleFileName_link()
	{
		$fileMock = $this->getFileMock(false, true, false);
		$actual = $this->styler->styleFileName($fileMock);
		$expected = '<fg=yellow>test@</>';
		$this->assertEquals($expected, $actual);
	}

	public function testStyleFileName_executable()
	{
		$fileMock = $this->getFileMock(false, false, true);
		$actual = $this->styler->styleFileName($fileMock);
		$expected = '<fg=green>test*</>';
		$this->assertEquals($expected, $actual);
	}

	public function testStyleFileName_decorated()
	{
		$this->styler = new Styler(true);
		$fileMock = $this->getFileMock(true, false, false);
		$actual = $this->styler->styleFileName($fileMock);
		$expected = 'test';
		$this->assertEquals($expected, $actual);
	}

	private function getFileMock(bool $isDirectory, bool $isLink, bool $isExecutable)
	{
		$fileMock = $this->getMockBuilder(File::class)
			->disableOriginalConstructor()
			->onlyMethods(['getFileName', 'isDirectory', 'isLink', 'isExecutable'])
			->getMock();

		$fileMock->expects($this->any())->method('getFileName')->willReturn('test');
		$fileMock->expects($this->any())->method('isDirectory')->willReturn($isDirectory);
		$fileMock->expects($this->any())->method('isLink')->willReturn($isLink);
		$fileMock->expects($this->any())->method('isExecutable')->willReturn($isExecutable);

		return $fileMock;
	}
}
