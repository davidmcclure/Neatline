<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Row class for Neatline data record.
 *
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class NeatlineRecord extends Omeka_Record_AbstractRecord
{

    /**
     * The id of the parent item.
     * int(10) unsigned NULL
     */
    public $item_id;

    /**
     * The id of the parent exhibit.
     * int(10) unsigned NULL
     */
    public $exhibit_id;

    /**
     * The title for the record.
     * mediumtext COLLATE utf8_unicode_ci NULL
     */
    public $title;

    /**
     * An exhibit-unique plaintext identifier for the record.
     * varchar(100) NULL
     */
    public $slug;

    /**
     * A plaintext description for the record.
     * mediumtext COLLATE utf8_unicode_ci NULL
     */
    public $description;

    /**
     * The fill color for geometries.
     * tinytext COLLATE utf8_unicode_ci NULL
     */
    public $vector_color;

    /**
     * The line color for geometries.
     * tinytext COLLATE utf8_unicode_ci NULLL
     */
    public $stroke_color;

    /**
     * The highlight color for geometries.
     * tinytext COLLATE utf8_unicode_ci NULL
     */
    public $highlight_color;

    /**
     * The fill opacity for geometries.
     * int(10) unsigned NULL
     */
    public $vector_opacity;

    /**
     * The selected opacity for geometries.
     * int(10) unsigned NULL
     */
    public $select_opacity;

    /**
     * The line opacity for geometries.
     * int(10) unsigned NULL
     */
    public $stroke_opacity;

    /**
     * The opacity of points rendered as images.
     * int(10) unsigned NULL
     */
    public $graphic_opacity;

    /**
     * The width of lines on geometries.
     * int(10) unsigned NULL
     */
    public $stroke_width;

    /**
     * The radius of points on geometries.
     * int(10) unsigned NULL
     */
    public $point_radius;

    /**
     * The URL for a static to represent points.
     * tinytext COLLATE utf8_unicode_ci NULL
     */
    public $point_image;

    /**
     * KML for geometries.
     * mediumtext COLLATE utf8_unicode_ci NULL
     */
    public $geocoverage;

    /**
     * Default map focus position
     * varchar(100) NULL
     */
    public $map_focus;

    /**
     * Default map zoom level.
     * int(10) unsigned NULL
     */
    public $map_zoom;

    /**
     * Boolean for whether the record is present on the map.
     * tinyint(1) NULL
     */
    public $map_active;

    /**
     * The record's parent record (used for caching).
     * Omeka_Record_AbstractRecord
     */
    protected $_parent;

    /**
     * The record's parent exhibit (used for caching).
     * Omeka_Record_AbstractRecord
     */
    protected $_exhibit;

    /**
     * Default attributes.
     */
    private static $defaults = array(
        'left_percent' => 0,
        'right_percent' => 100,
        'geocoverage' => ''
    );

    /**
     * Valid style attribute names.
     */
    private static $styles = array(
        'vector_color',
        'vector_opacity',
        'stroke_color',
        'stroke_opacity',
        'stroke_width',
        'select_opacity',
        'graphic_opacity',
        'point_radius',
        'highlight_color'
    );

    /**
     * DC Date regular expression.
     */
    private static $dcDateRegex =
        '/^(?P<start>[0-9:\-\s]+)(\/(?P<end>[0-9:\-\s]+))?/';


    /**
     * Instantiate and foreign keys.
     *
     * @param Omeka_record $item The item record.
     * @param Omeka_record $neatline The exhibit record.
     *
     * @return Omeka_record $this.
     */
    public function __construct($item = null, $neatline = null)
    {

        parent::__construct();

        // If defined, set the item key.
        if (!is_null($item)) {
            $this->item_id = $item->id;
        }

        // If defined, set the item key.
        if (!is_null($neatline)) {
            $this->exhibit_id = $neatline->id;
        }

        $this->_parent = null;
        $this->_exhibit = null;

    }

    /**
     * Get the parent item record.
     *
     * @return Omeka_record $item The parent item.
     */
    public function getItem()
    {

        $item = null;

        // If record id is defined, get item.
        if (!is_null($this->item_id)) {
           $item = $this->getTable('Item')->find($this->item_id);
        }

        return $item;

    }

    /**
     * Get the parent exhibit record.
     *
     * @return Omeka_record $exhibit The parent exhibit.
     */
    public function getExhibit()
    {

        if (is_null($this->_exhibit)) {
            $this->_exhibit = $this->getTable('NeatlineExhibit')
                ->find($this->exhibit_id);
        }

        return $this->_exhibit;

    }

    /**
     * Construct array with data for editor form.
     *
     * @return JSON The data.
     */
    public function buildEditFormData()
    {
        return array();
    }


    /**
     * Setters.
     */


    /**
     * Set the an attribute if the passed value is not null or ''.
     *
     * @param string $attribute The name of the attribute.
     * @param boolean $value The value to set.
     *
     * @return void.
     */
    public function setNotEmpty($attribute, $value)
    {
        if ($value == '') $this[$attribute] = null;
        else $this[$attribute] = $value;
    }

    /**
     * Set the slug if it is unique.
     *
     * @param boolean $slug The slug.
     *
     * @return void.
     */
    public function setSlug($slug)
    {

        // Get records table.
        $_recordsTable = $this->getTable('NeatlineRecord');

        // Set the record value if it is unique.
        if ($_recordsTable->slugIsAvailable($this, $this->getExhibit(), $slug)) {
            $this->slug = $slug;
        }

    }

    /**
     * Set the geocoverage field if the passed value is not <string>'null', which
     * is true when there was not an instantiated map when the  triggering save
     * action was performed in the editor.
     *
     * @param integer $value The value.
     *
     * @return boolean True if the set succeeds.
     */
    public function setGeocoverage($value)
    {
        if ($value == 'null') return false;
        return $this->setNotEmpty('geocoverage', $value);
    }

    /**
     * Set a style attribute. If there is an exhibit default, only set
     * if the passed value is different. If there is no exhibit default,
     * only set if the passed value is different from the system
     * default. If a non-style column name is passed, return false.
     *
     * @param string style The name of the style.
     * @param mixed $value The value to set.
     *
     * @return boolean True if the set succeeds.
     */
    public function setStyle($style, $value)
    {
        $this[$style] = $value;
        return true;
    }

    /**
     * Set all style attributes to null.
     *
     * @return void.
     */
    public function resetStyles()
    {
        $this->vector_color =       null;
        $this->stroke_color =       null;
        $this->highlight_color =    null;
        $this->vector_opacity =     null;
        $this->stroke_opacity =     null;
        $this->graphic_opacity =    null;
        $this->stroke_width =       null;
        $this->point_radius =       null;
        $this->point_image =        null;
    }


    /**
     * Getters.
     */


    /**
     * Set the an attribute if the passed value is not null or ''.
     *
     * @param string $attribute The name of the attribute.
     * @param boolean $value The value to set.
     *
     * @return void.
     */
    public function getNotEmpty($attribute)
    {
        if (is_null($this[$attribute])) return '';
        else return $this[$attribute];
    }

    /**
     * Get a style attribute. In order or priority, return the row
     * value, exhibit default, or system default.
     *
     * @param string style The name of the style.
     *
     * @return mixed The value.
     */
    public function getStyle($style)
    {
        if (!is_null($this[$style])) return $this[$style];
    }

    /**
     * Return title.
     *
     * @return string $title The title.
     */
    public function getTitle()
    {

        // Return row-level value.
        if (!is_null($this->title)) return $this->title;

        // If there is a parent item.
        else if (!is_null($this->item_id)) {

            // Try to get DC title.
            return metadata($this->getItem(),
                array('Dublin Core', 'Title'));

        }

        else return '';

    }

    /**
     * For dropdown selects, strip HTML and truncate.
     *
     * @param integer length The max length.
     *
     * @return string $title The title.
     */
    public function getTitleForSelect($length=60)
    {

        // Get title, strip tags, truncate.
        $title = strip_tags($this->getTitle());
        $fixed = substr($title, 0, $length);

        // Trim title.
        if (strlen($title) > $length) $fixed .= ' ...';

        return $fixed;

    }

    /**
     * If there is a title return it; if not, try to return
     * the first portion of the description.
     *
     * @return string $title The title.
     */
    public function getTitleOrDescription()
    {

        // Return row-level value.
        $title = $this->getTitle();
        if ($title !== '') return $title;

        else {

            // Try to get a description.
            $description = $this->getDescription();
            if ($description !== '') return substr($description, 0, 200);
            else return __('[Untitled]');

        }

    }

    /**
     * Return slug.
     *
     * @return string $slug The slug.
     */
    public function getSlug()
    {
        return (!is_null($this->slug)) ? $this->slug : '';
    }

    /**
     * Return description.
     *
     * @return string $description The description.
     */
    public function getDescription()
    {

        // Return row-level value.
        if (!is_null($this->description)) return $this->description;

        // If there is a parent item.
        else if (!is_null($this->item_id)) {

            // Try to get a DC description.
            return metadata($this->getItem(),
                array('Dublin Core', 'Description'));

        }

        else return '';

    }

    /**
     * Return coverage.
     *
     * @return string The coverage data. If there is record-specific data,
     * return it. If not, and there is a parent Omeka item, try to get a non-
     * empty value from the DC coverage field.
     */
    public function getGeocoverage()
    {

        // Return local value if one exists.
        if (!is_null($this->geocoverage) && $this->geocoverage !== '') {
            return $this->geocoverage;
        }

        // Try to get DC value.
        else if (!is_null($this->item_id)) {

            // If Neatline Features is not installed.
            if (!plugin_is_active('NeatlineFeatures')) {

                // Get the DC coverage.
                $coverage = metadata(
                    $this->getItem(), array('Dublin Core', 'Coverage'));

                // Return if not empty, otherwise return default.
                return ($coverage !== '') ?
                    $coverage : self::$defaults['geocoverage'];

            }

            // If Neatline Features is installed.
            else {

                // Get feature records.
                $features = $this->getTable('NeatlineFeature')
                    ->getItemFeatures($this->getItem());

                // Walk features and build array.
                $wkt = array();
                foreach ($features as $feature) {

                    // Push wkt if not null or empty.
                    if (!is_null($feature->wkt) && $feature->wkt !== '') {
                        $wkt[] = $feature->wkt;
                    }

                    // If at least one feature exists, implode and return.
                    if (count($wkt)) return implode('|', $wkt);
                    else return self::$defaults['geocoverage'];

                }

            }

        }

        // Fall back on default string.
        else return self::$defaults['geocoverage'];

    }

    /**
     * On save, update the modified column on the parent exhibit.
     *
     * @return void.
     */
    public function save()
    {

        if (!is_null($this->exhibit_id)) {
            $exhibit = $this->getExhibit();
            $exhibit->save();
        }

        parent::save();

    }

    /**
     * This calls `delete` in a transaction.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function deleteTransaction()
    {
        $db = get_db();
        $db->beginTransaction();
        try {
            $this->delete();
            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    /**
     * Construct map data.
     *
     * @param array $index This is the index of NeatlineRecord objects for
     * caching. Optional.
     * @param array $wmss This is an index mapping item IDs to rows from the
     * NeatlineMapsService WMS data.
     *
     * @return array The map JSON.
     **/
    public function buildJsonData($index=array(), $wmss=array()) {

        $data = array(

            // Relations:
            'id'                  => $this->id,
            'item_id'             => $this->item_id,

            // Text:
            'title'               => $this->getTitle(),
            'description'         => $this->getDescription(),
            'slug'                => $this->getSlug(),

            // Styles:
            'vector_color'        => $this->getStyle('vector_color'),
            'stroke_color'        => $this->getStyle('stroke_color'),
            'highlight_color'     => $this->getStyle('highlight_color'),
            'vector_opacity'      => $this->getStyle('vector_opacity'),
            'select_opacity'      => $this->getStyle('select_opacity'),
            'stroke_opacity'      => $this->getStyle('stroke_opacity'),
            'graphic_opacity'     => $this->getStyle('graphic_opacity'),
            'stroke_width'        => $this->getStyle('stroke_width'),
            'point_radius'        => $this->getStyle('point_radius'),
            'point_image'         => $this->getNotEmpty('point_image'),

            // Map:
            'map_focus'           => $this->map_focus,
            'map_zoom'            => $this->map_zoom,
            'coverage'            => $this->getGeocoverage(),
            'wmsAddress'          => null,
            'layers'              => null,

            // Statuses:
            'map_active'          => $this->map_active

        );

        // If the record has a parent item and Neatline Maps is present.
        if (!is_null($this->item_id) && array_key_exists($this->item_id, $wmss)) {
            $wms = $wmss[$this->item_id];
            $data['wmsAddress'] = $wms['address'];
            $data['layers']     = $wms['layers'];
        }

        return $data;

    }

}