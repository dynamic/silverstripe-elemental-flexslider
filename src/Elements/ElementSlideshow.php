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
    private static $icon = 'font-icon-block-carousel';

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

    /**
     * Set to false to prevent an in-line edit form from showing in an elemental area. Instead the element will be
     * clickable and a GridFieldDetailForm will be used.
     *
     * @config
     * @var bool
     */
    private static $inline_editable = false;

    /**
     * @return FieldList
     */
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
    public function getSummary()
    {
        if ($this->Slides()->count() == 1) {
            $slide = ' slide';
        } else {
            $slide = ' slides';
        }
        return DBField::create_field('HTMLText', $this->Slides()->count() . $slide)->Summary(20);
    }

    /**
     * @return array
     */
    protected function provideBlockSchema()
    {
        $blockSchema = parent::provideBlockSchema();
        $blockSchema['content'] = $this->getSummary();
        return $blockSchema;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return _t(__CLASS__.'.BlockType', 'Slideshow');
    }
}
