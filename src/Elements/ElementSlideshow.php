<?php

namespace Dynamic\Elements\Flexslider\Elements;

use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\FieldType\DBField;

/**
 * Class ElementSlideshow
 * @package Dynamic\Elements\Flexslider\Elements
 *
 * @property string Content
 */
class ElementSlideshow extends BaseElement
{
    /**
     * @var string
     */
    private static $icon = 'slideshow-icon';

    /**
     * @var string
     */
    private static $singular_name = 'Flexslider Element';

    /**
     * @var string
     */
    private static $plural_name = 'Flexslider Elements';

    /**
     * @var string
     */
    private static $table_name = 'ElementSlideshow';

    /**
     * @var array
     */
    private static $db = [
        'Content' => 'HTMLText',
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->dataFieldByName('Content')
                ->setRows(8);
        });

        return parent::getCMSFields();
    }

    /**
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     */
    public function ElementSummary()
    {
        return DBField::create_field('HTMLText', $this->Content)->Summary(20);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return _t(__CLASS__.'.BlockType', 'Slideshow');
    }
}
