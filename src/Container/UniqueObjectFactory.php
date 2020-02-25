<?php

/**
 * @see       https://github.com/laminas/laminas-doctrine-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-doctrine-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-doctrine-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Validator\Doctrine\Container;

use Laminas\Validator\Doctrine\UniqueObject;
use Psr\Container\ContainerInterface;

class UniqueObjectFactory extends AbstractValidatorFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return UniqueObject
     */
    public function __invoke(ContainerInterface $container, string $requestedName, ?array $options = null) : UniqueObject
    {
        $useContext = isset($options['use_context']) ? (bool) $options['use_context'] : false;

        return new UniqueObject($this->merge($options, [
            'object_manager'    => $this->getObjectManager($container, $options),
            'use_context'       => $useContext,
            'object_repository' => $this->getRepository($container, $options),
            'fields'            => $this->getFields($options),
        ]));
    }
}
