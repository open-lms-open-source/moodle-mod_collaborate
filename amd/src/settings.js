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
 * Settings JS.
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2017 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/str'], function($, str) {

    return {
        /**
         * Initialising function
         * @param {int} contextId
         */
        init: function(contextId) {

            var modified = false,
                initialised = false,
                strings = {};

            /**
             * Check that settings fields have been completed.
             * @param {array} flds
             * @returns {boolean}
             */
            var checkFieldsComplete = function(flds) {
                for (var f in flds) {
                    var fld = flds[f];
                    var val = '' + $('#id_s_collaborate_'+fld).val().trim();
                    if (val === '') {
                        return false;
                    }
                }
                return true;
            };

            /**
             * Check that REST settings have been completed.
             * @returns {boolean}
             */
            var checkRESTFieldsComplete = function() {
                var flds = ['restserver', 'restkey', 'restsecret'];
                return checkFieldsComplete(flds);
            };

            /**
             * Check that SOAP settings have been compelted.
             * @returns {boolean}
             */
            var checkSOAPFieldsComplete = function() {
                var flds = ['server', 'username', 'password'];
                return checkFieldsComplete(flds);
            };

            /**
             * Render new api status message.
             *
             * @param string stringKey
             * @param string alertClass
             * @param string extraClasses
             */
            var apiMsg = function(stringKey, alertClass, extraClasses) {
                var msg = strings[stringKey];

                var msgContainer = $('#api_diag .noticetemplate_' + alertClass).children().first().clone();

                $(msgContainer).addClass(extraClasses);
                $(msgContainer).html('<span class="api-connection-msg">' + msg + '</span>');

                // Wipe out existing connection status msg container.
                $('#api_diag .api-connection-status').empty();

                // Put in new msg container.
                $('#api_diag .api-connection-status').append($(msgContainer));
            };

            /**
             * Test api.
             */
            var testApi = function() {

                apiMsg('verifyingapi', 'message', 'spinner');

                var data;

                if (checkRESTFieldsComplete()) {
                    data = {
                        'server': $('#id_s_collaborate_restserver').val().trim(),
                        'restkey': $('#id_s_collaborate_restkey').val().trim(),
                        'restsecret': $('#id_s_collaborate_restsecret').val() // Never trim secrets!
                    };
                } else {
                    data = {
                        'server': $('#id_s_collaborate_server').val().trim(),
                        'username': $('#id_s_collaborate_username').val().trim(),
                        'password': $('#id_s_collaborate_password').val() // Never trim passwords!
                    };
                }
                data.contextid = contextId;

                $.ajax({
                    url: M.cfg.wwwroot + '/mod/collaborate/testapi.php',
                    context: document.body,
                    data: data,
                    success: function(data) {
                        if (data.success) {
                            if (!modified) {
                                apiMsg('connectionverified', 'success');
                            } else {
                                apiMsg('connectionverifiedchanged', 'success');
                            }
                        } else {
                            apiMsg('connectionfailed', 'problem');
                        }
                    },
                    error: function() {
                        apiMsg('connectionfailed', 'problem');
                    }
                });
            };

            /**
             * Apply listener for api test button.
             *
             * @author Guy Thomas
             */
            var applyClickApiTest = function() {
                $('.api_diag_btn').click(function(e) {
                    e.preventDefault();
                    testApi();
                });
            };

            /**
             * Apply listener for when settings changed.
             *
             * @author Guy Thomas
             */
            var applySettingChangeCheck = function() {
                var settingfields = '#id_s_collaborate_server, #id_s_collaborate_username, #id_s_collaborate_password';
                $(settingfields).keypress(function() {
                    modified = true;
                });
            };


            str.get_strings([
                {key: 'connectionfailed', component: 'mod_collaborate'},
                {key: 'connectionverified', component: 'mod_collaborate'},
                {key: 'verifyingapi', component: 'mod_collaborate'},
                {key: 'connectionstatusunknown', component: 'mod_collaborate'}
            ]).then(function(s){

                strings.connectionfailed = s[0];
                strings.connectionverified = s[1];
                strings.verifyingapi = s[2];
                strings.connectionstatusunknown = s[3];

                if (!initialised) {
                    apiMsg('connectionstatusunknown', 'message');
                }
                applySettingChangeCheck();
                applyClickApiTest();

                if (checkSOAPFieldsComplete() !== '' || checkRESTFieldsComplete() !== '') {
                    testApi();
                }
                // For IE / Edge, disable fieldset fields.
                if (/Edge\/\d./i.test(navigator.userAgent)
                    || /MSIE/i.test(navigator.userAgent)
                    || /Trident/i.test(navigator.userAgent)) {
                    $('fieldset[disabled="true"] input').attr('disabled', 'true');
                }

                // If REST settings not complete then reveal SOAP settings.
                if (!checkRESTFieldsComplete()) {
                    $('.soapapisettings').css('display', 'block');
                }

                initialised = true;
            });


        }
    };
});

