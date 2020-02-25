<?php

/**
 * @see       https://github.com/laminas/laminas-doctrine-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-doctrine-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-doctrine-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Validator\Doctrine\Container;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Laminas\Validator\Doctrine\Container\ObjectExistsFactory;
use Laminas\Validator\Doctrine\ObjectExists;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ObjectExistsFactoryTest extends TestCase
{
    /**
     * @var ObjectExistsFactory
     */
    private $factory;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->factory =  new ObjectExistsFactory();
    }

    public function testInvoke()
    {
        $options = [
            'target_class' => 'Foo\Bar',
            'fields'       => ['test'],
        ];

        $repository = $this->prophesize(ObjectRepository::class);
        $objectManager = $this->prophesize(ObjectManager::class);
        $objectManager->getRepository('Foo\Bar')
            ->shouldBeCalled()
            ->willReturn($repository->reveal());

        $container = $this->prophesize(ContainerInterface::class);
        $container->get('doctrine.entitymanager.orm_default')
            ->shouldBeCalled()
            ->willReturn($objectManager->reveal());

        $instance = $this->factory->__invoke(
            $container->reveal(),
            ObjectExists::class,
            $options
        );
        $this->assertInstanceOf(ObjectExists::class, $instance);
    }
}
