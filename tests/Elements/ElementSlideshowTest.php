<?php

namespace Dynamic\Elements\Flexslider\Test\Elements;

use Dynamic\Elements\Flexslider\Elements\ElementSlideshow;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;

class ElementSlideshowTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = '../fixtures.yml';

    /**
     * Tests getCMSFields().
     */
    public function testGetCMSFields()
    {
        $object = $this->objFromFixture(ElementSlideshow::class, 'one');
        $this->assertInstanceOf(FieldList::class, $object->getCMSFields());
    }

    /**
     *
     */
    public function testGetElementSummary()
    {
        $object = $this->objFromFixture(ElementSlideshow::class, 'one');
        $this->assertEquals($object->ElementSummary(), $object->dbObject('Content')->Summary(20));
    }

    /**
     *
     */
    public function testGetType()
    {
        $object = $this->objFromFixture(ElementSlideshow::class, 'one');
        $this->assertEquals($object->getType(), 'Slideshow');
    }
}
