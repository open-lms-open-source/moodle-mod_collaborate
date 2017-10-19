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
 * Fake api - for testing
 * @author    gthomas2
 * @copyright Copyright (c) 2016 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_collaborate\soap;

use mod_collaborate\logging\loggerdb;
use mod_collaborate\soap\api;
use mod_collaborate\soap\generated\BuildHtmlSessionUrl;
use mod_collaborate\soap\generated\HtmlSession;
use mod_collaborate\soap\generated\HtmlSessionCollection;
use mod_collaborate\soap\generated\HtmlSessionRecordingResponse;
use mod_collaborate\soap\generated\ListHtmlSession;
use mod_collaborate\soap\generated\RemoveHtmlSession;
use mod_collaborate\soap\generated\SetHtmlSession;
use mod_collaborate\soap\generated\SuccessResponse;
use mod_collaborate\soap\generated\UpdateHtmlSessionDetails;
use mod_collaborate\soap\generated\ServerConfiguration;
use mod_collaborate\soap\generated\ServerConfigurationResponseCollection;
use mod_collaborate\soap\generated\ServerConfigurationResponse;
use mod_collaborate\soap\generated\UrlResponse;
use mod_collaborate\soap\generated\HtmlSessionRecording;
use mod_collaborate\soap\generated\HtmlSessionRecordingResponseCollection;
use mod_collaborate\soap\generated\RemoveHtmlSessionRecording;
use mod_collaborate\soap\generated\UpdateHtmlSessionAttendee;
use stdClass;


defined('MOODLE_INTERNAL') || die();

class fakeapi extends api {

    /**
     * Constructor
     *
     * @param array $options
     * @param string $wsdl - just here to match base class.
     * @param stdClass $config - custom config passed in on construct.
     */
    public function __construct(array $options = array(), $wsdl = null, stdClass $config = null) {

        $logger = new loggerdb();
        $this->setLogger($logger);
        $this->usable = true;

    }

    /**
     * Don't bother testing the service.
     * @param string $serviceuri
     *
     * @return bool
     */
    protected function test_service_reachable($serviceuri) {
        return true;
    }

    /**
     * Override parent __soapCall method
     *
     * @param $function_name
     * @param $arguments
     * @param null $options
     * @param null $input_headers
     * @param null $output_headers
     * @return mixed
     * @throws \moodle_exception
     */
    public function __soapCall($function_name, $arguments, $options = null, $input_headers = null, &$output_headers = null) {
        throw new \coding_exception('Soap calls should not be made with the fake api.'.
                'Possibly you are trying to test a function that has not been implemented in the fake api.');
    }

    /**
     * Get fake api.
     * @param bool $reset
     * @param array $options
     * @param string $wsdl
     * @param stdClass $config
     * @return fakeapi
     */
    public static function get_api($reset = false, $options = [], $wsdl = null, stdClass $config = null) {
        return parent::get_api($reset, $options, $wsdl, $config);
    }

    /**
     * Get just the class name, no namespace.
     * @param object $object
     */
    protected function get_class($object) {
        $full = get_class($object);
        if (strpos($full, '\\') !== false) {
            return substr($full, strrpos($full, '\\') + 1);
        } else {
            return $full;
        }
    }

    /**
     * Generate a fakeid for an object.
     * Note - this only ever gets used with testing code, so it's OK that its terrible :-).
     * Also note - not to be used to procure alcohol.
     * @param string|object $object
     * @return int
     */
    protected function fakeid($object) {
        if (is_string($object)) {
            $name = $object;
        } else {
            $name = $this->get_class($object);
        }
        $key = 'fakeid_'.$name;
        $fakeid = get_config('collaborate', $key);
        if ($fakeid === false) {
            $fakeid = 0;
        }
        $fakeid ++;
        set_config($key, $fakeid, 'collaborate');
        return $fakeid;
    }

    /**
     * Delete object.
     * Only used for testing.
     *
     * @param string $classname
     * @param int $id
     * @pram null|int $parentid
     */
    protected function deleteobject($classname, $id, $parentid = null) {
        $key = 'object_'.$classname.'_'.$id;
        $object = $this->getobject($classname, $id);
        if (isset($object->parentid) && empty($parentid)) {
            $parentid = $object->parentid;
        }
        set_config($key, null, 'collaborate');

        if ($parentid !== null) {
            $pkey = 'object_parent_' . $classname . '_' . $parentid;
            $parentobj = get_config('collaborate', $pkey);
            if (!empty($parentobj)) {
                $arr = unserialize(base64_decode($parentobj));
            } else {
                $arr = [];
            }
            unset($arr[$key]);
            set_config($pkey, base64_encode(serialize($arr)), 'collaborate');
        }
    }

