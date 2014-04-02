<?php


namespace Floppy\Bundle\Tests\Doctrine\DBAL\Types;


use Doctrine\DBAL\Types\Type;
use Floppy\Bundle\Doctrine\DBAL\Types\FileType;
use Floppy\Common\FileId;

class FileTypeTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        if(Type::hasType('extra_file')) {
            Type::overrideType('extra_file', 'Floppy\Bundle\Doctrine\DBAL\Types\FileType');
        } else {
            Type::addType('extra_file', 'Floppy\Bundle\Doctrine\DBAL\Types\FileType');
        }
    }


    /**
     * @test
     * @dataProvider convertToPhpValueProvider
     */
    public function testConvertToPhpValue($value, $expectedValue)
    {
        //given

        $fileType = Type::getType('extra_file');

        //when

        $actualValue = $fileType->convertToPHPValue($value, $this->createPlatformStub());

        //then

        $this->assertEquals($expectedValue, $actualValue);
    }

    public function convertToPhpValueProvider()
    {
        return array(
            array(null, null),
            array('', null),
            array('some id', new FileId('some id')),
        );
    }

    /**
     * @test
     * @dataProvider convertToDatabaseValueProvider
     */
    public function testConvertToDatabaseValue($value, $expectedValue)
    {
        //given

        $fileType = Type::getType('extra_file');

        //when

        $actualValue = $fileType->convertToDatabaseValue($value, $this->createPlatformStub());

        //then

        $this->assertSame($expectedValue, $actualValue);
    }

    public function convertToDatabaseValueProvider()
    {
        return array(
            array(null, null),
            array(new FileId(''), null),
            array(new FileId('some id'), 'some id'),
        );
    }

    private function createPlatformStub()
    {
        return $this->getMockForAbstractClass('Doctrine\DBAL\Platforms\AbstractPlatform');
    }
}
 