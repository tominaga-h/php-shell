<?php

namespace Hytmng\PhpShell\Prompt;

use Hytmng\PhpShell\DependencyInjection\Kernel;
use Hytmng\PhpShell\Prompt\Provider\VariableProviderInterface;

class PromptTemplate
{
	private string $template;
	private array $providers;
	private Kernel $kernel;

	public function __construct(string $template)
	{
		$this->template = $template;
		$this->providers = [];
		$this->setKernel(new Kernel());
	}

	public function setKernel(Kernel $kernel): void
	{
		$this->kernel = $kernel;
	}

	public function addProvider(VariableProviderInterface $provider): void
	{
		$this->providers[] = $provider;
	}

	public function addProviders(array $providers): void
	{
		foreach ($providers as $provider) {
			if ($provider instanceof VariableProviderInterface) {
				$this->providers[] = $provider;
			}
		}
	}

	protected function getVariableValue(string $name): string
	{
		foreach ($this->providers as $provider) {
			if ($provider->getName() === $name) {
				return $provider->getValue();
			}
		}
		throw new \RuntimeException("This variable is not supported: {{$name}}");
	}

	/**
	 * プロンプト文字列を返す
	 *
	 * @return string
	 */
	public function getPrompt(): string
	{
		// カーネルに登録されているプロバイダを追加
		$this->addProviders($this->kernel->getPromptVariableProviders());

		$regex = '/\{(\w+)\}/';
		return preg_replace_callback($regex, function ($matches) {
			$variableName = $matches[1];
			return $this->getVariableValue($variableName);
        }, $this->template);
	}
}
