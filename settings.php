<?php
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
 * Global settings for plugin.
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Blackboard Inc. (http://www.blackboard.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

use mod_collaborate\logging\constants;
use mod_collaborate\settings\setting_trimmed_configtext;
use mod_collaborate\settings\setting_statictext;

if ($ADMIN->fulltree) {

    // We have to require these classes even though they are autoloadable or we will get errors on upgrade.
    require_once(__DIR__.'/classes/settings/setting_statictext.php');
    require_once(__DIR__.'/classes/settings/setting_trimmed_configtext.php');

    if ($PAGE->pagetype === 'admin-setting-modsettingcollaborate') {
        $PAGE->requires->js_call_amd('mod_collaborate/settings', 'init', [$PAGE->context->id]);

        $renderer = $PAGE->get_renderer('mod_collaborate');
        $apitest = $renderer->api_diagnostics();

        $setting = new \admin_setting_heading('apidiagnostics', '', $apitest);
        $settings->add($setting);
    }

    $name = 'collaborate/apisettings';
    $setting = new \admin_setting_heading($name, get_string('apisettings', 'mod_collaborate'), '');
    $settings->add($setting);

    $name = 'collaborate/opensoapapisettings';
    $setting = new setting_statictext($name, '<fieldset class="soapapisettings" disabled="true">');
    $settings->add($setting);

    $name = 'collaborate/soapapisettings';
    $setting = new setting_statictext($name, '<h4>'.get_string('soapapisettings', 'mod_collaborate').'</h4>');;
    $settings->add($setting);

    $name = 'collaborate/server';
    $title = new \lang_string('configserver', 'collaborate');
    $description = new \lang_string('configserverdesc', 'collaborate');
    $default = '';
    $setting = new setting_trimmed_configtext($name, $title, $description, $default);
    $settings->add($setting);

    $name = 'collaborate/username';
    $title = new \lang_string('configusername', 'collaborate');
    $description = '';
    $default = '';
    $setting = new setting_trimmed_configtext($name, $title, $description, $default);
    $settings->add($setting);

    $name = 'collaborate/password';
    $title = new \lang_string('configpassword', 'collaborate');
    $description = '';
    $default = '';
    $setting = new \admin_setting_configpasswordunmask($name, $title, $description, $default);
    $settings->add($setting);

    $name = 'collaborate/closesoapapisettings';
    $setting = new setting_statictext($name, '</fieldset>');
    $settings->add($setting);

    $name = 'collaborate/openrestapisettings';
    $setting = new setting_statictext($name, '<fieldset class="restapisettings">');
    $settings->add($setting);

    $name = 'collaborate/restapisettings';
    $setting = new setting_statictext($name, '<h4>'.get_string('restapisettings', 'mod_collaborate').'</h4>');
    $settings->add($setting);

    $name = 'collaborate/restserver';
    $title = new \lang_string('configrestserver', 'collaborate');
    $description = new \lang_string('configrestserverdesc', 'collaborate');
    $default = '';
    $setting = new setting_trimmed_configtext($name, $title, $description, $default);
    $settings->add($setting);

    $name = 'collaborate/restkey';
    $title = new \lang_string('configrestkey', 'collaborate');
    $description = '';
    $default = '';
    $setting = new setting_trimmed_configtext($name, $title, $description, $default);
    $settings->add($setting);

    $name = 'collaborate/restsecret';
    $title = new \lang_string('configrestsecret', 'collaborate');
    $description = '';
    $default = '';
    $setting = new \admin_setting_configpasswordunmask($name, $title, $description, $default);
    $settings->add($setting);

    $name = 'collaborate/restmigration';
    $migratebutton = '<button class="btn btn-primary" disabled="true">'.
            get_string('configrestmigrate', 'mod_collaborate').'</button>';
    $setting = new setting_statictext($name, $migratebutton);
    $settings->add($setting);

    $name = 'collaborate/closerestapisettings';
    $setting = new setting_statictext($name, '</fieldset>');
    $settings->add($setting);

    // Add debugging settings.
    $name = 'collaborate/log';
    $setting = new \admin_setting_heading($name, get_string('debugging', 'mod_collaborate'), '');
    $settings->add($setting);

    $name = 'collaborate/wsdebug';
    $title = new lang_string('configwsdebug', 'collaborate');
    $description = new lang_string('configwsdebugdesc', 'collaborate');
    $checked = '1';
    $unchecked = '0';
    $default = $unchecked;
    $setting = new \admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
    $settings->add($setting);

    // Add log range.
    $name = 'collaborate/logrange';
    $title = new \lang_string('configlogging', 'collaborate');
    $description = new \lang_string('configloggingdesc', 'collaborate');
    $options = [
        constants::RANGE_NONE => get_string('log:none', 'mod_collaborate'),
        constants::RANGE_LIGHT => get_string('log:light', 'mod_collaborate'),
        constants::RANGE_MEDIUM => get_string('log:medium', 'mod_collaborate'),
        constants::RANGE_ALL => get_string('log:all', 'mod_collaborate'),
    ];
    $setting = new \admin_setting_configselect($name, $title, $description, 0, $options);
    $settings->add($setting);

    // Add Instructor settings.

    $checked = 1;
    $unchecked = 0;

    $name = 'collaborate/instructorsettings';
    $information = new lang_string('instructorsettings:toggledesc', 'collaborate');
    $setting = new \admin_setting_heading($name, get_string('instructorsettings', 'mod_collaborate'), $information);
    $settings->add($setting);

    $name = 'collaborate/instructorsettingstoggle';
    $title = new lang_string('instructorsettings:toggle', 'collaborate');
    $default = $checked;
    $setting = new \admin_setting_configcheckbox($name, $title, '', $default, $checked, $unchecked);
    $settings->add($setting);

    $name = 'collaborate/defaultsettings';
    $heading = new lang_string('instructorsettings:defaultsettings', 'mod_collaborate');
    $information = new lang_string('instructorsettings:defaultsettingsdesc', 'collaborate');
    $setting = new \admin_setting_heading($name, $heading, $information);
    $settings->add($setting);

    $name = 'collaborate/canpostmessages';
    $title = new lang_string('canpostmessages', 'collaborate');
    $description = new lang_string('canpostmessages', 'collaborate');
    $default = $checked;
    $setting = new \admin_setting_configcheckbox($name, $title, '', $default, $checked, $unchecked);
    $settings->add($setting);

    $name = 'collaborate/candownloadrecordings';
    $title = new lang_string('candownloadrecordings', 'collaborate');
    $description = new lang_string('candownloadrecordings', 'collaborate');
    $default = $checked;
    $setting = new \admin_setting_configcheckbox($name, $title, '', $default, $checked, $unchecked);
    $settings->add($setting);

    $name = 'collaborate/canannotatewhiteboard';
    $title = new lang_string('canannotatewhiteboard', 'collaborate');
    $description = new lang_string('canannotatewhiteboard', 'collaborate');
    $default = $unchecked;
    $setting = new \admin_setting_configcheckbox($name, $title, '', $default, $checked, $unchecked);
    $settings->add($setting);

    $name = 'collaborate/cansharevideo';
    $title = new lang_string('cansharevideo', 'collaborate');
    $description = new lang_string('cansharevideo', 'collaborate');
    $default = $unchecked;
    $setting = new \admin_setting_configcheckbox($name, $title, '', $default, $checked, $unchecked);
    $settings->add($setting);

    $name = 'collaborate/canshareaudio';
    $title = new lang_string('canshareaudio', 'collaborate');
    $description = new lang_string('canshareaudio', 'collaborate');
    $default = $unchecked;
    $setting = new \admin_setting_configcheckbox($name, $title, '', $default, $checked, $unchecked);
    $settings->add($setting);

    // Performance settings.
    $name = 'collaborate/performancesettings';
    $setting = new \admin_setting_heading($name, get_string('performancesettings', 'mod_collaborate'), '');
    $settings->add($setting);

    $name = 'collaborate/disablerecentactivity';
    $title = new lang_string('disablerecentactivity:toggle', 'collaborate');
    $description = new lang_string('disablerecentactivity:desc', 'collaborate');
    $default = $unchecked;
    $setting = new \admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
    $settings->add($setting);
}
