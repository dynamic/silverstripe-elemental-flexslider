<?php

namespace Dynamic\Elements\Flexslider\ORM;

use Dynamic\Elements\Flexslider\Elements\ElementSlideshow;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

class ElementSlideshowSlideDataExtension extends DataExtension
{
    /**
     * @var array
     */
    private static $has_one = array(
        'SlideshowElement' => ElementSlideshow::class,
    );

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName(array(
            'SlideshowElementID',
        ));
    }
}
