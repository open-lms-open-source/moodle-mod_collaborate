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

    private $accesstokenexpires = null;

    const DELETE = 'DELETE';
    const GET = 'GET';
    const PATCH = 'PATCH';
    const POST = 'POST';
    const PUT = 'PUT';

    private function __construct(stdClass $config) {
        $this->setup($config);
        $this->set_accesstoken();
    }

    /**
     * Get API singleton instance.
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
        $this->logger->info('using api url', ['url' => $url]);
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
        if (empty($pathparams)) {
            return $resourcepath;
        }
        if (stripos($resourcepath, '{') !== false) {
            // Replace by keys.
            foreach ($pathparams as $key => $val) {
                $resourcepath = str_replace('{'.$key.'}', $val, $resourcepath);
            }
        } else {
            // Add params.
            foreach ($pathparams as $param) {
                $resourcepath .= '/' . $param;
            }
        }
        return $resourcepath;
    }

    /**
     * @param string $verb
     * @param string $resourcepath
     * @param requestoptions $requestoptions
     * @return mixed
     */
    public function rest_call($verb, $resourcepath, requestoptions $requestoptions) {
        $ch = curl_init();
        $headers = [];
        if ($resourcepath != 'token') {
            if ($this->accesstokenexpires < time()) {
                // Token has expired, get a new one!
                $this->set_accesstoken();
            }
            if (empty($this->accesstoken->access_token)) {
                throw new \Exception('Failed to create access token');
            }
            $headers[] = 'Authorization: Bearer '.$this->accesstoken->access_token;
        }
        $query = empty($requestoptions->urlparams) ? '' : '?' . http_build_query($requestoptions->urlparams, '', '&');
        $url = $this->api_url($this->process_resource_path($resourcepath, $requestoptions->pathparams) . $query);
        $this->logger->info('making curl call', ['verb' => $verb,  'url' => $url]);
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
}
