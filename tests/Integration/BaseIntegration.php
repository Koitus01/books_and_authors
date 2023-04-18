<?php

namespace App\Tests\Integration;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class BaseIntegration extends KernelTestCase
{
	protected ManagerRegistry $doctrine;
    protected ContainerInterface|Container $container;

	protected function setUp(): void
	{
		$kernel = self::bootKernel();

		$this->doctrine = $kernel->getContainer()
			->get( 'doctrine' );
        $this->container = static::getContainer();
	}
}