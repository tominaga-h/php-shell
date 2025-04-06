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
	 * 元のInputをArrayInputに変換
	 *
	 * @param InputInterface $originalInput 元のInput
	 * @param string $argumentName 設定するargumentの名称
	 * @return InputInterface 変換後のInput
	 */
	public static function convertToArray(InputInterface $originalInput, string $argumentName): InputInterface
	{
		$string = (string) $originalInput;
		$originalArgs = \explode(' ', $string);
		$args = \array_map(function ($arg) {
			return \trim($arg, "'"); // クオートを削除
		}, $originalArgs);

		return new ArrayInput([
			"{$argumentName}" => $args
		]);
	}

}
