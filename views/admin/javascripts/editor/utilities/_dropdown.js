/*
 * Worker class that provides basic dropdown functionality for configuration
 * menus across the top bar of the editor.
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

(function($, undefined) {


    $.widget('neatline.dropdown', {

        options: {

            // Markup hooks.
            markup: {
                content: 'div.dropdown-content',
                topbar: '#topbar'
            }

        },

        /*
         * Get and prepare markup, run start-up routine.
         */
        _create: function() {

            // Getters.
            this._body =            $('body');
            this.topbar =           $(this.options.markup.topbar);

            // Get, detach, and re-append the content div.
            this.content = this.element.next(this.options.markup.content);
            this.content.detach();
            $('body').append(this.content);

            // Trackers.
            this._expanded = false;

            // Start-up.
            this._measure();
            this._addEventsToButton();

        },

        /*
         * Measure markup.
         */
        _measure: function() {

            this.contentWidth =     this.content.width();
            this.contentHeight =    this.content.height();
            this.topbarHeight =     this.topbar.height();
            this.buttonWidth =      this.element.width();

        },

        /*
         * Measure markup offsets.
         */
        _getOffsets: function() {

            this.buttonOffset = this.element.offset();

        },

        /*
         * Listen for mousedown and click on the dropdown tab.
         */
        _addEventsToButton: function() {

            var self = this;

            // Listen for mousedown, suppress click.
            this.element.bind({

                'mousedown': function() {

                    if (this._expanded) { this.hide(); }
                    else { this.show(); }

                },

                'click': function(e) {
                    e.preventDefault();
                }

            });

        },

        /*
         * Position the dropdown relative to the button.
         */
        position: function() {

            // Update offsets.
            this._getOffsets();

            // If hidden.
            if (!this._expanded) {

                // Calculate the new top offset.
                var topOffset = this.contentHeight - this.topbarHeight;

                // Manifest new position.
                this.content.css({
                    'left': this.buttonOffsetleft + this.buttonWidth - this.contentWidth,
                    'top': -(topOffset)
                });

            }

            // If visible.
            else {

                // Calculate the new top offset.
                var topOffset = this.topbarHeight;

                // Manifest new position.
                this.content.css({
                    'left': this.buttonOffsetleft + this.buttonWidth - this.contentWidth,
                    'top': topOffset
                });

            }

        },

        /*
         * Display the dropdown.
         */
        show: function() {

            this._expanded = true;

        },

        /*
         * Hide the dropdown.
         */
        hide: function() {

            this._expanded = false;

        },

    });


})( jQuery );
