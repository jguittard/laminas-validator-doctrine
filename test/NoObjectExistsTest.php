<?php

/**
 * @see       https://github.com/laminas/laminas-doctrine-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-doctrine-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-doctrine-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Validator\Doctrine;

use Doctrine\Persistence\ObjectRepository;
use Laminas\Validator\Doctrine\NoObjectExists;
use PHPUnit\Framework\TestCase;
use stdClass;

class NoObjectExistsTest extends TestCase
{
    public function testCanValidateWithNoAvailableObjectInRepository()
    {
        $repository = $this->createMock(ObjectRepository::class);

        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue(null));

        $validator = new NoObjectExists(['object_repository' => $repository, 'fields' => 'matchKey']);

        $this->assertTrue($validator->isValid('matchValue'));
    }

    public function testCannotValidateWithAvailableObjectInRepository()
    {
        $repository = $this->createMock(ObjectRepository::class);

        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue(new stdClass()));

        $validator = new NoObjectExists(['object_repository' => $repository, 'fields' => 'matchKey']);

        $this->assertFalse($validator->isValid('matchValue'));
    }

    public function testErrorMessageIsStringInsteadArray()
    {
        $repository = $this->createMock(ObjectRepository::class);
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue(new stdClass()));
        $validator = new NoObjectExists(['object_repository' => $repository, 'fields' => 'matchKey']);

        $this->assertFalse($validator->isValid('matchValue'));

        $messageTemplates = $validator->getMessageTemplates();

        $expectedMessage = str_replace(
            '%value%',
            'matchValue',
            $messageTemplates[NoObjectExists::ERROR_OBJECT_FOUND]
        );
        $messages        = $validator->getMessages();
        $receivedMessage = $messages[NoObjectExists::ERROR_OBJECT_FOUND];

        $this->assertSame($expectedMessage, $receivedMessage);
    }
}
