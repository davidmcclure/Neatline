<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=76; */

/**
 * Field set/get tests for NeatlineExhibit.
 *
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class Neatline_NeatlineExhibitTest_FieldAccess
    extends Neatline_Test_AppTestCase
{


    /**
     * Test field set and get.
     */
    public function testFieldAccess()
    {

        // Create exhibit.
        $exhibit = new NeatlineExhibit();

        // Set.
        $exhibit->creator_id    = 1;
        $exhibit->tag_id        = 2;
        $exhibit->title         = '3';
        $exhibit->description   = '4';
        $exhibit->slug          = '5';
        $exhibit->public        = 6;
        $exhibit->query         = '7';
        $exhibit->map_focus     = '8';
        $exhibit->map_zoom      = 9;
        $exhibit->__save();

        // Reload.
        $exhibit = $this->_exhibitsTable->find($exhibit->id);

        // Get.
        $this->assertNotNull($exhibit->added);
        $this->assertNotNull($exhibit->modified);
        $this->assertEquals($exhibit->creator_id,   1);
        $this->assertEquals($exhibit->tag_id,       2);
        $this->assertEquals($exhibit->title,        '3');
        $this->assertEquals($exhibit->description,  '4');
        $this->assertEquals($exhibit->slug,         '5');
        $this->assertEquals($exhibit->public,       6);
        $this->assertEquals($exhibit->query,        '7');
        $this->assertEquals($exhibit->map_focus,    '8');
        $this->assertEquals($exhibit->map_zoom,     9);

    }


}