<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Index controller.
 *
 * PHP version 5
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at http://www.apache.org/licenses/LICENSE-2.0 Unless required by
 * applicable law or agreed to in writing, software distributed under the
 * License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS
 * OF ANY KIND, either express or implied. See the License for the specific
 * language governing permissions and limitations under the License.
 *
 * @package     omeka
 * @subpackage  neatline
 * @author      Scholars' Lab <>
 * @author      Bethany Nowviskie <bethany@virginia.edu>
 * @author      Adam Soroka <ajs6f@virginia.edu>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2011 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
?>

<?php

class Neatline_IndexController extends Omeka_Controller_action
{

    /**
     * Get table objects.
     *
     * @return void
     */
    public function init()
    {

        $this->_neatlinesTable = $this->getTable('NeatlineNeatline');
        $this->_mapsTable = $this->getTable('NeatlineMapsMap');
        $this->_timelinesTable = $this->getTable('NeatlineTimeTimeline');

    }

    /**
     * Redirect index route to browse.
     *
     * @return void
     */
    public function indexAction()
    {

        $this->_redirect('neatline-exhibits/browse');

    }

    /**
     * Show list of existing Neatlines.
     *
     * @return void
     */
    public function browseAction()
    {

        // Push pagination variables.
        $this->view->pagination = $this->_neatlinesTable
            ->getPaginationSettings($this->_request);

        // Push Neatlines.
        $this->view->neatlines = $this->_neatlinesTable
            ->getNeatlinesForBrowse($this->_request);

    }

    /**
     * Create a new Neatline.
     *
     * @return void
     */
    public function addAction()
    {

        $neatline = new NeatlineNeatline;

        // Try to create the Neatline if the form has been submitted.
        if ($this->_request->isPost()) {

            $_post = $this->_request->getPost();
            $errors = $neatline->validateForm($_post);

            // If no errors, save form and redirect.
            if (count($errors) == 0) {

                if ($neatline->saveForm($_post)) {
                    // redirect to the Neatline.
                }

                else {
                    $this->flashError(neatline_saveFail($neatline->name));
                }

            }

            else {
                $neatline->populateData($_post);
                $this->view->errors = $errors;
            }

        }

        // Bounce back if there are no maps or no timelines.
        if ($this->_mapsTable->count() == 0 &&
            $this->_timelinesTable->count() == 0) {

            $this->flashError(neatline_noMapsOrTimelinesErrorMessage());
            $this->_redirect('neatline-exhibits');

        }

        // Push Neatline object into view.
        $this->view->neatline = $neatline;

    }

    /**
     * Delete exhibits.
     *
     * @return void
     */
    public function deleteAction()
    {



    }

    /**
     * Facade for Neatline Maps browse.
     *
     * @return void
     */
    public function mapsAction()
    {



    }

    /**
     * Facade for Neatline Time browse.
     *
     * @return void
     */
    public function timelinesAction()
    {



    }

}
