<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=76; */

/**
 * Row class for NeatlineRecord.
 *
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class NeatlineRecord extends Neatline_AbstractRecord
{


    public $item_id;            // INT(10) UNSIGNED NULL
    public $exhibit_id;         // INT(10) UNSIGNED NULL

    public $added;              // TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    public $modified;           // TIMESTAMP NULL

    public $title;              // MEDIUMTEXT NULL
    public $_title;             // MEDIUMTEXT NULL
    public $body;               // MEDIUMTEXT NULL
    public $_body;              // MEDIUMTEXT NULL
    public $coverage;           // GEOMETRY NOT NULL
    public $tags;               // TEXT NULL
    public $slug;               // VARCHAR(100) NULL


    private $styles = array();


    /**
     * Set foreign keys.
     *
     * @param Omeka_record $item The item record.
     * @param Omeka_record $exhibit The exhibit record.
     */
    public function __construct($exhibit = null, $item = null)
    {
        parent::__construct();
        if (!is_null($exhibit)) $this->exhibit_id = $exhibit->id;
        if (!is_null($item)) $this->item_id = $item->id;
    }


    /**
     * Add a key-value pair to `styles`.
     *
     * @param string $name The attribute name.
     * @param mixed $value The value.
     */
    public function __set($name, $value)
    {
        $this->styles[$name] = $value;
    }


    /**
     * Get style property.
     *
     * @param string $name The attribute name.
     * @param mixed $name The value.
     */
    public function __get($name)
    {
        return $this->styles[$name];
    }


    /**
     * Before saving, replace the raw value of `coverage` with the MySQL
     * expression to set the `GEOMETRY` value. If `coverage` is undefined,
     * use `POINT(0 0)` as a de facto "null" value (ignored in queries).
     *
     * @return array The array representation of the record fields.
     */
    public function toArrayForSave()
    {

        $fields = parent::toArrayForSave();

        // Add the coverage.
        if (!empty($fields['coverage'])) {
            $fields['coverage'] = new Zend_Db_Expr(
                "GeomFromText('{$fields['coverage']}')"
            );
        } else {
            $fields['coverage'] = new Zend_Db_Expr(
                "GeomFromText('POINT(0 0)')"
            );
        }

        return array_merge($fields, $this->styles);

    }


    /**
     * Set a field if the passed value is not null or ''.
     *
     * @param string $field The name of the fiel.
     * @param boolean $value The value to set.
     */
    public function setNotEmpty($field, $value)
    {
        if (is_string($value)) $value = trim($value);
        if ($value == '') $this->$field = null;
        else $this->$field = $value;
    }


    /**
     * Get the parent exhibit record.
     *
     * @return Omeka_record The parent exhibit.
     */
    public function getExhibit()
    {
        $exhibits = $this->getTable('NeatlineExhibit');
        return $exhibits->find($this->exhibit_id);
    }


    /**
     * Save form values.
     *
     * @param array $values The POST/PUT values.
     */
    public function saveForm($values)
    {
        foreach ($values as $k => $v) $this->setNotEmpty($k, $v);
        $this->save();
    }


    /**
     * Assemble record data for the front-end application.
     *
     * @return array The data array.
     **/
    public function buildJsonData() {
        $this->compile();
        return array_merge(parent::toArray(), $this->styles);
    }


    /**
     * Compile Omeka item references. Supported syntax:
     *
     * `[item:45]`
     * `[item:45:"Title"]`
     * `[item:45:files]`
     *
     * `[item:"Title"]`
     * `[item:files]`
     *
     **/
    public function compile() {

        $fields = array('title' => '_title', 'body' => '_body');
        get_view()->setScriptPath(VIEW_SCRIPTS_DIR);

        foreach ($fields as $src => $tar) {

            $this->$tar = $this->$src;

            // `[item:<id>]`
            $re = "/\[item:(?P<id>[0-9]+)\]/";
            preg_match_all($re, $this->$tar, $matches);

            foreach ($matches['id'] as $id) {
                $item = get_record_by_id('Item', $id);
                $text = all_element_texts($item);
                $re = "/\[item:{$id}\]/";
                $this->$tar= preg_replace($re, $text, $this->$tar);
            }

            // `[item:<id>:"<element>"]`
            $re = "/\[item:(?P<id>[0-9]+):\"(?P<el>[a-zA-Z\s]+)\"\]/";
            preg_match_all($re, $this->$tar, $matches);

            foreach ($matches['id'] as $i => $id) {
                $item = get_record_by_id('Item', $id);
                $element = $matches['el'][$i];
                $text = metadata($item, array('Dublin Core', $element));
                $re = "/\[item:[0-9]+:\"{$element}\"\]/";
                $this->$tar= preg_replace($re, $text, $this->$tar);
            }

        }

    }


}
