// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Module javascript.
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2016 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module mod/collaborate
 */
define(['jquery'], function($) {

    /**
     * Does the browser support copying?
     * @returns {bool}
     */
    var canCopy = function() {
        // We have to test for either supported or enabled as this is not consistent across browsers.
        return document.queryCommandSupported('copy') ||
               document.queryCommandEnabled('copy');
    };

    /**
     * Deselect Text.
     */
    var deselectText = function() {
        if (document.selection) {
            // Internet Explorer clear selection.
            document.selection.empty();
        } else {
            // All other browsers clear selection.
            var selection = window.getSelection();
            selection.removeAllRanges();
        }
    };

    /**
     * Copy text of specific element
     * @param element
     * @constructor
     */
    var copyText = function(element) {
        // Based on code found at:
        // http://stackoverflow.com/questions/985272/selecting-text-in-an-element-akin-to-highlighting-with-your-mouse .
        if (element instanceof $) {
            element = element[0];
        }

        if (element.select) {
            element.select();
            document.execCommand('copy');
            deselectText();
            return;
        }

        var doc = document,
            range,
            selection;

        if (doc.body.createTextRange) {
            range = document.body.createTextRange();
            range.moveToElementText(element);
            range.select();
            document.execCommand('copy');
            deselectText();
        } else if (window.getSelection) {
            selection = window.getSelection();
            range = document.createRange();
            range.selectNodeContents(element);
            selection.removeAllRanges();
            selection.addRange(range);
            document.execCommand('copy');
            deselectText();
        }
    };

    /**
     * Apply "click to copy" events.
     */
    var applyClickToCopy = function() {
        if (canCopy()) {
            $('.copyablelink').addClass('enabled');
            $('.copyablelink button').click(function(e){
                e.preventDefault();
                copyText($(this).parent('form').find('input'));
                $(this).parent('form').addClass('copied');
                $(this).parent('form').find('div').focus();
            });
        }
    };

    return {init:function(){applyClickToCopy();}};
});
