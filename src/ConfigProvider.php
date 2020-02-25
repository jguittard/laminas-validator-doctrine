<?php

/**
 * @see       https://github.com/laminas/laminas-doctrine-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-doctrine-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-doctrine-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Validator\Doctrine;

class ConfigProvider
{
    /**
     * Return configuration for this component.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'validators' => $this->getValidators(),
        ];
    }

    /**
     * @return array
     */
    public function getValidators(): array
    {
        return [
            'factories' => [
                NoObjectExists::class => Container\NoObjectExistsFactory::class,
                ObjectExists::class => Container\ObjectExistsFactory::class,
                UniqueObject::class => Container\UniqueObjectFactory::class,
            ],
        ];
    }
}
