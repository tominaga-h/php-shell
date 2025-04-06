<?php

namespace Hytmng\PhpShell\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;

class Command extends BaseCommand
{
	private bool $externalCommand = false;
	private ?string $argumentName = null;

	public function setAsExternalCommand(?string $argumentName = null): self
	{
		$this->externalCommand = true;
		$this->argumentName = $argumentName;
		return $this;
	}

	public function isExternalCommand(): bool
	{
		return $this->externalCommand;
	}

	public function setArgumentName(string $argumentName): self
	{
		$this->argumentName = $argumentName;
		return $this;
	}

	public function getArgumentName(): ?string
	{
		return $this->argumentName;
	}
}
