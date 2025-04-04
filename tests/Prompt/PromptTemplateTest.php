<?php

namespace Tests\Prompt;

use PHPUnit\Framework\TestCase;
use Hytmng\PhpShell\Prompt\PromptTemplate;
use Hytmng\PhpShell\Prompt\Provider\UserVariableProvider;
use Hytmng\PhpShell\DependencyInjection\Kernel;

class PromptTemplateTest extends TestCase
{
	public function testPrompt_unregisteredVariable()
	{
		$this->expectException(\RuntimeException::class);
		$this->expectExceptionMessage('This variable is not supported: {test}');
		$template = new PromptTemplate('{test}');
		$prompt = $template->getPrompt();
	}

	public function testPrompt_noVariable()
	{
		$template = new PromptTemplate('test');
		$prompt = $template->getPrompt();
		$this->assertEquals('test', $prompt);
	}

	public function testPrompt()
	{
		$kernelMock = $this->getEmptyKernelMock();
		$providerMock = $this->createMock(UserVariableProvider::class);
		$providerMock->method('getName')->willReturn('user');
		$providerMock->method('getValue')->willReturn('test');

		$template = new PromptTemplate('{user}');
		$template->setKernel($kernelMock);
		$template->addProvider($providerMock);
		$prompt = $template->getPrompt();
		$this->assertEquals('test', $prompt);
	}

	protected function getEmptyKernelMock(): Kernel
	{
		$kernelMock = $this->createMock(Kernel::class);
		$kernelMock->expects($this->once())
			->method('getPromptVariableProviders')
			->willReturn([]);
		return $kernelMock;
	}
}
