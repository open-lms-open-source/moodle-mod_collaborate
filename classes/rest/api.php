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
 * REST API lib.
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2017 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_collaborate\rest;

defined ('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../vendor/autoload.php');

use Horde\Socket\Client\Exception;
use mod_collaborate\rest\jwthelper,
    mod_collaborate\rest\requestoptions,
    mod_collaborate\local,
    mod_collaborate\traits\api as apitrait,
    Psr\Log\LoggerAwareTrait,
    stdClass;

class api {

    use LoggerAwareTrait,
        apitrait;

    /**
     * @var stdClass {expires_in, access_token}
     */
    private $accesstoken = null;

    /**
     * @var int
     */
    private $accesstokenexpires = null;

    const DELETE = 'DELETE';
    const GET = 'GET';
    const PATCH = 'PATCH';
    const POST = 'POST';
    const PUT = 'PUT';

    private function __construct(stdClass $config) {
        $this->setup($config);
        if (self::configured($config)) {
            $this->set_accesstoken();
        }
    }

    /**
     * Get API singleton instance.
     * @param bool $reset
     * @param bool $config
     * @return api
     */
    public static function instance($reset = false, $config = false) {
        static $instance;
        if ($reset) {
            $instance = null;
        }
        if (empty($instance)) {
            if (!$config) {
                $config = get_config('collaborate');
            }
            $instance = new api($config);
        }
        return $instance;
    }

    /**
     * Is REST API configured?
     * @param stdClass | bool $config
     * @return bool
     */
    public static function configured(stdClass $config = null) {
        if (!$config) {
            $config = get_config('collaborate');
        }
        return !empty($config) && !empty($config->restserver) && !empty($config->restkey) &&
            !empty($config->restsecret);
    }

    /**
     * @param string $methodparams method and param portion of url
     * @return string
     * @throws \Exception
     */
    private function api_url($methodparams) {
        $baseurl = trim($this->config->restserver);
        if (empty($baseurl)) {
            throw new \Exception('Error, baseurl not configured or invalid.');
        }
        if (substr($baseurl, -1) != '/') {
            $baseurl .= '/';
        }
        $url = $baseurl . $methodparams;
        return $url;
    }

    private function set_accesstoken() {
        $data = [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => jwthelper::get_token($this->config->restkey, $this->config->restsecret)
        ];

        $this->logger->info('Getting access token with req data', $data);

        $reqopts = new requestoptions('', [], $data);

        try {
            $this->accesstoken = $this->rest_call(self::POST, 'token', $reqopts);
            if (!empty($this->accesstoken->access_token)) {
                $this->accesstokenexpires = time() + $this->accesstoken->expires_in;
                $this->usable = true;
            } else {
                $this->usable = false;
            }
        } catch (Exception $e) {
            $this->usable = false;
        }
    }

    public function is_usable() {
        return $this->usable;
    }

    protected function process_resource_path($resourcepath, array $pathparams) {
        $resourcepath = strtolower($resourcepath);
        if (substr($resourcepath, 0, 1) === '/') {
            $resourcepath = substr($resourcepath, 1);
        }
        if (empty($pathparams)) {
            return $resourcepath;
        }
        if (stripos($resourcepath, '{') !== false) {
            // Replace by keys.
            foreach ($pathparams as $key => $val) {
                $resourcepath = str_ireplace('{'.$key.'}', $val, $resourcepath);
            }
        } else {
            // Add params.
            foreach ($pathparams as $param) {
                $resourcepath .= '/' . $param;
            }
        }
        return $resourcepath;
    }

    protected function test_service_reachable($serviceuri) {
        $this->logger->info('Testing service availability: '.$serviceuri);

        $options = [
            CURLOPT_TIMEOUT => 15,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        ];
        $curl = new \curl();
        $curl->setopt($options);
        $resp = $curl->get($serviceuri);
        $respobj = json_decode($resp);
        // We should get an error key if we hit the serviceuri without a path - e.g. /sessions.
        if (empty($respobj) || empty($respobj->errorKey)) {
            return false;
        }
        return true;

    }

