<?php

namespace Floppy\Bundle\Tests\Form\Type;

use Floppy\Tests\Client\Stub\FakeCredentialsGenerator;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Forms;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Floppy\Client\Url;
use Floppy\Bundle\Form\Type\FileType;

class FileTypeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function rejectInvalidFormOptions($fileTypeAliases, $givenOptions, $expectedException, $expectedFileTypesOptionValue)
    {
        //given

        $fileType = new FileType($this->validFormConfig(), $this->endpointUrl(), $this->checksumChecker(), $this->credentialsGenerator(), $fileTypeAliases);

        if($expectedException) {
            $this->setExpectedException('Symfony\Component\OptionsResolver\Exception\InvalidOptionsException');
        }

        //when

        $optionsResolver = new OptionsResolver();
        $fileType->setDefaultOptions($optionsResolver);

        $actualOptions = $optionsResolver->resolve($givenOptions);

        //then

        if(!$expectedException) {
            $this->assertEquals($expectedFileTypesOptionValue, $actualOptions['file_types']);
        }
    }

    private function validFormConfig()
    {
        return array(
            'swf' => 'url',
            'xap' => 'url',
            'file_key' => 'file',
        );
    }

    public function dataProvider()
    {
        $fileTypeAliases = array('image' => array('name' => 'Images', 'extensions' => array('jpg', 'png')));
        return array(
            array(
                $fileTypeAliases,
                array('file_types' => array('image')),
                false,
                array(
                    $fileTypeAliases['image'],
                )
            ),
            array(
                $fileTypeAliases,
                array('file_types' => array($fileTypeAliases['image'])),
                false,
                array(
                    $fileTypeAliases['image'],
                )
            ),
            array(
                $fileTypeAliases,
                array('file_types' => 'image'),
                false,
                array(
                    $fileTypeAliases['image'],
                )
            ),
            array(
                $fileTypeAliases,
                array('file_types' => array('invalid')),
                true,
                array()
            ),
            array(
                $fileTypeAliases,
                array('file_types' => array(
                    array('name' => 'extensions missing'),
                )),
                true,
                array()
            ),
            array(
                $fileTypeAliases,
                array('file_types' => array(
                    array('extensions' => array('name missing')),
                )),
                true,
                array()
            ),
            array(
                $fileTypeAliases,
                array('file_types' => array(
                    array('name' => 'name', 'extensions' => 'array expected'),
                )),
                true,
                array()
            ),
        );
    }

    private function endpointUrl()
    {
        return new Url('localhost', '/');
    }

    private function checksumChecker()
    {
        return $this->getMock('Floppy\Common\ChecksumChecker');
    }

    private function credentialsGenerator()
    {
        return new FakeCredentialsGenerator(array());
    }
}
 