<?php

namespace Hytmng\PhpShell\Prompt\Provider;

use Hytmng\PhpShell\Prompt\Provider\VariableProviderInterface;

class HostnameVariableProvider implements VariableProviderInterface
{
	public function getName(): string
	{
		return 'hostname';
	}

	public function getValue(): string
	{
		return php_uname('n');
	}
}
