<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\BufferedOutput;
use Hytmng\PhpShell\Shell;
use Hytmng\PhpShell\ReplApplication;
use Hytmng\PhpShell\Command\PrintCommand;
use Hytmng\PhpShell\Command\ExitCommand;

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
		$this->shell->setPrompt('test> ');

		$this->assertInstanceOf(Application::class, $this->shell->getApplication());
		$this->assertInstanceOf(StringInput::class, $this->shell->getInput());
		$this->assertInstanceOf(ConsoleOutput::class, $this->shell->getOutput());
		$this->assertEquals('test> ', $this->shell->getPrompt());
	}

	public function testLaunch_throwException()
	{
		$this->expectException(\RuntimeException::class);
		$this->expectExceptionMessage('Application is not set.');
		$this->shell->launch();
	}

	public function testLaunch_throwException_noOutput()
	{
		$this->expectException(\RuntimeException::class);
		$this->expectExceptionMessage('Output is not set.');
		$this->shell->setApplication(new Application());
		$this->shell->launch();
	}

	public function testRunning_launch()
	{
		$this->shell = Shell::createForConsole('test', '1.0.0');
		$this->shell->launch();

		$this->assertTrue($this->shell->isRunning());
	}

	public function testRunning_exit()
	{
		$this->shell = Shell::createForConsole('test', '1.0.0');
		$this->shell->launch();
		$this->shell->exit();

		$this->assertFalse($this->shell->isRunning());
	}

	public function testHandleUserInput()
	{
		$this->shell->handleUserInput('test');
		$input = $this->shell->getInput();

		$this->assertInstanceOf(StringInput::class, $input);
	}

	public function testAddCommand()
	{
		$this->shell = Shell::createForConsole('test', '1.0.0');
		$this->shell->addCommand(new PrintCommand());
		$commands = $this->shell->getCommands();

		$this->assertCount(1, $commands);
		$this->assertArrayHasKey('print', $commands);
	}

	public function testAddCommand_throwException()
	{
		$this->expectException(\RuntimeException::class);
		$this->expectExceptionMessage('Application is not set.');
		$this->shell->addCommand(new PrintCommand());
	}

	public function testFindCommand()
	{
		$this->shell = Shell::createForConsole('test', '1.0.0');
		$this->shell->addCommand(new PrintCommand());
		$command = $this->shell->findCommand('print');

		$this->assertInstanceOf(PrintCommand::class, $command);
	}

	public function testFindCommand_throwException()
	{
		$this->expectException(\RuntimeException::class);
		$this->expectExceptionMessage('Command name is not set.');
		$this->shell->findCommand(null);
	}

	public function testExecCommand()
	{
		$output = new BufferedOutput();
		$this->shell = Shell::createForConsole('test', '1.0.0');
		$this->shell->setOutput($output);
		$this->shell->addCommand(new PrintCommand());

		$this->shell->handleUserInput('print test');
		$command = $this->shell->findCommand('print');
		$result = $this->shell->execCommand($command);

		$this->assertEquals(0, $result);
		$this->assertEquals('test' . PHP_EOL, $output->fetch());
	}

	public function testHandleResult()
	{
		$output = new BufferedOutput();
		$this->shell = Shell::createForConsole('test', '1.0.0');
		$this->shell->setOutput($output);
		$this->shell->addCommand(new ExitCommand());

		$this->shell->handleUserInput('exit');
		$command = $this->shell->findCommand('exit');
		$result = $this->shell->execCommand($command);

		$this->assertEquals(9, $result);
		$this->assertEquals('Bye!' . PHP_EOL, $output->fetch());
	}

}
