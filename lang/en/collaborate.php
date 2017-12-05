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
// The apisettings language string matches the customer welcome email. Don't change unless the welcome email changes.
$string['apisettings'] = 'Production Moodle Integration';
$string['attendance'] = 'Attendance';
$string['boundaryminutes'] = 'Boundary Time';
$string['chair'] = 'Chair';
$string['cachedef_recordingcounts'] = 'Cache for Collaborate recording views and downloads';
$string['cleanupsessionstask'] = 'Clean up sessions task';
$string['collaborate'] = 'Collaborate';
$string['collaborate:addinstance'] = 'Add Collaborate instance';
$string['collaborate:deleterecordings'] = 'Delete recordings';
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
// The configpassword language string matches the customer welcome email. Don't change unless the welcome email changes.
$string['configpassword'] = 'Password';
$string['configrestkey'] = 'Key';
$string['configrestmigrate'] = 'Migrate to REST API';
$string['configrestsecret'] = 'Secret';
$string['configrestserver'] = 'REST server URL';
$string['configrestserverdesc'] = 'The REST server your Collaborate sessions are created on.';
// The configserver language string matches the customer welcome email. Don't change unless the welcome email changes.
$string['configserver'] = 'URL';
$string['configserverdesc'] = 'The (old) SOAP server your Collaborate sessions are created on.';
// The configusername language string matches the customer welcome email. Don't change unless the welcome email changes.
$string['configusername'] = 'Username';
$string['configwsdebug'] = 'Web Services Debugging';
$string['configwsdebugdesc'] = 'Turn on Web Services debugging: useful when you are receiving Fault errors using this module but prints out a lot of extra information';
$string['connectionfailed'] = 'Connection failed - please check credentials';
$string['connectionverified'] = 'Credentials verified';
$string['connectionstatusunknown'] = 'Connection status unknown';
$string['copiedlink'] = 'Link copied to clipboard.';
$string['copylink'] = 'Copy link';
$string['crontask'] = 'Collaborate scheduled task';
$string['debugging'] = 'Debugging';
$string['deleterecording'] = 'Delete recording entitled "{$a}"';
$string['deleterecordingconfirmation'] = 'Are you sure you want to delete the recording entitled "{$a}"?';
$string['downloadrec'] = 'Download recording';
$string['duration'] = 'Duration';
$string['ends'] = 'Ends - {$a}';
$string['error:apicallfailed'] = 'API call failed ( {$a} )';
$string['error:apifailure'] = 'An error occurred whilst talking to the collaborate server - please try again later. If the problem persists, please contact support.';
$string['error:failedtocreateurl'] = 'Failed to generate url for session';
$string['error:failedtodeletesession'] = 'Failed to delete collaborate session';
$string['error:invalidmoduleid'] = 'You must specify a valid course_module ID or an instance ID';
$string['error: invalidservertimezone'] = 'Invalid time zone, please make sure the time zone is properly configured';
$string['error:noconfiguration'] = 'Collaborate module is not configured correctly. Contact your site administrator.';
$string['error:restapifailedtocreateaccesstoken'] = 'Collaborate REST API problem - Failed to create access token. Please ask an administrator to check the API credentials';
$string['error:restapiunexpectedresponsecode'] = 'REST API failure. Unexpected response code ({$a}). Please contact an administrator';
$string['error:restapiunusable'] = 'Collaborate REST API is not in a usable state. Please contact an administrator.';
$string['error:restapiunreachable'] = 'Unable to reach Collaborate REST API. Please contact an administrator';
$string['error:restapimultpleenrollments'] = 'Multiple enrollments found for sessionId {$a->sessionid} and userId {$a->userid}';
$string['error:restapifailedtoenroll'] = 'Failed to create enrollment for userid {$a->userid} and sessionid {$a->sessionid}';
$string['error:restapiduplicatecontexts'] = 'Multiple contexts in Collaborate with extId {$a}.';
$string['error:restapiduplicateusers'] = 'Multiple users in Collaborate with extId {$a}.';
$string['error:restapisessionguesturlmissing'] = 'Guest url missing for sessionId {$a}.';
$string['error:serviceunreachable'] = 'WSDL unreachable';
$string['error:unknownaction'] = 'Unknown action';
$string['eventrecordingdeleted'] = 'Recording deleted';
$string['eventrecordingdownloaded'] = 'Recording downloaded';
$string['eventrecordingviewed'] = 'Recording viewed';
$string['eventsessionlaunched'] = 'Collab session launched';
$string['exitapidiagnostics'] = 'Exit API diagnostics';
$string['guestaccessenabled'] = 'Allow Collaborate guest access';
$string['guestrole'] = 'Collaborate guest role';
$string['guestlink'] = 'Collaborate guest link';
$string['hour'] = '1 Hour';
$string['hourminutes'] = '1 Hour and {$a->minutes} Minutes';
$string['hours'] = '{$a} Hours';
$string['hoursminutes'] = '{$a->hours} Hours and {$a->minutes} Minutes';
$string['lastjoined'] = 'Last Joined: {$a}';
$string['lastleft'] = 'Last Left: {$a}';
$string['log:all'] = 'All';
$string['log:light'] = 'Light - Emergency|Alert|Critical';
$string['log:medium'] = 'Medium - Emergency|Alert|Critical|Error|Warning';
$string['log:none'] = 'None';
$string['logging'] = 'Logging';
$string['meetingtimecurrent'] = 'Session in progress';
$string['meetingtimejoin'] = 'Join session';
$string['meetingtimepast'] = 'Session ended';
$string['minutes'] = '{$a} Minutes';
$string['modulename'] = 'Collaborate';
$string['modulename_help'] = 'Use Blackboard Collaborate with the Ultra experience to connect with one student or your entire class. Create virtual classrooms, offices and meeting spaces to engage your students in a more collaborative and interactive learning experience.';
$string['modulenameplural'] = 'Collaborate instances';
$string['moderator'] = 'Moderator';
$string['noguestentry'] = 'Sorry, guests are not allowed to take part in discussions';
$string['openended'] = 'Duration of course';
$string['participant'] = 'Participant';
$string['pluginadministration'] = 'Collaborate administration';
$string['pluginname'] = 'Collaborate';
$string['presenter'] = 'Presenter';
$string['recording'] = 'Recording {$a}';
$string['recordingcounts'] = '{$a->views} view(s)';
$string['recordingcountsincdownloads'] = '{$a->views} view(s) · {$a->downloads} download(s)';
$string['recordingdeleted'] = 'The recording entitled "{$a}" has been deleted.';
$string['recordings'] = 'Recordings';
$string['restapisettings'] = 'REST API settings';
$string['sessiongroup'] = 'Group {$a}';
$string['sessionstart'] = 'Start';
$string['sessionstarthelp'] = 'Participants can join the session 15 minutes before it starts';
$string['soapapisettings'] = 'SOAP (old) API settings';
$string['starts'] = 'Starts - {$a}';
$string['testapi'] = 'Test connection';
$string['timezone'] = 'Timezone: {$a}';
$string['unrestored'] = 'A moderator must view or join the session before you can access it.';
$string['verifyingapi'] = 'Verifying API connection. Please wait...';
$string['viewrec'] = 'View recording';
$string['downloadrec'] = 'Download recording';
