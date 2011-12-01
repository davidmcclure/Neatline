/*
 * Manager class for the dropdown item edit forms in the Neatline editor.
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


    $.widget('neatline.itemform', {

        options: {

            // CSS constants.
            css: {
                form_duration: 300
            },

            // Hexes.
            colors: {
                light_blue: '#f3f6ff',
                light_yellow: '#fffef8',
                dark_purple: '#4f1d6a',
                purple: '#724e85',
                text: '#515151',
                gray: '#8d8d8d',
                red: '#ca3c3c',
                orange: '#ffda82'
            }

        },

        /*
         * Get and measure markup.
         */
        _create: function() {

            // Getters.
            this.form =                     this.element.find('form');
            this.cancelButton =             this.form.find('button[type="reset"]');
            this.saveButton =               this.form.find('input[type="submit"]');
            this.title =                    this.form.find('input[name="title"]');
            this.description =              this.form.find('textarea[name="description"]');
            this.startDate =                this.form.find('input[name="start-date-date"]');
            this.startTime =                this.form.find('input[name="start-date-time"]');
            this.endDate =                  this.form.find('input[name="end-date-date"]');
            this.endTime =                  this.form.find('input[name="end-date-time"]');
            this.endTime =                  this.form.find('input[name="end-date-time"]');
            this.color =                    this.form.find('input[name="color"]');
            this.leftPercent =              this.form.find('input[name="left-ambiguity-percentage"]');
            this.rightPercent =             this.form.find('input[name="right-ambiguity-percentage"]');
            this.closeButton =              this.form.find('button[type="reset"]');
            this.saveButton =               this.form.find('input[type="submit"]');
            this.textInputs =               this.form.find('input[type="text"], textarea');
            this.ambiguity =                this.form.find('.date-ambiguity-container');

            // Preparatory routines.
            this._measureForm();
            this._instantiateWidgets();

        },

        /*
         * Instantiate the ambiguity sliders and color picker.
         */
        _instantiateWidgets: function() {

            var self = this;

            // Gradient builder.
            this.ambiguity.gradientbuilder({

                'stopHandleDrag': function(event, obj) {

                    self._trigger('ambiguityChange', {}, {
                        'color': obj.color,
                        'leftPercent': obj.leftPercent,
                        'rightPercent': obj.rightPercent
                    });

                }

            });

            // Color picker.
            this.color.miniColors({

                'change': function(hex, rgb) {

                    // Trigger out, change gradient.
                    if (!self._opened) self._trigger('formEdit');
                    self._trigger('coloredit', {}, { 'color': hex });
                    self.ambiguity.gradientbuilder('setColor', hex);

                }

            });

        },


        /*
         * =================
         * Public methods.
         * =================
         */


        /*
         * Expand and gloss an item edit form.
         */
        showForm: function(item, scrollMap, scrollTimeline, focusItems) {

            // Getters and setters.
            this.item =                     item;
            this.container =                this.item.next('tr').find('td');
            this.textSpan =                 this.item.find('.item-title-text');

            // Inject the form markup.
            this.container.append(this.element);

            // DOM touches.
            this._getFormData();
            this._showContainer();
            this._expandTitle();
            this._buildFormFunctionality();

        },

        /*
         * Collapse an item edit form and unbind all events.
         */
        hideForm: function(item, immediate) {

            // DOM touches.
            this._hideContainer(immediate);
            this._contractTitle();

        },

        /*
         * Pluck data from form, get geocoverage data, build ajax request and
         * send data for save.
         */
        saveItemForm: function() {

            console.log('save');

        },

        /*
         * Expand/contract the height of an open item form. Called after a width
         * drag on the container div that might affect the wrapped height of the
         * form contents.
         */
        resizeForm: function() {

            console.log('resize');

        },


        /*
         * =================
         * DOM touches.
         * =================
         */


        /*
         * Expand the item form.
         */
        _showContainer: function() {

            // Display the form and zero the height.
            this.container.css('display', 'table-cell');
            this.element.css('visibility', 'visible');
            this.form.css('height', 0);

            // Animate up the height.
            this.form.animate({
                'height': this._nativeHeight
            }, this.options.css.form_duration);

         },

        /*
         * Contract the item form.
         */
        _hideContainer: function(immediate) {

            var self = this;

            // Get the duration.
            var duration = immediate ? 0 : this.options.css.form_duration;

            // Animate down.
            this.form.animate({ 'height': 0 }, duration, function() {
                self.container.css('display', 'none');
            });

         },

        /*
         * Grow title size, set color depending on whether the form has been
         * saved or not.
         */
        _expandTitle: function() {

            // By default, fade to the default text color and weight.
            var textColor = this.options.colors.dark_purple;

            // Keep the title bold red if the form was not saved.
            if (this.textSpan.data('changed')) {
                textColor = this.options.colors.red;
            }

            // Highlight the item title.
            this.textSpan.stop().animate({
                'color': textColor,
                'font-size': 14,
                'font-weight': 'bold'
            }, 100);

         },

        /*
         * Shrink title size, set color depending on whether the form has been
         * saved or not.
         */
        _contractTitle: function() {

            // By default, fade to the default text color and weight.
            var textColor = this.options.colors.text;
            var textWeight = 'normal';

            // Keep the title bold red if the form was not saved.
            if (this.textSpan.data('changed')) {
                textColor = this.options.colors.red;
                textWeight = 'bold';
            }

            // Highlight the item title.
            this.textSpan.stop().animate({
                'color': textColor,
                'font-size': 12,
                'font-weight': textWeight
            }, 100);

         },

        /*
         * Push the form data into the input fields.
         */
        _applyData: function(data) {

            // Populate inputs.
            this.title.attr('value', data.title);
            this.color.attr('value', data.vector_color);
            this.leftPercent.attr('value', data.left_percent);
            this.rightPercent.attr('value', data.right_percent);
            this.startDate.attr('value', data.start_date);
            this.startTime.attr('value', data.start_time);
            this.endDate.attr('value', data.end_date);
            this.endTime.attr('value', data.end_time);
            this.description.text(data.description);

            // Set the gradient builder color.
            this.ambiguity.gradientbuilder(
                'setColor',
                data.vector_color);

            // Reposition the draggers.
            this.ambiguity.gradientbuilder(
                'positionMarkers',
                data.left_percent,
                data.right_percent);

            // Push the new color onto the picker. Need to set the global
            // _opened tracker to circumvent miniColors' automatic firing of
            // the change callback on value set.
            this._opened = true;
            this.color.miniColors('value', data.vector_color);
            this._opened = false;

         },

        /*
         * Add event listeners to the form elements, instantiate the color
         * picker and ambiguity widget.
         */
        _buildFormFunctionality: function() {

            var self = this;

            // On keydown in any of the text fields, trigger change event.
            this.textInputs.bind('keydown', function() {
                self._trigger('formEdit');
            });

            // Close button.
            this.closeButton.bind({

                'mousedown': function() {
                    self._trigger('hide');
                },

                'click': function(event) {
                    event.preventDefault();
                }

            });

            // Save button.
            this.saveButton.bind({

                'mousedown': function() {
                    self._trigger('save');
                },

                'click': function(event) {
                    event.preventDefault();
                }

            });

         },


        /*
         * =================
         * Ajax calls.
         * =================
         */


        /*
         * Get data to populate the form.
         */
        _getFormData: function() {

            var self = this;

            $.ajax({

                url: 'form',
                dataType: 'json',

                data: {
                    item_id: this.item.attr('recordid'),
                    neatline_id: Neatline.id
                },

                success: function(data) {

                    // Push the data into the form.
                    self._applyData(data);

                }

            });

        },


        /*
         * =================
         * Utilities.
         * =================
         */

        /*
         * Get the native height of the form.
         */
        _measureForm: function() {

            this._nativeHeight = this.form.height();

         }

    });

})( jQuery );
