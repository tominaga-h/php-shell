<?php

namespace Hytmng\PhpShell;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Hytmng\PhpShell\ReplApplication;
use Hytmng\PhpShell\Command\CommandResults;
use Hytmng\PhpShell\IO\InputFactory;
class Shell
{
	private ?Application $application;
	private ?InputInterface $input;
	private ?OutputInterface $output;

	// シェルが実行中かどうか
	private bool $running;
	// シェルのプロンプト
	private string $prompt = 'psh> ';

	public function __construct()
	{
		$this->application = null;
		$this->input = null;
		$this->output = null;
		$this->running = false;
	}

	public static function createForConsole(string $name, string $version): self
	{
		$shell = new Self();
		$shell->setApplication(new ReplApplication($name, $version));
		$shell->setInput(InputFactory::create());
		$shell->setOutput(new ConsoleOutput());
		return $shell;
	}

	public function setApplication(Application $application): void
	{
		$this->application = $application;
	}

	public function getApplication(): ?Application
	{
		return $this->application;
	}

	public function setInput(InputInterface $input): void
	{
		$this->input = $input;
	}

	public function getInput(): InputInterface
	{
		return $this->input;
	}

	public function setOutput(OutputInterface $output): void
	{
		$this->output = $output;
	}

	public function getOutput(): ?OutputInterface
	{
		return $this->output;
	}

	public function addCommand(Command $command): void
	{
		$this->application->add($command);
	}

	public function getCommands(): array
	{
		return $this->application->all();
	}

	public function setPrompt(string $prompt): void
	{
		$this->prompt = $prompt;
	}

	public function getPrompt(): string
	{
		return $this->prompt;
	}

	public function launch(): void
	{
		if (\is_null($this->application)) {
			throw new \RuntimeException('Application is not set.');
		}

		if (\is_null($this->output)) {
			throw new \RuntimeException('Output is not set.');
		}

		$this->running = true;
	}

	public function isRunning(): bool
	{
		return $this->running;
	}

	public function exit(): void
	{
		$this->running = false;
	}

	/**
	 * ユーザーの入力を処理する
	 *
	 * @param string $input ユーザーの入力
	 */
	public function handleUserInput(string $input): void
	{
		$this->setInput(InputFactory::create($input));
	}

	/**
	 * コマンド名からコマンドを取得する
	 *
	 * @param null|string $commandName コマンド名
	 * @return Command コマンド
	 * @throws \RuntimeException コマンド名がnullの場合
	 */
	public function findCommand(?string $commandName): Command
	{
		if (\is_null($commandName)) {
			throw new \RuntimeException('Command name is not set.');
		}
		return $this->application->find($commandName);
	}

	/**
	 * コマンドを実行する
	 *
	 * @param Command $command コマンド
	 */
	public function handleCommand(Command $command): void
	{
		$result = $command->run($this->input, $this->output);
		$this->handleResult($result);
	}

	/**
	 * コマンドの実行結果を処理する
	 *
	 * @param int $result コマンドの実行結果
	 */
	public function handleResult(int $result): void
	{
		if ($result === CommandResults::EXIT) {
			$this->output->writeln('Bye!');
			$this->exit();
		}
	}

}
