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
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
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
$string['completiondetail:launch'] = 'Launch a session';
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
$string['hideduration'] = 'Hide duration view';
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
$string['error:restapimigrationstatus'] = 'SOAP migration status could not be verified.';
$string['error:restapimigrationdata'] = 'SOAP migration data could not be retrieved.';
$string['error:serviceunreachable'] = 'WSDL unreachable';
$string['error:unknownaction'] = 'Unknown action';
$string['eventrecordingdeleted'] = 'Recording deleted';
$string['eventrecordingdownloaded'] = 'Recording downloaded';
$string['eventrecordingviewed'] = 'Recording viewed';
$string['eventsessionlaunched'] = 'Collab session launched';
$string['exitapidiagnostics'] = 'Exit API diagnostics';
$string['guestaccessenabled'] = 'Allow Collaborate guest access';
$string['group'] = 'Group: ';
$string['nogroup'] = 'Main session';
$string['guestrole'] = 'Collaborate guest role';
$string['guestlink'] = 'Collaborate guest link';
$string['guestlinks'] = 'Guest links';
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
$string['pluginname'] = 'Collaborate Ultra';
$string['presenter'] = 'Presenter';
$string['recording'] = 'Recording {$a}';
$string['recordingcounts'] = '{$a->views} view(s)';
$string['recordingcountsincdownloads'] = '{$a->views} view(s) · {$a->downloads} download(s)';
$string['recordingdeleted'] = 'The recording entitled "{$a}" has been deleted.';
$string['recordings'] = 'Recordings';
$string['restapisettings'] = 'REST API settings';
$string['session'] = 'Session';
$string['sessiongroup'] = 'Group {$a}';
$string['sessionstart'] = 'Start';
$string['sessionstarthelp'] = 'Participants can join the session 15 minutes before it starts';
$string['soapapisettings'] = 'SOAP (old) API settings';
$string['soapmigrationpending'] = 'Migration from SOAP to REST has been scheduled, but not initiated.';
$string['soapmigrationinprogress'] = 'Migration from SOAP to REST is currently in progress.';
$string['soapmigrationfinished'] = 'Migration from SOAP to REST has been completed successfully.';
$string['soapmigrationincomplete'] = 'Migration from SOAP to REST has finished. However, several sessions could not be migrated, please contact your administrator.';
$string['soapmigrationconfirmation'] = 'Are you sure you want to start the migration from SOAP to REST?';
$string['soapmigrationmessage'] = 'Migration execution has been scheduled.';
$string['starts'] = 'Starts - {$a}';
$string['testapi'] = 'Test connection';
$string['timezone'] = 'Timezone: {$a}';
$string['unrestored'] = 'A moderator must view or join the session before you can access it.';
$string['verifyingapi'] = 'Verifying API connection. Please wait...';
$string['viewrec'] = 'View recording';
$string['downloadrec'] = 'Download recording';
$string['privacy:metadata:collaborate'] = 'In order to identify the user who is joining the session, user data is sent to the Collaborate service.';
$string['privacy:metadata:collaborate:userid'] = 'The userid is sent from Moodle to allow you to access your data on the remote system.';
$string['privacy:metadata:collaborate:fullname'] = 'Your full name is sent to the remote system to allow a better user experience.';
$string['privacy:metadata:collaborate:avatarurl'] = 'Your profile picture is sent the Collaborate service.';
$string['privacy:metadata:collaborate:role'] = 'Your role within the Collaborate session.';
$string['privacy:metadata:launch:userid'] = 'The userid is sent from Moodle to allow you to access your data on the remote system.';
$string['privacy:metadata:launch:timelaunched'] = 'Session launched date';
$string['privacy:metadata:launch'] = 'In order to identify the user who is joining the session, user data is sent to the Collaborate service.';

// Instructor settings.
$string['instructorsettings'] = 'Instructor Settings';
$string['instructorsettings:allow'] = 'Allow participants to';
$string['instructorsettings:allow_help'] = 'These settings will only take effect on the session participants';
$string['instructorsettings:toggle'] = 'Turn on instructors settings';
$string['instructorsettings:toggledesc'] = 'By checking this setting, instructors will be allowed to change the Collaborate session settings';
$string['instructorsettings:defaultsettings'] = 'Default instructor settings';
$string['instructorsettings:defaultsettingsdesc'] = 'Default settings for new Collaborate sessions';
$string['cansharevideo'] = 'Share video feed';
$string['canpostmessages'] = 'Post messages';
$string['canannotatewhiteboard'] = 'Annotate on the whiteboard';
$string['canshareaudio'] = 'Share audio feed';
$string['candownloadrecordings'] = 'Download recordings';
$string['collaborate:downloadrecordings'] = 'Download recordings';
$string['instructorsettings:largesession'] = 'Large sessions';
$string['instructorsettings:largesession_help'] = 'This will enable sessions to grow up to 500 participants. If this setting is disabled upon saving and it shouldn\'t be, please contact your Collaborate representative.';
$string['largesessionenable'] = 'Enable sessions to allocate up to 500 participants';
$string['optionnotavailableforgroups'] = 'This option is not available for groups';
$string['rolenotavailableforlargesession'] = 'In large scale sessions, guests must be participants.';
// Override group mode.
$string['overridegroupmodeoff'] = 'Override Group Mode';
$string['overridegroupmode'] = 'Override group mode to off';
$string['overridegroupmodedesc'] = 'This will override and lock all Group Mode settings for all courses and activities in which they were created.';

// Performance settings.
$string['performancesettings'] = 'Performance settings';
$string['disablerecentactivity:toggle'] = 'Disable recent activity';
$string['disablerecentactivity:desc'] = 'Stop showing Collaborate session launches on recent activity feeds';

$string['migrationoncourseerror:creation'] = 'Edition of collaborate sessions is disabled. Migration to REST in course.';
$string['migrationoncourseerror:management'] = 'Management of collaborate sessions is disabled. Migration to REST in course.';
