<?php

namespace Dynamic\Elements\Flexslider\Elements;

use DNADesign\Elemental\Models\BaseElement;
use Dynamic\FlexSlider\Model\SlideImage;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\TreeMultiselectField;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\Security\Group;
use SilverStripe\Security\InheritedPermissions;
use SilverStripe\Security\InheritedPermissionsExtension;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\Security\Security;

/**
 * Class ElementSlideshow
 * @package Dynamic\Elements\Flexslider\Elements
 *
 * @property string Content
 */
class ElementSlideshow extends BaseElement implements PermissionProvider
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
     * @var string[]
     */
    private static $extensions = [
        InheritedPermissionsExtension::class,
    ];

    /**
     * @var array
     */
    private static $owns = [
        'Slides',
    ];

    /**
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

        $labels['Content'] = _t(__CLASS__ . '.ContentLabel', 'Description');

        return $labels;
    }

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->removeByName([
                'ViewerGroups',
                'EditorGroups',
            ]);

            $fields->dataFieldByName('Content')
                ->setRows(5)
                ->setDescription(_t(
                    __CLASS__ . '.ContentDescription',
                    'optional. Add introductory copy to your slideshow.'
                ));

            $fields->addFieldsToTab(
                'Root.Settings',
                $this->getSettingsFields()
            );
        });

        return parent::getCMSFields();
    }

    /**
     * @return array
     */
    public function getSettingsFields()
    {
        $mapFn = function ($groups = []) {
            $map = [];
            foreach ($groups as $group) {
                // Listboxfield values are escaped, use ASCII char instead of &raquo;
                $map[$group->ID] = $group->getBreadcrumbs(' > ');
            }
            asort($map);
            return $map;
        };
        $viewAllGroupsMap = $mapFn(Permission::get_groups_by_permission(['SITETREE_VIEW_ALL', 'ADMIN']));

        $fields = [
            $viewersOptionsField = new OptionsetField(
                "CanViewType",
                _t(__CLASS__ . '.ACCESSHEADER', "Who can view this page?")
            ),
            $viewerGroupsField = TreeMultiselectField::create(
                "ViewerGroups",
                _t(__CLASS__ . '.VIEWERGROUPS', "Viewer Groups"),
                Group::class
            ),
        ];

        $viewersOptionsSource = [
            InheritedPermissions::INHERIT => _t(__CLASS__ . '.ACCESSINHERIT', 'Inherit'),
            InheritedPermissions::ANYONE => _t(__CLASS__ . '.ACCESSANYONE', "Anyone"),
            InheritedPermissions::LOGGED_IN_USERS => _t(__CLASS__ . '.ACCESSLOGGEDIN', "Logged-in users"),
            InheritedPermissions::ONLY_THESE_USERS => _t(
                __CLASS__ . '.ACCESSONLYTHESE',
                "Only these groups (choose from list)"
            ),
        ];
        $viewersOptionsField->setSource($viewersOptionsSource);


        if ($viewAllGroupsMap) {
            $viewerGroupsField->setDescription(_t(
                'SilverStripe\\CMS\\Model\\SiteTree.VIEWER_GROUPS_FIELD_DESC',
                'Groups with global view permissions: {groupList}',
                ['groupList' => implode(', ', array_values($viewAllGroupsMap))]
            ));
        }

        return $fields;
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
            ['count' => $count]
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
        return _t(__CLASS__ . '.BlockType', 'Slideshow');
    }


    /**
     * @return array
     */
    public function providePermissions()
    {
        return [
            'ElementalSlide_EDIT' => 'Slide Element Edit',
            'ElementalSlide_DELETE' => 'Slide Element Delete',
            'ElementalSlide_CREATE' => 'Slide Element Create',
            'ElementalSlide_VIEW' => 'Slide Element View',
        ];
    }

    /**
     * @param null $member
     * @param array $context
     *
     * @return bool|int
     */
    public function canCreate($member = null, $context = [])
    {
        return Permission::check('ElementalSlide_CREATE', 'any', $member);
    }

    /**
     * @param null $member
     * @param array $context
     *
     * @return bool|int
     */
    public function canEdit($member = null, $context = [])
    {
        return Permission::check('ElementalSlide_EDIT', 'any', $member);
    }

    /**
     * @param null $member
     * @param array $context
     *
     * @return bool|int
     */
    public function canDelete($member = null, $context = [])
    {
        return Permission::check('ElementalSlide_DELETE', 'any', $member);
    }

    /**
     * This function should return true if the current user can view this page. It can be overloaded to customise the
     * security model for an application.
     *
     * Denies permission if any of the following conditions is true:
     * - canView() on any extension returns false
     * - "CanViewType" directive is set to "Inherit" and any parent page return false for canView()
     * - "CanViewType" directive is set to "LoggedInUsers" and no user is logged in
     * - "CanViewType" directive is set to "OnlyTheseUsers" and user is not in the given groups
     *
     * @param Member $member
     * @return bool True if the current user can view this page
     * @uses DataExtension->canView()
     * @uses ViewerGroups()
     *
     */
    public function canView($member = null)
    {
        if (!$member) {
            $member = Security::getCurrentUser();
        }

        // Standard mechanism for accepting permission changes from extensions
        $extended = $this->extendedCan('canView', $member);
        if ($extended !== null) {
            return $extended;
        }

        // admin override
        if ($member && Permission::checkMember($member, ["ADMIN", "SITETREE_VIEW_ALL"])) {
            return true;
        }

        // Orphaned pages (in the current stage) are unavailable, except for admins via the CMS
        /*if ($this->isOrphaned()) {
            return false;
        }*/

        // Note: getInheritedPermissions() is disused in this instance
        // to allow parent canView extensions to influence subpage canView()

        // check for empty spec
        if (!$this->CanViewType || $this->CanViewType === InheritedPermissions::ANYONE) {
            return true;
        }

        // check for inherit
        if ($this->CanViewType === InheritedPermissions::INHERIT) {
            if ($this->ParentID) {
                return $this->Parent()->canView($member);
            } else {
                return $this->getSiteConfig()->canViewPages($member);
            }
        }

        // check for any logged-in users
        if ($this->CanViewType === InheritedPermissions::LOGGED_IN_USERS && $member && $member->ID) {
            return true;
        }

        // check for specific groups
        if ($this->CanViewType === InheritedPermissions::ONLY_THESE_USERS
            && $member
            && $member->inGroups($this->ViewerGroups())
        ) {
            return true;
        }

        return false;
    }
}
