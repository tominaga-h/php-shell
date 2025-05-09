<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Hytmng\PhpShell\Shell;
use Hytmng\PhpShell\ReplApplication;
use Hytmng\PhpShell\Command\PrintCommand;
use Hytmng\PhpShell\Command\ExitCommand;
use Hytmng\PhpShell\Command\GitCommand;
use Hytmng\PhpShell\Command\ExecCommand;
use Hytmng\PhpShell\Prompt\PromptTemplate;

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

	public function testGetStyle_throwException_noOutput()
	{
		$this->expectException(\RuntimeException::class);
		$this->expectExceptionMessage('Output is not set.');
		$this->shell->getStyle();
	}

	public function testGetStyle_throwException_noInput()
	{
		$this->expectException(\RuntimeException::class);
		$this->expectExceptionMessage('Input is not set.');
		$this->shell->setOutput(new BufferedOutput());
		$this->shell->getStyle();
	}

	public function testGetStyle()
	{
		$this->shell = Shell::createForConsole('test', '1.0.0');
		$style = $this->shell->getStyle();

		$this->assertInstanceOf(SymfonyStyle::class, $style);
	}

	public function testPromptTemplate()
	{
		$expected = 'test> ';
		$promptTemplateMock = $this->getMockBuilder(PromptTemplate::class)
			->disableOriginalConstructor()
			->onlyMethods(['getPrompt'])
			->getMock();

		$promptTemplateMock
			->expects($this->once())
			->method('getPrompt')
			->willReturn($expected);

		$this->shell->setPromptTemplate($promptTemplateMock);
		$actual = $this->shell->getPrompt();
		$this->assertEquals($expected, $actual);
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
		$this->shell->setOutput(new BufferedOutput());
		$this->shell->launch();

		$this->assertTrue($this->shell->isRunning());
	}

	public function testRunning_exit()
	{
		$this->shell = Shell::createForConsole('test', '1.0.0');
		$this->shell->setOutput(new BufferedOutput());
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

	public function testFindCommand_externalCommand()
	{
		$this->shell = Shell::createForConsole('test', '1.0.0');
		$this->shell->setInput(new StringInput('git status'));
		$this->shell->addCommand(new GitCommand());

		$command = $this->shell->findCommand('git');
		$input = $this->shell->getInput();
		$actual = (string) $input;
		$expected = 'git status';

		$this->assertInstanceOf(GitCommand::class, $command);
		$this->assertEquals($expected, $actual);
	}

	public function testFindCommand_execCommand()
	{
		$this->shell = Shell::createForConsole('test', '1.0.0');
		$this->shell->setInput(new StringInput('/bin/ls'));
		$this->shell->addCommand(new ExecCommand());

		$command = $this->shell->findCommand('/bin/ls');
		$input = $this->shell->getInput();
		$actual = (string) $input;
		$expected = "'/bin/ls'";

		$this->assertInstanceOf(ExecCommand::class, $command);
		$this->assertEquals($expected, $actual);
	}

	public function testFindCommand_CommandNotFound()
	{
		$this->expectException(CommandNotFoundException::class);
		$this->expectExceptionMessage('Command "notfound" is not found.');
		$this->shell->findCommand('notfound');
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
