<?php

/**
 * @see       https://github.com/laminas/laminas-doctrine-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-doctrine-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-doctrine-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Validator\Doctrine\Container;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Laminas\Validator\Doctrine\Container\NoObjectExistsFactory;
use Laminas\Validator\Doctrine\Exception\InvalidArgumentException;
use Laminas\Validator\Doctrine\NoObjectExists;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class NoObjectExistsFactoryTest extends TestCase
{
    /**
     * @var NoObjectExistsFactory
     */
    private $factory;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->factory = new NoObjectExistsFactory();
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
            NoObjectExists::class,
            $options
        );
        $this->assertInstanceOf(NoObjectExists::class, $instance);
    }

    public function testInvokeWithObjectManagerGiven()
    {
        $repository = $this->prophesize(ObjectRepository::class);
        $objectManager = $this->prophesize(ObjectManager::class);
        $objectManager->getRepository('Foo\Bar')
            ->shouldBeCalled()
            ->willReturn($repository->reveal());

        $options = [
            'target_class'   => 'Foo\Bar',
            'object_manager' => $objectManager->reveal(),
            'fields'         => ['test'],
        ];

        $container = $this->prophesize(ContainerInterface::class);
        $container->get('doctrine.entitymanager.orm_default')
            ->shouldNotBeCalled();

        $instance = $this->factory->__invoke(
            $container->reveal(),
            NoObjectExists::class,
            $options
        );
        $this->assertInstanceOf(NoObjectExists::class, $instance);
    }

    public function testInvokeWithMerge()
    {
        $options = [
            'target_class' => 'Foo\Bar',
            'fields'       => ['test'],
            'messages'     => [
                NoObjectExists::ERROR_OBJECT_FOUND => 'test',
            ]
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
            NoObjectExists::class,
            $options
        );
        $templates = $instance->getMessageTemplates();
        $this->assertArrayHasKey(NoObjectExists::ERROR_OBJECT_FOUND, $templates);
        $this->assertSame('test', $templates[NoObjectExists::ERROR_OBJECT_FOUND]);
    }

    public function testInvokeWithoutTargetClass()
    {
        $this->expectException(InvalidArgumentException::class);

        $container = $this->prophesize(ContainerInterface::class);
        $this->factory->__invoke(
            $container->reveal(),
            NoObjectExists::class,
            []
        );
    }

}
