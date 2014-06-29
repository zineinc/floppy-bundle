<?php


namespace Floppy\Bundle\Tests\UrlGenerator;

use Floppy\Bundle\UrlGenerator\DefaultFilterSetApplier;
use Floppy\Common\FileId;

class DefaultFilterSetApplierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function givenOneFilterSet_applyFilterToFileId()
    {
        //given

        $filterSet = array(
            'quality' => 100,
            'thumbnail' => array(
                'size' => array(100, 100),
            ),
        );
        $filterSets = array(
            'some_thumb' => $filterSet,
        );

        //when

        $actualFileId = $this->createApplier($filterSets)->applyFilterSet($this->createAnyFileId(), 'some_thumb');

        //then

        $this->assertEquals($filterSet, $actualFileId->attributes()->all());
    }

    /**
     * @test
     */
    public function givenOneFilterSet_passAlsoOptions_applyMergedFiltersToFileId()
    {
        //given

        $filterSet = array(
            'quality' => 100,
            'thumbnail' => array(
                'size' => array(100, 100),
                'mode' => 'a'
            ),
        );
        $filterSets = array(
            'some_thumb' => $filterSet,
        );

        //when

        $actualFileId = $this->createApplier($filterSets)->applyFilterSet(
            $this->createAnyFileId(),
            'some_thumb',
            array('thumbnail' => array('mode' => 'b'))
        );

        //then

        $expectedFileAttributes = $filterSet;
        $expectedFileAttributes['thumbnail']['mode'] = 'b';
        $this->assertEquals($expectedFileAttributes, $actualFileId->attributes()->all());
    }

    /**
     * @test
     */
    public function givenOneFilterSet_passAlsoOptionsForAnotherFilter_applyMergedFiltersToFileId()
    {
        //given

        $filterSet = array(
            'quality' => 100,
            'thumbnail' => array(
                'size' => array(100, 100),
            ),
        );
        $filterSets = array(
            'some_thumb' => $filterSet,
        );

        //when

        $customOptions = array('another_filter' => array('name' => 'value'));

        $actualFileId = $this->createApplier($filterSets)->applyFilterSet(
            $this->createAnyFileId(),
            'some_thumb',
            $customOptions
        );

        //then

        $expectedFileAttributes = $customOptions + $filterSet;
        $this->assertEquals($expectedFileAttributes, $actualFileId->attributes()->all());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function passUnexistedFilterSet_throwEx()
    {
        //given

        $filterSet = array(
            'thumbnail' => array(),
        );

        //when

        $this->createApplier($filterSet)->applyFilterSet($this->createAnyFileId(), 'unexisted_filter');
    }

    /**
     * @test
     */
    public function givenFilterWithScalarOptions_passValueForTheFilter_usePassedValue()
    {
        //given

        $filterSet = array(
            'quality' => 100
        );
        $filterSets = array(
            'some_thumb' => $filterSet,
        );

        $customOptions = array('quality' => 90);

        //when

        $actualFileId = $this->createApplier($filterSets)->applyFilterSet(
            $this->createAnyFileId(),
            'some_thumb',
            array('quality' => 90)
        );

        //then

        $this->assertEquals($customOptions, $actualFileId->attributes()->all());
    }

    /**
     * @return FileId
     */
    private function createAnyFileId()
    {
        return new FileId('id');
    }

    /**
     * @param $filterSets
     * @return DefaultFilterSetApplier
     */
    private function createApplier($filterSets)
    {
        return new DefaultFilterSetApplier($filterSets);
    }
}
 