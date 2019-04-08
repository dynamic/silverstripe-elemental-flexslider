<?php

namespace Dynamic\Elements\Flexslider\Elements;

use DNADesign\Elemental\Models\BaseElement;
use Dynamic\FlexSlider\Model\SlideImage;
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
     * @var string
     */
    private static $slide_tab_title = 'Main';

    /**
     * @param bool $includerelations
     * @return array
     */
    public function fieldLabels($includerelations = true)
    {
        $labels = parent::fieldLabels($includerelations);

        $labels['Content'] = _t(__CLASS__.'.ContentLabel', 'Description');

        return $labels;
    }

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->dataFieldByName('Content')
                ->setRows(5)
                ->setDescription(_t(
                    __CLASS__ . '.ContentDescription',
                    'optional. Add introductory copy to your slideshow.'
                ));
        });

        return parent::getCMSFields();
    }

    /**
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     */
    public function getSummary()
    {
        $count = $this->Slides()->count();
        $label = _t(
            SlideImage::class . '.PLURALS',
            '{count} Slide|{count} Slides',
            [ 'count' => $count ]
        );
        return DBField::create_field('HTMLText', $label)->Summary(20);
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
