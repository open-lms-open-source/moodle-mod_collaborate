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
use mod_collaborate\soap\generated\UpdateHtmlSessionAttendee;


defined('MOODLE_INTERNAL') || die();

class fakeapi extends api {

    /**
     * Constructor
     *
     * @param array $options
     * @param string $wsdl - just here to match base class.
     * @param bool $config - custom config passed in on construct.
     */
    public function __construct(array $options = array(), $wsdl = null, $config = false) {

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
    protected function quick_test_service($serviceuri) {
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
     * @param bool|\stdClass $config
     * @return fakeapi
     */
    public static function get_api($reset = false, $options = [], $wsdl = null, $config = false) {
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
     *
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
     * @param int $id
     * @param string $classname
     */
    protected function deleteobject($id, $classname) {
        $key = 'object_'.$classname.'_'.$id;
        set_config($key, null, 'collaborate');
    }

    /**
     * Get object.
     * Only used for testing.
     *
     * @param int $id
     * @param string $classname
     * @return mixed
     */
    protected function getobject($id, $classname) {
        $key = 'object_'.$classname.'_'.$id;
        return unserialize(get_config('collaborate', $key));
    }

    /**
     * Set / update an object in config.
     * Only used for testing.
     *
     * @param int $id
     * @param object $object
     */
    protected function setobject($id, $object) {
        $classname = $this->get_class($object);
        $key = 'object_'.$classname.'_'.$id;
        set_config($key, serialize($object), 'collaborate');
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
            throw new \coding_exception('$objecta must be an object');
        }
        if (!is_object($objectb)) {
            throw new \coding_exception('$objectb must be an object');
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
        $this->setobject($fakeid, $ret->getHtmlSession()[0]);
        return $ret;
    }

    /**
     * @param UpdateHtmlSessionDetails $parameters
     * @return HtmlSessionCollection
     */
    public function UpdateHtmlSession(UpdateHtmlSessionDetails $parameters) {

        /* @var $existing HtmlSession*/
        $existing = $this->getobject($parameters->getSessionId(), 'HtmlSession');

        // Modify existing HtmlSession class with update parameters.
        $this->updateclass($existing, $parameters);

        $ret = new HtmlSessionCollection();
        $ret->setHtmlSession([$existing]);

        $this->setobject($existing->getSessionId(), $existing);

        return $ret;
    }

    /**
     * @param RemoveHtmlSession $parameters
     * @return SuccessResponse
     */
    public function RemoveHtmlSession(RemoveHtmlSession $parameters) {
        $this->deleteobject($parameters->getSessionId(), 'HtmlSession');
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
     * @param HtmlSessionRecording $parameters
     * @param bool $returnnone
     * @return HtmlSessionRecordingResponseCollection
     */
    public function ListHtmlSessionRecording(HtmlSessionRecording $parameters, $returnnone = false) {
        $recordings = [];
        if (!$returnnone) {
            $url = new \moodle_url('/mod/collaborate/tests/fixtures/fakeurl.php');
            $param = urlencode($url->out(false));
            $url->param('original_media_url', $param);

            $daysago = new \DateTimeImmutable('3 days ago');
            $starttime = $daysago->format(\DateTime::ATOM);
            $endtime = $daysago->add(new \DateInterval('PT1H'))->format(\DateTime::ATOM);
            $sessid = $parameters->getSessionId();

            $starttime2 = $daysago->add(new \DateInterval('P1D'))->format(\DateTime::ATOM);
            $endtime2 = $daysago->add(new \DateInterval('P1DT1H'))->format(\DateTime::ATOM);

            $recordings = [
                new HtmlSessionRecordingResponse(1, $endtime, $starttime, $endtime, 60, $url->out(false), 'Recording 1', $sessid),
                new HtmlSessionRecordingResponse(2, $endtime2, $starttime2, $endtime2, 60, $url->out(false), 'Recording 2', $sessid),
            ];
        }
        $response = new HtmlSessionRecordingResponseCollection();
        $response->setHtmlSessionRecordingResponse($recordings);

        return $response;
    }
}
