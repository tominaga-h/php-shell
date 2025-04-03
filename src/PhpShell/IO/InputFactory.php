<?php

namespace Hytmng\PhpShell\IO;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;

class InputFactory
{
	/**
	 * ユーザーの入力を受け取り、InputInterfaceを返す
	 *
	 * @param string|null $userInput ユーザーの入力
	 * @return InputInterface
	 */
	public static function create(?string $userInput = null): InputInterface
	{
		return new StringInput($userInput ?? '');
	}

}
