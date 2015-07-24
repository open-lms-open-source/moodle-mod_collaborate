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
 * English strings for collaborate mod.
 *
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['apidiagnostics'] = 'API diagnostics';
$string['apidiagnosticsavenotice'] = 'Note: You must submit this form before running the api test';
$string['attendance'] = 'Attendance';
$string['boundaryminutes'] = 'Boundary Time';
$string['chair'] = 'Chair';
$string['collaborate'] = 'Collaborate';
$string['collaborate:addinstance'] = 'Add Collaborate instance';
$string['collaborate:moderate']  = 'Moderate Collaborate instance';
$string['collaborate:participate']  = 'Join Collaborate instance';
$string['collaborate:view'] = 'View Collaborate instance';
$string['collaborate:viewattendance'] = 'View Attendance';
$string['collaboratefieldset'] = 'Custom example fieldset';
$string['collaboratename'] = 'Session name';
$string['collaboratename_help'] = 'This is the content of the help tooltip associated with the collaboratename field. Markdown syntax is supported.';
$string['completionlaunch'] = 'Student must launch the collaborate session to complete it';
$string['configlogging'] = 'Logging';
$string['configloggingdesc'] = 'Logging levels - determines how much data is logged for debugging purposes. Note: This is different to moodle event based logs which are not designed for logging errors, soap calls, etc..';
$string['configpassword'] = 'Password';
$string['configserver'] = 'Server URL';
$string['configserverdesc'] = 'The server your Collaborate sessions are created on.';
$string['configusername'] = 'Username';
$string['configwsdebug'] = 'Web Services Debugging';
$string['configwsdebugdesc'] = 'Turn on Web Services debugging: useful when you are receiving Fault errors using this module but prints out a lot of extra information';
$string['configwsdl'] = 'WSDL URL';
$string['configwsdldesc'] = 'Default .wsdl to use when creating a new Blackboard Collaborate session.';
$string['connectionfailed'] = 'API connection failed';
$string['connectionverified'] = 'API connection verified';
$string['crontask'] = 'Collaborate scheduled task';
$string['debugging'] = 'Debugging';
$string['duration'] = 'Duration';
$string['ends'] = 'Ends - {$a}';
$string['error:apicallfailed'] = 'API call failed ({$a})';
$string['error:apifailure'] = 'An error occurred whilst talking to the collaborate server - please try again later. If the problem persists, please contact support.';
$string['error:failedtocreateurl'] = 'Failed to generate url for meeting';
$string['error:failedtodeletesession'] = 'Failed to delete collaborate session';
$string['error:invalidmoduleid'] = 'You must specify a valid course_module ID or an instance ID';
$string['error:noconfiguration'] = 'Module not configured - please contact an administrator';
$string['error:serviceunreachable'] = 'WSDL unreachable';
$string['error:unknownaction'] = 'Unknown action';
$string['eventsessionlaunched'] = 'Collab session launched';
$string['exitapidiagnostics'] = 'Exit API diagnostics';
$string['hour'] = '1 Hour';
$string['hourminutes'] = '1 Hour and {$a->minutes} Minutes';
$string['hours'] = '{$a} Hours';
$string['hoursminutes'] = '{$a->hours} Hours and {$a->minutes} Minutes';
$string['joiningmeeting'] = 'Joining Meeting';
$string['lastjoined'] = 'Last Joined: {$a}';
$string['lastleft'] = 'Last Left: {$a}';
$string['log:all'] = 'All';
$string['log:light'] = 'Light - Emergency|Alert|Critical';
$string['log:medium'] = 'Medium - Emergency|Alert|Critical|Error|Warning';
$string['log:none'] = 'None';
$string['logging'] = 'Logging';
$string['meetingtimecurrent'] = 'Meeting in progress';
$string['meetingtimejoin'] = 'Join meeting';
$string['meetingtimepast'] = 'Meeting ended';
$string['minutes'] = '{$a} Minutes';
$string['modulename'] = 'Collaborate';
$string['modulename_help'] = 'Use Blackboard Collaborate with the Ultra experience to connect with one student or your entire class. Create virtual classrooms, offices and meeting spaces to engage your students in a more collaborative and interactive learning experience.';
$string['modulenameplural'] = 'Collaborate instances';
$string['noguestentry'] = 'Sorry, guests are not allowed to take part in discussions';
$string['openended'] = 'Duration of course';
$string['pluginadministration'] = 'Collaborate administration';
$string['pluginname'] = 'Collaborate';
$string['sessionstart'] = 'Start';
$string['sessionstarthelp'] = 'Participants can join the meeting 15 minutes before it starts';
$string['starts'] = 'Starts - {$a}';
$string['testapi'] = 'Test API';
$string['timezone'] = 'Timezone: {$a}';
$string['unrestored'] = 'A moderator must enter the session before you can access it.';

