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
    protected static $fixture_file = 'element-slideshow.yml';

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
    public function testGetSummary()
    {
        $object = $this->objFromFixture(ElementSlideshow::class, 'one');
        $result = 'A Slide Image';
        $this->assertEquals($object->getSummary(), $result);
    }

    /**
     *
     */
    public function testGetType()
    {
        $object = $this->objFromFixture(ElementSlideshow::class, 'one');
        $this->assertEquals($object->getType(), 'Slideshow');
    }

    /**
     * A slideshow with slides renders the markup from dynamic/flexslider's
     * templates/Includes/FlexSlider.ss.
     */
    public function testRenderIncludesFlexSlider()
    {
        $object = $this->objFromFixture(ElementSlideshow::class, 'one');
        $this->assertTrue(
            $object->getSlideShow()->exists(),
            'Precondition: the fixture element has slides.'
        );

        $output = $object->forTemplate();

        $this->assertStringContainsString('class="flexslider"', $output);
        $this->assertStringContainsString('<ul class="slides">', $output);
    }

    /**
     * A slideshow without slides renders its own content and skips the flexslider markup.
     */
    public function testRenderWithoutSlides()
    {
        $object = ElementSlideshow::create();
        $object->Title = 'Empty slideshow';
        $object->Content = '<p>Intro copy</p>';
        $object->write();

        $this->assertFalse(
            $object->getSlideShow()->exists(),
            'Precondition: the element has no slides.'
        );

        $output = $object->forTemplate();

        $this->assertStringContainsString('Intro copy', $output);
        $this->assertStringNotContainsString('class="flexslider"', $output);
    }
}