    /**
     * Get object.
     * Only used for testing.
     *
     * @param string $classname
     * @param int $id
     * @return mixed
     */
    protected function getobject($classname, $id) {
        $key = 'object_'.$classname.'_'.$id;
        return unserialize(base64_decode(get_config('collaborate', $key)));
    }

    /**
     * Set / update an object in config.
     * Only used for testing.
     *
     * @param mixed $object
     * @param int $id
     * @param int|null $parentid
     */
    protected function setobject($object, $id, $parentid = null) {
        $classname = $this->get_class($object);
        $key = 'object_'.$classname.'_'.$id;
        if ($parentid) {
            $object->parentid = $parentid;
        }
        set_config($key, base64_encode(serialize($object)), 'collaborate');
        if ($parentid !== null) {
            $pkey = 'object_parent_'.$classname.'_'.$parentid;
            $parentobj = get_config('collaborate', $pkey);
            if (empty($parentobj)) {
                $arr = [];
            } else {
                $arr = unserialize(base64_decode($parentobj));
            }
            $arr[$key] = $object;
            set_config($pkey, base64_encode(serialize($arr)), 'collaborate');
        }
    }

    /**
     * Update class A with properties of class B providing get and set methods exist.
     *
     * @param object $objecta
     * @param object $objectb
     * @throws \coding_exception
     */
    protected function updateclass($objecta, $objectb) {
        if (!is_object($objecta)) {
            throw new \coding_exception('$objecta must be an object', var_export($objecta, true));
        }
        if (!is_object($objectb)) {
            throw new \coding_exception('$objectb must be an object', var_export($objectb, true));
        }
        $reflect = new \ReflectionClass($objecta);
        $aprops = $reflect->getProperties();

        foreach ($aprops as $prop){
            $getmethod = 'get'.ucwords($prop->name);
            $setmethod = 'set'.ucwords($prop->name);
            if (method_exists($objectb, $getmethod)) {
                $newval = $objectb->$getmethod();
                if ($newval !== null) {
                    if (method_exists($objecta, $setmethod)) {
                        $objecta->$setmethod($newval);
                    }
                }
            }
        }
    }

    /**
     * @param ListHtmlSession $parameters
     *
     * @return HtmlSessionCollection
     */
    public function ListHtmlSession(ListHtmlSession $parameters) {
        $session = $this->getobject('HtmlSession', $parameters->getSessionId());
        $collection = new HtmlSessionCollection();
        $collection->setHtmlSession([$session]);
        return $collection;
    }

    /**
     * @param SetHtmlSession $parameters
     *
     * @return HtmlSessionCollection
     */
    public function SetHtmlSession(SetHtmlSession $parameters) {
        global $CFG;
        $ret = new HtmlSessionCollection();
        $fakeid = $this->fakeid('HtmlSession');
        $ret->setHtmlSession([new HtmlSession(
            $fakeid,
            $parameters->getName(),
            $parameters->getDescription(),
            $parameters->getStartTime(),
            $parameters->getEndTime(),
            $parameters->getBoundaryTime(),
            $parameters->getNoEndDate(),
            $parameters->getAllowGuest(),
            $CFG->wwwroot.'/mod/collaborate/tests/fixtures/fakeurl.php?id='.$fakeid.'&mode=guest',
            $parameters->getGuestRole(),
            $parameters->getShowProfile(),
            $parameters->getCanShareVideo(),
            $parameters->getCanShareAudio(),
            $parameters->getCanPostMessage(),
            $parameters->getCanAnnotateWhiteboard(),
            $parameters->getHtmlAttendees(),
            $parameters->getCreatorId(),
            true
        )]);
        $this->setobject($ret->getHtmlSession()[0], $fakeid);
        return $ret;
    }

    /**
     * @param UpdateHtmlSessionDetails $parameters
     * @return HtmlSessionCollection
     */
    public function UpdateHtmlSession(UpdateHtmlSessionDetails $parameters) {

        /* @var $existing HtmlSession*/
        $existing = $this->getobject('HtmlSession', $parameters->getSessionId());

        // Modify existing HtmlSession class with update parameters.
        $this->updateclass($existing, $parameters);

        $ret = new HtmlSessionCollection();
        $ret->setHtmlSession([$existing]);

        $this->setobject($existing, $existing->getSessionId());

        return $ret;
    }

