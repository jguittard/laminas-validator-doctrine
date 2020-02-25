<?php

/**
 * @see       https://github.com/laminas/laminas-doctrine-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-doctrine-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-doctrine-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Validator\Doctrine\Container;

use Laminas\Validator\Doctrine\NoObjectExists;
use Psr\Container\ContainerInterface;

class NoObjectExistsFactory extends AbstractValidatorFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return NoObjectExists
     */
    public function __invoke(ContainerInterface $container, string $requestedName, ?array $options = null) : NoObjectExists
    {
        $repository = $this->getRepository($container, $options);

        return new NoObjectExists($this->merge($options, [
            'object_repository' => $repository,
            'fields'            => $this->getFields($options),
        ]));
    }
}
