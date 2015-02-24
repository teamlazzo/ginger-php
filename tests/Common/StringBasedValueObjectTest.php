<?php

namespace GingerPayments\Payment\Tests\Common;

use GingerPayments\Payment\Common\StringBasedValueObject;

final class StringBasedValueObjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function itShouldCreateAnObjectFromString()
    {
        $stringValueObject = FakeStringBasedValueObject::fromString('foobar');

        $this->assertInstanceOf(
            'GingerPayments\Payment\Tests\Common\FakeStringBasedValueObject',
            $stringValueObject
        );
    }

    /**
     * @test
     */
    public function itShouldConvertToString()
    {
        $stringBasedValueObject = FakeStringBasedValueObject::fromString('foobar');

        $this->assertInternalType('string', $stringBasedValueObject->toString());
        $this->assertEquals('foobar', $stringBasedValueObject->toString());
    }

    /**
     * @test
     */
    public function itShouldCastToString()
    {
        $stringBasedValueObject = FakeStringBasedValueObject::fromString('foobar');

        $this->assertEquals(
            $stringBasedValueObject->toString(),
            (string) $stringBasedValueObject
        );
    }
}

final class FakeStringBasedValueObject
{
    use StringBasedValueObject;

    private function __construct($value)
    {
        $this->value = $value;
    }
}
