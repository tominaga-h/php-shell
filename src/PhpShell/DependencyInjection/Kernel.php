<?php

namespace Hytmng\PhpShell\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class Kernel
{
	private ContainerBuilder $container;

	public function __construct()
	{
		$this->container = new ContainerBuilder();
		$locator = new FileLocator(__DIR__ . '/../../../config');
		$loader = new YamlFileLoader($this->container, $locator);
		$loader->load('services.yaml');
		$this->container->compile();
	}

	public function getContainer(): ContainerBuilder
	{
		return $this->container;
	}

	public function getCommands(): array
	{
		$services = $this->container->findTaggedServiceIds('console.command');
		$commands = [];
		foreach ($services as $serviceId => $_) {
			$command = $this->container->get($serviceId);
			$commands[] = $command;
		}
		return $commands;
	}
}
