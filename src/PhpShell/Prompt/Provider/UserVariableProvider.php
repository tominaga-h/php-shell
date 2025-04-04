<?php

namespace Hytmng\PhpShell\Prompt\Provider;

use Hytmng\PhpShell\Prompt\Provider\VariableProviderInterface;

class UserVariableProvider implements VariableProviderInterface
{
	public function getName(): string
	{
		return 'user';
	}

	public function getValue(): string
	{
		return get_current_user();
	}
}
