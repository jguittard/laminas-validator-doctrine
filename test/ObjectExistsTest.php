<?php

/**
 * @see       https://github.com/laminas/laminas-doctrine-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-doctrine-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-doctrine-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Validator\Doctrine;

use Doctrine\Persistence\ObjectRepository;
use Laminas\Validator\Doctrine\Exception\InvalidArgumentException;
use Laminas\Validator\Doctrine\ObjectExists;
use PHPUnit\Framework\TestCase;
use stdClass;

class ObjectExistsTest extends TestCase
{
    public function testCanValidateWithSingleField()
    {
        $repository = $this->createMock(ObjectRepository::class);

        $repository
            ->expects($this->exactly(2))
            ->method('findOneBy')
            ->with(['matchKey' => 'matchValue'])
            ->will($this->returnValue(new stdClass()));

        $validator = new ObjectExists(['object_repository' => $repository, 'fields' => 'matchKey']);

        $this->assertTrue($validator->isValid('matchValue'));
        $this->assertTrue($validator->isValid(['matchKey' => 'matchValue']));
    }

    public function testCanValidateWithMultipleFields()
    {
        $repository = $this->createMock(ObjectRepository::class);
        $repository
            ->expects($this->exactly(2))
            ->method('findOneBy')
            ->with(['firstMatchKey' => 'firstMatchValue', 'secondMatchKey' => 'secondMatchValue'])
            ->will($this->returnValue(new stdClass()));

        $validator = new ObjectExists([
            'object_repository' => $repository,
            'fields'            => [
                'firstMatchKey',
                'secondMatchKey',
            ],
        ]);
        $this->assertTrue(
            $validator->isValid([
                'firstMatchKey'  => 'firstMatchValue',
                'secondMatchKey' => 'secondMatchValue',
            ])
        );
        $this->assertTrue($validator->isValid(['firstMatchValue', 'secondMatchValue']));
    }

    public function testCanValidateFalseOnNoResult()
    {
        $repository = $this->createMock(ObjectRepository::class);
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue(null));

        $validator = new ObjectExists([
            'object_repository' => $repository,
            'fields'            => 'field',
        ]);
        $this->assertFalse($validator->isValid('value'));
    }

    public function testWillRefuseMissingRepository()
    {
        $this->expectException(InvalidArgumentException::class);

        new ObjectExists(['fields' => 'field']);
    }

    public function testWillRefuseNonObjectRepository()
    {
        $this->expectException(InvalidArgumentException::class);

        new ObjectExists(['object_repository' => 'invalid', 'fields' => 'field']);
    }

    public function testWillRefuseInvalidRepository()
    {
        $this->expectException(InvalidArgumentException::class);

        new ObjectExists(['object_repository' => new stdClass(), 'fields' => 'field']);
    }

    public function testWillRefuseMissingFields()
    {
        $this->expectException(InvalidArgumentException::class);

        new ObjectExists([
            'object_repository' => $this->createMock(ObjectRepository::class),
        ]);
    }

    public function testWillRefuseEmptyFields()
    {
        $this->expectException(InvalidArgumentException::class);

        new ObjectExists([
            'object_repository' => $this->createMock(ObjectRepository::class),
            'fields'            => [],
        ]);
    }

    public function testWillRefuseNonStringFields()
    {
        $this->expectException(InvalidArgumentException::class);
        new ObjectExists([
            'object_repository' => $this->createMock(ObjectRepository::class),
            'fields'            => [123],
        ]);
    }

    public function testWillNotValidateOnFieldsCountMismatch()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Provided values count is 1, while expected number of fields to be matched is 2'
        );
        $validator = new ObjectExists([
            'object_repository' => $this->createMock(ObjectRepository::class),
            'fields'            => ['field1', 'field2'],
        ]);
        $validator->isValid(['field1Value']);
    }

    public function testWillNotValidateOnFieldKeysMismatch()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Field "field2" was not provided, but was expected since the configured field lists needs it for validation'
        );

        $validator = new ObjectExists([
            'object_repository' => $this->createMock(ObjectRepository::class),
            'fields'            => ['field1', 'field2'],
        ]);

        $validator->isValid(['field1' => 'field1Value']);
    }

    public function testErrorMessageIsStringInsteadArray()
    {
        $validator  = new ObjectExists([
            'object_repository' => $this->createMock(ObjectRepository::class),
            'fields'            => 'field',
        ]);

        $this->assertFalse($validator->isValid('value'));

        $messageTemplates = $validator->getMessageTemplates();

        $expectedMessage = str_replace(
            '%value%',
            'value',
            $messageTemplates[ObjectExists::ERROR_NO_OBJECT_FOUND]
        );
        $messages        = $validator->getMessages();
        $receivedMessage = $messages[ObjectExists::ERROR_NO_OBJECT_FOUND];

        $this->assertSame($expectedMessage, $receivedMessage);
    }
}
