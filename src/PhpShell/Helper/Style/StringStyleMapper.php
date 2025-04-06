<?php

namespace Hytmng\PhpShell\Helper\Style;

use Hytmng\PhpShell\Helper\Style\StyleMapperInterface;

class StringStyleMapper implements StyleMapperInterface
{
	private array $map;

	/**
	 * コンストラクタ
	 *
	 * @param array $map 対象の文字列と色を連想配列で指定
	 */
	public function __construct(array $map)
	{
		$this->map = $map;
	}

	public function apply(string $char): string
	{
		$color = $this->map[$char] ?? 'gray';
		return "<fg={$color}>{$char}</>";
	}
}
