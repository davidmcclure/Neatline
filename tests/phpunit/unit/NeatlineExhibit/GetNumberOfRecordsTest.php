<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=76; */

/**
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class NeatlineExhibitTest_GetNumberOfRecords extends Neatline_Case_Default
{


    /**
     * `getNumberOfRecords` should return the number of records.
     */
    public function testGetNumberOfRecords()
    {
        $exhibit = $this->_exhibit();
        $record1 = $this->_record($exhibit);
        $this->assertEquals($exhibit->getNumberOfRecords(), 1);
        $record2 = $this->_record($exhibit);
        $this->assertEquals($exhibit->getNumberOfRecords(), 2);
        $record3 = $this->_record($exhibit);
        $this->assertEquals($exhibit->getNumberOfRecords(), 3);
    }


}
