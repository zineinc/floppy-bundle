<?php


namespace Form\DataTransformer;


use ZineInc\Storage\Common\FileId;
use ZineInc\StorageBundle\Form\DataTransformer\FileDataTransformer;

class FileDataTransformerTest extends \PHPUnit_Framework_TestCase
{
    private $transformer;
    private $checksumChecker;

    protected function setUp()
    {
        $this->checksumChecker = $this->getMock('ZineInc\Storage\Common\ChecksumChecker');
        $this->transformer = new FileDataTransformer($this->checksumChecker);
    }

    /**
     * @test
     * @dataProvider transformProvider
     */
    public function testTransform($value, $expectedValue, $expectedException = false)
    {
        if($expectedException) {
            $this->setExpectedException('Symfony\Component\Form\Exception\TransformationFailedException');
        }

        //when

        $actualValue = $this->transformer->transform($value);

        //then

        $this->assertEquals($expectedValue, $actualValue);
    }

    public function transformProvider()
    {
        return array(
            array(null, null),
            array(new FileId('id'), 'id'),
            array('id', 'id'),
            array(new \stdClass(), null, true),
            array(new FileId(null), null),
        );
    }

    /**
     * @test
     * @dataProvider reverseTransformProvider
     */
    public function testReverseTransform($value, $expectedValue, $checksumSuccess = true, $expectedException = false)
    {
        if($expectedException) {
            $this->setExpectedException('Symfony\Component\Form\Exception\TransformationFailedException');
        }

        if(isset($value['attributes']))
        {
            $attrs = @json_decode($value['attributes'], true);
            $checksum = isset($attrs['checksum']) ? $attrs['checksum'] : '';
            unset($attrs['checksum']);
            $this->checksumChecker->expects($this->any())
                ->method('isChecksumValid')
                ->with($checksum, $attrs)
                ->will($this->returnValue($checksumSuccess));
        }
        else
        {
            $this->checksumChecker->expects($this->any())
                ->method('isChecksumValid')
                ->will($this->returnValue(true));
        }

        //when

        $actualValue = $this->transformer->reverseTransform($value);

        //then

        $this->assertEquals($expectedValue, $actualValue);
    }

    public function reverseTransformProvider()
    {
        $attrs = array('name' => 'value', 'id' => 'someid', 'checksum' => 'abc');
        return array(
            array('id', new FileId('id')),
            array(array('id' => 'someid', 'attributes' => json_encode($attrs)), new FileId('someid', $attrs)),
            //checksum is required
            array(array('id' => 'someid', 'attributes' => json_encode(array('checksum' => null) + $attrs)), null, true, true),
            //attributes.id === id
            array(array('id' => 'someid', 'attributes' => json_encode(array('id' => 'another-id', 'checksum' => 'abc'))), null, true, true),
            array(array('id' => null, 'attributes' => null), null),
            array(array('id' => null, 'attributes' => json_encode(array())), null),
            array(array('id' => null, 'attributes' => array()), null, true, true),
            array(array('id' => array(), 'attributes' => null), null, true, true),
            array(null, null),
            array(array('invalid array'), null, true, true),
            array(array('id' => 'someid', 'attributes' => 'invalid json'), null, true, true),

            array(array('id' => 'someid', 'attributes' => json_encode($attrs)), null, false, true),
        );
    }
}
 