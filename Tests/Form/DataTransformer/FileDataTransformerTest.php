<?php


namespace Form\DataTransformer;


use ZineInc\Storage\Common\FileId;
use ZineInc\StorageBundle\Form\DataTransformer\FileDataTransformer;

class FileDataTransformerTest extends \PHPUnit_Framework_TestCase
{
    private $transformer;

    protected function setUp()
    {
        $this->transformer = new FileDataTransformer();
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
    public function testReverseTransform($value, $expectedValue, $expectedException = false)
    {
        if($expectedException) {
            $this->setExpectedException('Symfony\Component\Form\Exception\TransformationFailedException');
        }

        //when

        $actualValue = $this->transformer->reverseTransform($value);

        //then

        $this->assertEquals($expectedValue, $actualValue);
    }

    public function reverseTransformProvider()
    {
        $attrs = array('name' => 'value');
        return array(
            array('id', new FileId('id')),
            array(array('id' => 'someid', 'attributes' => json_encode($attrs)), new FileId('someid', $attrs)),
            array(array('id' => null, 'attributes' => null), null),
            array(array('id' => null, 'attributes' => json_encode(array())), null),
            array(array('id' => null, 'attributes' => array()), null, true),
            array(array('id' => array(), 'attributes' => null), null, true),
            array(null, null),
            array(array('invalid array'), null, true),
            array(array('id' => 'someid', 'attributes' => 'invalid json'), null, true),
        );
    }
}
 