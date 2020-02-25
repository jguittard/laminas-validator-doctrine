<?php

/**
 * @see       https://github.com/laminas/laminas-doctrine-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-doctrine-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-doctrine-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Validator\Doctrine\Container;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Laminas\Validator\Doctrine\Container\AbstractValidatorFactory;
use Laminas\Validator\Doctrine\Container\UniqueObjectFactory;
use Laminas\Validator\Doctrine\UniqueObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class UniqueObjectFactoryTest extends TestCase
{
    /**
     * @var UniqueObjectFactory
     */
    private $factory;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->factory = new UniqueObjectFactory();
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
            UniqueObject::class,
            $options
        );
        $this->assertInstanceOf(UniqueObject::class, $instance);
    }
}
