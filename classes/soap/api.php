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

namespace mod_collaborate\soap;

defined('MOODLE_INTERNAL') || die();

use mod_collaborate\logging\loggerdb;
use mod_collaborate\logging\constants;
use mod_collaborate\local;

require_once(__DIR__ . '/../../vendor/autoload.php');

use Psr\Log\LoggerAwareTrait;
use mod_collaborate\soap\fakeapi;

/**
 * The collab api.
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api extends generated\SASDefaultAdapter {
    use LoggerAwareTrait;

    /**
     * @var bool
     */
    protected $usable = true;

    /**
     * If true, do not output any error messages.
     * @var bool
     */
    protected $silent = false;

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

        $config = $config ?: get_config('collaborate');

        local::require_configured();

        // Set wsdl to local version.
        $wsdl = __DIR__ . '/../../wsdl.xml';

        // Set service end point if populated.
        if (!empty($config->server)) {
            $options['location'] = $config->server;
        }

        $options['login'] = $config->username;
        $options['password'] = $config->password;

        if (!empty($config->wsdebug)) {
            ini_set('soap.wsdl_cache_enabled', '0');
            ini_set('soap.wsdl_cache_ttl', '0');
            $options['trace'] = 1;
        }

        $serviceok = $this->quick_test_service($options['location']);
        if (!$serviceok) {
            $this->usable = false;
            return;
        }
        try {
            parent::__construct($options, $wsdl);
        } catch (\Exception $e) {
            $this->usable = false;
        }
    }

    /**
     * Set silent - i.e. no errors output to page.
     *
     * @param bool $silent
     */
    public function set_silent($silent = true) {
        $this->silent = $silent;
    }

    /**
     * Get api.
     * @param bool $reset
     * @param array $options
     * @param string $wsdl
     * @param bool|stdClass $config
     * @return api
     */
    public static function get_api($reset = false, $options = [], $wsdl = null, $config = false) {
        static $api;
        if ($api && !$reset) {
            return $api;
        }
        if (local::duringtesting()) {
            $api = new fakeapi($options, $wsdl, $config);
        } else {
            $api = new api($options, $wsdl, $config);
        }
        return $api;
    }

    /**
     * Set usable status.
     *
     * @param $usable
     */
    public function set_usable($usable) {
        $this->usable = $usable;
    }

    /**
     * Is the api usable?
     */
    public function is_usable() {
        return $this->usable;
    }

    /**
     * Quickly test service is reachable
     *
     * @param $serviceuri
     * @return bool
     */
    protected function quick_test_service($serviceuri) {
        $ch = curl_init();
        $this->logger->info('Testing service availability: '.$serviceuri);
        curl_setopt($ch, CURLOPT_URL, $serviceuri);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $body = curl_exec($ch);
        if (stripos($body, '<soap:Envelope') === false) {
            // Body does not contain expected text, service is bad.
            $body = false;
        }
        if (!$body) {
            $this->logger->critical(get_string('error:serviceunreachable', 'mod_collaborate'));
            return false;
        } else {
            $this->logger->info('Service accessible');
            return true;
        }
    }

    /**
     * Log error and display an error if appropriate.
     *
     * @param $errorkey
     * @param $errorlevel
     * @param string $debuginfo
     * @param array $errorarr
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function process_error($errorkey, $errorlevel, $debuginfo = '', array $errorarr = []) {
        global $COURSE;

        $errorstring = get_string($errorkey, 'mod_collaborate');

        if (!empty($debuginfo)) {
            // Add debuginfo to start of error array (for logging).
            $debuginfarr = ['debug_info' => $debuginfo];
            $errorarr = array_merge($debuginfarr, $errorarr);
        }

        switch ($errorlevel) {
            case constants::SEV_EMERGENCY :
                $this->logger->emergency($errorstring, $errorarr);
                break;
            case constants::SEV_ALERT :
                $this->logger->alert($errorstring, $errorarr);
                break;
            case constants::SEV_CRITICAL :
                $this->logger->critical($errorstring, $errorarr);
                break;
            case constants::SEV_ERROR :
                $this->logger->error($errorstring, $errorarr);
                break;
            case constants::SEV_WARNING :
                $this->logger->warning($errorstring, $errorarr);
                break;
            case constants::SEV_NOTICE :
                $this->logger->notice($errorstring, $errorarr);
                break;
            case constants::SEV_INFO :
                $this->logger->info($errorstring, $errorarr);
                break;
            case constants::SEV_DEBUG :
                $this->logger->info($errorstring, $errorarr);
                break;
        }

        if ($this->silent) {
            return;
        }

        // Developer orinetated error message.
        $url = new \moodle_url('/course/view.php', ['id' => $COURSE->id]);
        if (!empty($errorarr)) {
            if (!empty($debuginfo)) {
                $debuginfo .= "\n\n" .
                    var_export($errorarr, true);
            } else {
                $debuginfo = var_export($errorarr, true);
            }
        }
        throw new \moodle_exception($errorkey, 'mod_collaborate', $url, null, $debuginfo);
    }

    /**
     * Gets last soap request and response - strips out credentials.
     *
     * @return array
     */
    public function get_soap_req_resp() {

        $debug = get_config('collaborate', 'wsdebug');
        if (!$debug) {
            return [
                'Service debugging not enabled in config',
                'Service debugging not enabled in config',
                'Service debugging not enabled in config',
                'Service debugging not enabled in config'
            ];
        }

        // Get last request xml but remove soap api password.
        libxml_use_internal_errors(true);
        $lastrequest = $this->__getLastRequest();
        $lastrequestheaders = $this->__getLastRequestHeaders();

        $reqx = simplexml_load_string($lastrequest);
        if ($reqx) {
            $passwords = $reqx->xpath('//ns1:Password');
            for ($c = 0; $c < count($passwords); $c++) {
                $passwords[$c][0] = '****';
            }
            $lastrequest = $reqx->asXML();
        }
        $lastresponse = $this->__getLastResponse();
        $lastresponseheaders = $this->__getLastResponseHeaders();

        return [
            $lastrequestheaders,
            $lastrequest,
            $lastresponseheaders,
            $lastresponse
        ];
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

        $start = microtime(true);

        if (!$this->usable) {
            $key = 'error:apifailure';
            $this->process_error($key, constants::SEV_CRITICAL);
            if ($this->silent) {
                return false;
            }
        }

        $config = get_config('collaborate');

        $headerbody = array('Name' => $config->username,
        'Password' => $config->password);
        $ns = 'http://sas.elluminate.com/';
        $header = new \SOAPHeader($ns, 'BasicAuth', $headerbody);
        $this->__setSoapHeaders($header);

        try {
            $result = parent::__soapCall($function_name, $arguments, $options, $input_headers, $output_headers);
        } catch (\SoapFault $fault) {
            $soapfault = "SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})";

            list ($lreqheaders, $lastreq, $lrespheaders, $lastresp) = $this->get_soap_req_resp();
            $debuginfo = [
                'request_headers' => $lreqheaders,
                'request' => $lastreq,
                'response_headers' => $lrespheaders,
                'response' => $lastresp
            ];

            $this->process_error('error:apifailure', constants::SEV_CRITICAL, $soapfault, $debuginfo);

            if ($this->silent) {
                return false;
            }
        }

        // OK there were no faults.
        // This soap call will already have been logged if there was an error ubt the script will have died so we wont
        // get duplicate calls logged.
        $duration = round(microtime(true) - $start, 2);
        list ($lreqheaders, $lastreq, $lrespheaders, $lastresp) = $this->get_soap_req_resp();
        $this->logger->debug('SOAP CALL SUCCESS ('.$duration.' S)', [
            'request_headers' => $lreqheaders,
            'request' => $lastreq,
            'response_headers' => $lrespheaders,
            'response' => $lastresp]);

        return ($result);
    }
}