    /**
     * @param string $verb
     * @param string $resourcepath
     * @param requestoptions $requestoptions
     * @return mixed
     */
    public function rest_call($verb, $resourcepath, requestoptions $requestoptions) {
        if ($resourcepath != 'token') {
            if (!$this->is_usable()) {
                if (!self::configured()) {
                    throw new \moodle_exception('error:noconfiguration', 'collaborate');
                } else {
                    if (!$this->test_service_reachable($this->config->restserver)) {
                        throw new \moodle_exception('error:restapiunreachable', 'collaborate');
                    } else {
                        throw new \moodle_exception('error:restapiunusable', 'collaborate');
                    }
                }
            }
        }
        $ch = curl_init();
        $headers = [];
        if ($resourcepath != 'token') {
            if (!self::configured()) {
                throw new \moodle_exception('error:noconfiguration', 'collaborate');
            }
            if ($this->accesstokenexpires < time()) {
                // Token has expired, get a new one!
                $this->set_accesstoken();
            }
            if (empty($this->accesstoken) || empty($this->accesstoken->access_token)) {
                throw new \moodle_exception('error:restapifailedtocreateaccesstoken', 'collaborate');
            }
            $headers[] = 'Authorization: Bearer '.$this->accesstoken->access_token;
        }
        $query = empty($requestoptions->queryparams) ? '' : '?' . http_build_query($requestoptions->queryparams, '', '&');
        $url = $this->api_url($this->process_resource_path($resourcepath, $requestoptions->pathparams) . $query);
        $this->logger->info('making curl call', ['verb' => $verb,  'url' => $url, 'json' => $requestoptions->bodyjson]);
        curl_setopt($ch, CURLOPT_URL, $url);
        switch ($verb) {
            case 'DELETE' :
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            case 'PATCH' :
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                break;
            case 'POST' :
                curl_setopt($ch, CURLOPT_POST, true);
                break;
            // Note, for PUT we cannot use CURLOPT_PUT as it adds the header Expect: 100-continue.
            case 'PUT' :
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                break;
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (!empty($requestoptions->postfields)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestoptions->postfields);
        }
        if (!empty($requestoptions->bodyjson)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestoptions->bodyjson);
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Content-Length: ' . strlen($requestoptions->bodyjson);
        }
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $jsonstr = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->logger->info('response', ['httpcode' => $httpcode]);
        $this->logger->info('response', ['jsonstr' => $jsonstr]);
        return (object) json_decode($jsonstr);
    }

    /**
     * @param stdClass $collaborate
     * @param int|null $groupid
     * @return object
     */
    private function make_session_request_object($collaborate, $groupid = null) {

        $collaborate->timeend = local::timeend_from_duration($collaborate->timestart, $collaborate->duration);
        $noenddate = local::timeend_open_ended($collaborate->timeend);
        list ($timestart, $timeend) = local::get_apitimes($collaborate->timestart, $collaborate->duration);

        $sessionname = $collaborate->name;
        if ($groupid !== null) {
            // Append sessionname with groupname.
            $groupname = groups_get_group_name($groupid);
            $sessionname .= ' ('.$groupname.')';
        }

        $description = isset($collaborate->introeditor['text']) ? $collaborate->introeditor['text'] : $collaborate->intro;

        $allowguests = !empty($collaborate->guestaccessenabled) && $collaborate->guestaccessenabled == 1;
        $guestrole = '';
        if ($allowguests) {
            switch ($collaborate->guestrole) {
                case 'pa' :
                    $guestrole = 'Participant';
                    break;
                case 'pr' :
                    $guestrole = 'Presenter';
                    break;
                case 'mo' :
                    $guestrole = 'Moderator';
                    break;
                default :
                    $guestrole = 'Participant';
            }
        }

        $now = time();

        $session = (object) [
            "allowInSessionInvitees" => true,
            "allowGuest" => $allowguests,
            "openChair" => false, // Hard coded.
            "mustBeSupervised" => true, // Hard coded.
            "noEndDate" => $noenddate,
            "description" => strip_tags($description),
            "name" => $sessionname,
            "occurrenceType" => "S", // Hard coded.
            "canPostMessage" => true, // Hard coded.
            "participantCanUseTools" => true, // Hard coded.
            "courseRoomEnabled" => true, // Hard coded.
            "canAnnotateWhiteboard" => true, // Hard coded.
            "canDownloadRecording" => true, // Hard coded.
            "canShareVideo" => true, // Hard coded.
            "raiseHandOnEnter" => false, // Hard coded.
            "boundaryTime" => local::boundary_time(),
            "showProfile" => true, // Hard coded.
            "canShareAudio" => true, // Hard coded.
            "startTime" => $timestart,
            "modified" => $now
        ];

        if (!empty($guestrole)) {
            $session->guestRole = $guestrole;
        }

        if (!isset($collaborate->sessionuid)) {
            $session->created = $now;
        }

        if (!$noenddate) {
            $session->endTime = $timeend;
        }

        return $session;
    }

    public function create_session(stdClass $collaborate, $course, $groupid = null) {

        // Note - Collaborate REST API does not allow for bulk enrollments on creation / update so we do not bother
        // doing it here (users get enrolled on the fly when they click "join").

        $session = $this->make_session_request_object($collaborate, $groupid);

        $respobj = $this->rest_call(self::POST, 'sessions', new requestoptions(json_encode($session)));
        if (!isset($respobj->id)) {
            throw new \coding_exception('Failed to create REST session with JSON '.json_encode($session).
                'response = '.var_export($respobj, true));
        }
        $sessionuid = $respobj->id;

        if ($groupid === null) {
            // Update the main collaborate instance, this is not for a group.
            $this->update_collaborate_instance_record($collaborate, $respobj);
        }

        return ($sessionuid);
    }

    public function update_collaborate_instance_record(stdClass $collaborate, $respobj) {
        global $DB;

        $sessionid = $respobj->id;

        // Update the main collaborate instance, this is not for a group.
        $collaborate->sessionuid = $sessionid;
        $collaborate->timemodified = time();
        $collaborate->timestart = strtotime($respobj->startTime);
        if ($collaborate->timeend != strtotime(local::TIMEDURATIONOFCOURSE)) {
            $collaborate->timeend = strtotime($respobj->endTime);
            $collaborate->duration = $collaborate->timeend - $collaborate->timestart;
        }

        if (empty($collaborate->guestaccessenabled)) {
            // This is necessary as an unchecked check box just removes the property instead of setting it to 0.
            $collaborate->guestaccessenabled = 0;
        }

        $result = $DB->update_record('collaborate', $collaborate);

        if ($result) {
            collaborate_grade_item_update($collaborate);
            local::update_calendar($collaborate);
        }

        return $result;
    }

    public function update_session($collaborate, $course, $sessionlink) {

        // Note - Collaborate REST API does not allow for bulk enrollments on creation / update so we do not bother
        // doing it here (users get enrolled on the fly when they click "join").

        if (empty($collaborate->sessionuid)) {
            throw new \coding_exception('Collaborate row must have a sessionuid property for an update to be possible');
        }

        $sessionuid = $collaborate->sessionuid;
        $session = $this->make_session_request_object($collaborate, $sessionlink->groupid);
        $respobj = $this->rest_call(self::PUT, 'sessions', new requestoptions(json_encode($session), [$sessionuid]));

        if (empty($sessionlink->groupid)) {
            // Update the main collaborate instance, this is not for a group.
            $this->update_collaborate_instance_record($collaborate, $respobj);
        }

        return ($sessionuid);
    }

    /**
     * Creates a user in Collaborate. Note - purposefully private scope.
     * @param int $userid - Moodle userid
     * @param string $avatarurl
     * @param string $displayname
     * @return mixed
     */
    private function create_user($userid, $avatarurl, $displayname) {
        $user = (object) [
            "avatarUrl" => $avatarurl,
            "displayName" => $displayname,
            "extId" => $userid,
            "created" => $this->api_datetime(time()),
            "modified" => $this->api_datetime(time())
        ];
        $reqopts = new requestoptions(json_encode($user));
        $this->rest_call(self::POST, '/users', $reqopts);
        return $this->get_user($userid);
    }

    /**
     * Get Collaborate user by moodle userid. Intentionally private scope.
     * @param int $userid - Moodle userid
     * @return mixed
     */
    private function get_user($userid) {
        $reqopts = new requestoptions('', [], ['extId' => $userid]);
        $resp = $this->rest_call(self::GET, '/users', $reqopts);
        if (empty($resp->results)) {
            return false;
        } else if (count($resp->results) > 1) {
            throw new coding_exception('Multiple users in Collaborate with extId '.$userid);
        }
        return reset($resp->results);
    }

    /**
     * Ensure user record in Collaborate and return it.
     * @param int $userid - Moodle userid
     * @param $avatarurl
     * @param $displayname
     * @return mixed
     */
    private function ensure_user($userid, $avatarurl, $displayname) {
        $user = $this->get_user($userid);
        if (!$user) {
            $user = $this->create_user($userid, $avatarurl, $displayname);
        }
        return $user;
    }

    /**
     * Update user record in Collaborate and return it.
     * @param int $userid
     * @param string $avatarurl
     * @param string $displayname
     * @return mixed
     */
    private function update_user($userid, $avatarurl, $displayname) {
        $user = $this->ensure_user($userid, $avatarurl, $displayname);
        $collaborateuserid = $user->id;
        $update = (object) [
            "avatarUrl" => $avatarurl,
            "displayName" => $displayname,
            "extId" => $userid,
            "modified" => $this->api_datetime(time())
        ];
        $reqops = new requestoptions(json_encode($update), ['userId' => $collaborateuserid]);
        return $this->rest_call(self::PUT, '/users/{userId}', $reqops);
    }

    public function update_attendee($sessionid, $userid, $avatarurl, $displayname, $role) {

        $user = $this->update_user($userid, $avatarurl, $displayname);
        $collabuserid = $user->id;

        $reqoptions = new requestoptions('', ['sessionId' => $sessionid], ['userId' => $collabuserid]);
        $enrollment = $this->rest_call(self::GET, '/sessions/{sessionId}/enrollments', $reqoptions);
        if (!isset($enrollment->results)) {
            $enrollment = false;
        } else {
            if (count($enrollment->results) > 1) {
                throw new \coding_exception('Multiple enrollments found for sessionId '.$sessionid.' and userId '.$collabuserid);
            }
            $enrollment = reset($enrollment->results);
        }
        $enrollobj = (object) [
            "launchingRole" => $role,
            "editingPermission" => $role === 'moderator' ? 'writer' : 'reader',
            "userId" => $collabuserid
        ];
        if (!$enrollment) {
            // Enrolllment does not exist!
            // Just create one.
            $reqopts = new requestoptions(json_encode($enrollobj), ['sessionId' => $sessionid]);
            $enrollmentresponse = $this->rest_call(self::POST, '/sessions/{sessionId}/enrollments', $reqopts);
            var_dump($enrollmentresponse);
            if (empty($enrollmentresponse->userId)) {
                throw new \coding_exception('Failed to create enrollment for userid '.$userid.
                        ' and sessionid '.$sessionid);
            }
            return $enrollmentresponse->permanentUrl;
        }
        $reqopts = new requestoptions(json_encode($enrollobj),
                ['sessionId' => $sessionid, 'enrollmentId' => $enrollment->id]);
        $enrollmentresponse = $this->rest_call(self::PUT, '/sessions/{sessionId}/enrollments/{enrollmentId}', $reqopts);
        return $enrollmentresponse->permanentUrl;
    }
}