    /**
     * @param RemoveHtmlSession $parameters
     * @return SuccessResponse
     */
    public function RemoveHtmlSession(RemoveHtmlSession $parameters) {
        $this->deleteobject('HtmlSession', $parameters->getSessionId());
        return new SuccessResponse(true);
    }

    /**
     * @param ServerConfiguration $parameters
     * @return ServerConfigurationResponseCollection
     */
    public function GetServerConfiguration(ServerConfiguration $parameters) {
        $collection = new ServerConfigurationResponseCollection();
        $response = new ServerConfigurationResponse(15, 50, 50, false, false, false, false, 'UTC');
        $collection->setServerConfigurationResponse([$response]);
        return ($collection);
    }

    /**
     * @param BuildHtmlSessionUrl $param
     * @return UrlResponse
     */
    public function BuildHtmlSessionUrl(BuildHtmlSessionUrl $param) {
        global $CFG;
        if (empty($param->getUserId())) {
            $url = $CFG->wwwroot.'/mod/collaborate/tests/fixtures/fakeurl.php?id='.$param->getSessionId().'&mode=guest';
        } else {
            $url = $CFG->wwwroot.'/mod/collaborate/tests/fixtures/fakeurl.php?id='.$param->getSessionId();
        }
        $response = new UrlResponse($url);
        return $response;
    }

    /**
     * @param UpdateHtmlSessionAttendee $parameters
     * @return UrlResponse
     */
    public function UpdateHtmlSessionAttendee(UpdateHtmlSessionAttendee $parameters) {
        $sessid = $parameters->getSessionId();
        $userid = $parameters->getHtmlAttendee()->getUserId();
        $url = new \moodle_url('/mod/collaborate/tests/fixtures/fakeurl.php', ['id' => $sessid, 'userid' => $userid]);

        return new UrlResponse($url->out(false));
    }

    /**
     * @param int $sessionid
     * @param null|int $id
     * @param null|string $starttime date formatted as \DateTime::ATOM
     * @param null|string $endtime date formatted as \DateTime::ATOM
     * @param null|string $recordingname
     */
    public function add_test_recording($sessionid, $id = null, $starttime = null, $endtime = null, $recordingname = null) {
        if (!$id) {
            $id = $this->fakeid('HtmlSessionRecordingResponse');
        }
        $recordingname = $recordingname === null ? 'Recording '.$id : $recordingname;
        $dti = new \DateTimeImmutable();
        $createdtime = $dti->format(\DateTime::ATOM);
        if ($starttime === null) {
            $dti = new \DateTimeImmutable('+' . $id . ' days');
            $starttime = $dti->format(\DateTime::ATOM);
        }
        if ($endtime === null) {
            $dti = new \DateTime($starttime);
            $dti->add(new \DateInterval('PT1H'));
            $endtime = $dti->format(\DateTime::ATOM);
        }
        $url = new \moodle_url('/mod/collaborate/tests/fixtures/fakeurl.php');
        $param = urlencode($url->out(false));
        $url->param('original_media_url', $param);
        $durationms = (strtotime($endtime) - strtotime($starttime)) * 1000;
        $object = new HtmlSessionRecordingResponse(
            $id, $createdtime, $starttime, $endtime, $durationms, $url->out(false), $recordingname, $sessionid
        );
        $this->setobject($object, $id, $sessionid);
        return $object;
    }

    /**
     * @param HtmlSessionRecording $parameters
     * @return HtmlSessionRecordingResponseCollection
     */
    public function ListHtmlSessionRecording(HtmlSessionRecording $parameters) {

        $recordings = $this->getobject('parent_HtmlSessionRecordingResponse', $parameters->getSessionId());
        if (empty($recordings)) {
            $recordings = [];
        }
        $recordings = array_values($recordings);
        $response = new HtmlSessionRecordingResponseCollection();
        $response->setHtmlSessionRecordingResponse($recordings);

        return $response;
    }

    /**
     * @param RemoveHtmlSessionRecording $parameters
     * @return null
     */
    public function RemoveHtmlSessionRecording(RemoveHtmlSessionRecording $parameters) {
        $recordingid = $parameters->getRecordingId();
        $this->deleteobject('HtmlSessionRecordingResponse', $recordingid);
        return null;
    }
}
