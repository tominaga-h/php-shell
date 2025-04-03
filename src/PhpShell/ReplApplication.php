<?php

namespace Hytmng\PhpShell;

use Symfony\Component\Console\Application;

class ReplApplication extends Application
{
	protected function getDefaultCommands(): array
	{
		return []; // デフォルトコマンドを排除
	}
}
