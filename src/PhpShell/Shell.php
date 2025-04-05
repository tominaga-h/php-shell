<?php

namespace Hytmng\PhpShell;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Hytmng\PhpShell\ReplApplication;
use Hytmng\PhpShell\IO\InputFactory;
use Hytmng\PhpShell\Command\CommandResults;
use Hytmng\PhpShell\Prompt\PromptTemplate;
use Hytmng\PhpShell\DependencyInjection\Kernel;

class Shell
{
	private ?Application $application;
	private ?InputInterface $input;
	private ?OutputInterface $output;
	private ?PromptTemplate $promptTemplate;
	private ?Kernel $kernel;

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
		$this->promptTemplate = null;
		$this->kernel = null;
	}

	public static function createForConsole(string $name, string $version): self
	{
		$shell = new Self();
		$shell->setApplication(new ReplApplication($name, $version));
		$shell->setInput(InputFactory::create());
		$shell->setOutput(new ConsoleOutput());
		$shell->setKernel(new Kernel());
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

	public function setKernel(Kernel $kernel): void
	{
		$this->kernel = $kernel;
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

	public function getStyle(): SymfonyStyle
	{
		if (\is_null($this->output)) {
			throw new \RuntimeException('Output is not set.');
		}

		if (\is_null($this->input)) {
			throw new \RuntimeException('Input is not set.');
		}

		return new SymfonyStyle($this->input, $this->output);
	}

	public function addCommand(Command $command): void
	{
		if (\is_null($this->application)) {
			throw new \RuntimeException('Application is not set.');
		}

		$this->application->add($command);
	}

	public function addCommands(array $commands): void
	{
		foreach ($commands as $command) {
			if ($command instanceof Command) {
				$this->addCommand($command);
			}
		}
	}

	public function getCommands(): array
	{
		return $this->application->all();
	}

	public function setPrompt(string $prompt): void
	{
		$this->prompt = $prompt;
	}

	public function setPromptTemplate(PromptTemplate $template): void
	{
		$this->promptTemplate = $template;
	}

	public function getPrompt(): string
	{
		if (\is_null($this->promptTemplate)) {
			return $this->prompt;
		}

		return $this->promptTemplate->getPrompt();
	}

	public function launch(): void
	{
		if (\is_null($this->application)) {
			throw new \RuntimeException('Application is not set.');
		}

		if (\is_null($this->output)) {
			throw new \RuntimeException('Output is not set.');
		}

		if (!\is_null($this->kernel)) {
			// コマンド自動登録
			$this->addCommands($this->kernel->getCommands());
		}

		// シェルを実行中にする
		$this->running = true;

		// タイトル表示
		$this->getStyle()->title(\sprintf('Welcome to %s!', $this->application->getName()));
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
	public function execCommand(Command $command): int
	{
		$result = $command->run($this->input, $this->output);
		$this->handleResult($result);
		return $result;
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
