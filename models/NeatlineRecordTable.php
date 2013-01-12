<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=76; */

/**
 * Table class for Neatline data records.
 *
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class NeatlineRecordTable extends Omeka_Db_Table
{


    /**
     * Select `coverage` as plain-text.
     *
     * @return Omeka_Db_Select The modified select.
     */
    public function getSelect()
    {
        return parent::getSelect()->columns(array(
            'coverage' => new Zend_Db_Expr(
                'NULLIF(AsText(coverage), "POINT(0 0)")'
            )
        ));
    }


    /**
     * Count the number of active records in an exhibit.
     *
     * @param NeatlineExhibit $exhibit The exhibit record.
     * @return int The number of active records.
     */
    public function countActiveRecordsByExhibit($exhibit)
    {
        return $this->count(array(
            'exhibit_id' => $exhibit->id,
            'map_active' => 1
        ));
    }


    /**
     * Construct data array for individual record.
     *
     * @param int $id The record id.
     * @return array The record data.
     */
    public function queryRecord($id)
    {
        return $this->fetchObject(
            $this->getSelect()->where('id=?', $id)
        )->buildJsonData();
    }


    /**
     * Construct records array for exhibit and editor.
     *
     * @param NeatlineExhibit $exhibit The exhibit record.
     * @param string $extent The viewport extent.
     * @param int $zoom The zoom level.
     * @return array The collection of records.
     */
    public function queryRecords($exhibit, $extent=null, $zoom=null)
    {

        $data = array();
        $select = $this->getSelect()->where(
            'exhibit_id=?', $exhibit->id
        );

        // Zoom.
        if (!is_null($zoom)) {
            $select = $this->_filterByZoom($select, $zoom);
        }

        // Extent.
        if (!is_null($extent)) {
            $select = $this->_filterByExtent($select, $extent);
        }

        // Get records.
        if ($records = $this->fetchObjects($select)) {
            foreach ($records as $record) {
                $data[] = $record->buildJsonData();
            }
        }

        return $data;

    }


    /**
     * Filter by zoom.
     *
     * @param Omeka_Db_Select $select The starting select.
     * @param integer $zoom The zoom level.
     * @return Omeka_Db_Select The filtered select.
     */
    protected function _filterByZoom($select, $zoom)
    {
        $select->where('min_zoom IS NULL OR min_zoom<=?', $zoom);
        $select->where('max_zoom IS NULL OR max_zoom>=?', $zoom);
        return $select;
    }


    /**
     * Filter by extent.
     *
     * @param Omeka_Db_Select $select The starting select.
     * @param string $extent The extent, as a WKT polygon.
     * @return Omeka_Db_Select The filtered select.
     */
    protected function _filterByExtent($select, $extent)
    {

        // Query for viewport intersection.
        $select->where(new Zend_Db_Expr('MBRIntersects(
            coverage, GeomFromText("'.$extent.'")
        )'));

        // Omit records at POINT(0 0).
        $select->where(new Zend_Db_Expr(
            'AsText(coverage) != "POINT(0 0)"'
        ));

        return $select;

    }


}
