<?php

namespace Hytmng\PhpShell\Prompt\Provider;

use Hytmng\PhpShell\Prompt\Provider\VariableProviderInterface;

class CwdVariableProvider implements VariableProviderInterface
{
	public function getName(): string
	{
		return 'cwd';
	}

	public function getValue(): string
	{
		$path = basename(getcwd());
		if ($path === '') {
			return '/';
		}
		return $path;
	}
}
