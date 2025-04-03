<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Hytmng\PhpShell\Shell;
use Hytmng\PhpShell\ReplApplication;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class ShellTest extends TestCase
{
	private Shell $shell;

	public function setUp(): void
	{
		$this->shell = new Shell();
	}

	public function testCreate()
	{
		$this->shell = Shell::createForConsole('test', '1.0.0');

		$this->assertInstanceOf(ReplApplication::class, $this->shell->getApplication());
		$this->assertInstanceOf(StringInput::class, $this->shell->getInput());
		$this->assertInstanceOf(ConsoleOutput::class, $this->shell->getOutput());
	}

	public function testSetterGetter()
	{
		$this->shell->setApplication(new Application());
		$this->shell->setInput(new StringInput(''));
		$this->shell->setOutput(new ConsoleOutput());

		$this->assertInstanceOf(Application::class, $this->shell->getApplication());
		$this->assertInstanceOf(StringInput::class, $this->shell->getInput());
		$this->assertInstanceOf(ConsoleOutput::class, $this->shell->getOutput());
	}
}
