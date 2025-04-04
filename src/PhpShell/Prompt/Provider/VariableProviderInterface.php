<?php

namespace Hytmng\PhpShell\Prompt\Provider;

interface VariableProviderInterface
{
	/**
	 * 変数名を返す
	 */
	public function getName(): string;

	/**
	 * 変数の値を返す
	 */
	public function getValue(): string;
}
