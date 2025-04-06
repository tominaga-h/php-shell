<?php

namespace Hytmng\PhpShell\Helper\Style;

interface StyleMapperInterface
{
	/**
	 * 文字列を装飾して返す
	 *
	 * @param string $char 装飾する文字列
	 * @return string 装飾された文字列
	 */
	public function apply(string $char): string;
}
