<?php

namespace Hytmng\PhpShell\IO;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Input\ArrayInput;

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

	/**
	 * 元のInputをExecコマンドを実行するためのInputに変換
	 *
	 * @param InputInterface $originalInput 元のInput
	 * @return InputInterface 変換後のInput
	 */
	public static function convertToExec(InputInterface $originalInput): InputInterface
	{
		$string = (string) $originalInput;
		$args = \explode(' ', $string);
		return new ArrayInput([
			'command' => [
				$originalInput->getFirstArgument(),
				\implode(' ', \array_slice($args, 1)),
			]
		]);
	}

}
