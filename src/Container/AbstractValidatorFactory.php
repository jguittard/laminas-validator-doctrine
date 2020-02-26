<?php

/**
 * @see       https://github.com/laminas/laminas-doctrine-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-doctrine-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-doctrine-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Validator\Doctrine\Container;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Validator\Doctrine\Exception\InvalidArgumentException;
use Psr\Container\ContainerInterface;
use function is_string;
use function sprintf;

abstract class AbstractValidatorFactory
{
    public const DEFAULT_OBJECT_MANAGER_KEY = 'doctrine.entitymanager.orm_default';

    /**
     * @param ContainerInterface $container
     * @param null|mixed[] $options
     *
     * @return ObjectRepository
     */
    protected function getRepository(ContainerInterface $container, ?array $options = null) : ObjectRepository
    {
        if (array_key_exists('object_repository', $options) && isset($options['object_repository'])) {
            if ($options['object_repository'] instanceof ObjectRepository) {
                return $options['object_repository'];
            }
            return $container->get($options['object_repository']);
        } else {
            if (empty($options['target_class'])) {
                throw new InvalidArgumentException(sprintf(
                                                       'Option \'target_class\' is missing when creating validator %s',
                                                       self::class
                                                   ));
            }

            $objectManager   = $this->getObjectManager($container, $options);
            $targetClassName = $options['target_class'];

            return $objectManager->getRepository($targetClassName);
        }
    }

    /**
     * @param ContainerInterface $container
     * @param array|null $options
     * @return ObjectManager
     */
    protected function getObjectManager(ContainerInterface $container, ?array $options = null) : ObjectManager
    {
        $objectManager = $options['object_manager'] ?? self::DEFAULT_OBJECT_MANAGER_KEY;

        if (is_string($objectManager)) {
            $objectManager = $container->get($objectManager);
        }

        return $objectManager;
    }

    /**
     * Helper to merge options array passed to `__invoke`
     * together with the options array created based on the above
     * helper methods.
     *
     * @param mixed[] $previousOptions
     * @param mixed[] $newOptions
     *
     * @return mixed[]
     */
    protected function merge(array $previousOptions, array $newOptions) : array
    {
        return ArrayUtils::merge($previousOptions, $newOptions, true);
    }

    /**
     * @param mixed[] $options
     *
     * @return mixed[]
     */
    protected function getFields(array $options) : array
    {
        if (isset($options['fields'])) {
            return (array) $options['fields'];
        }

        return [];
    }
}
