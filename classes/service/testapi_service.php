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
 * Test api service
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_collaborate\service;

use mod_collaborate\local;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../lib.php');

/**
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testapi_service {


    /**
     * @var \mod_collaborate_renderer
     */
    protected $renderer;

    /**
     * @var bool
     */
    protected $server = false;

    /**
     * @var bool
     */
    protected $username = false;

    /**
     * @var bool
     */
    protected $password = false;

    /**
     * Constructor.
     *
     * @param \mod_collaborate_renderer $renderer
     */
    public function __construct(\mod_collaborate_renderer $renderer,
                                $server = false, $username = false, $password = false, $key = false, $secret = false) {
        $this->renderer = $renderer;
        $this->server = $server;
        $this->username  = $username;
        $this->password  = $password;
        $this->key = $key;
        $this->secret = $secret;
    }

    /**
     * Is the api verified?
     * Pass in config if it's available.
     *
     * @return bool
     */
    protected function api_verified() {
        $soapdetscomplete = $this->server !== false && $this->username !== false && $this->password !== false;
        $restdetscomplete = $this->server !== false && $this->key !== false && $this->secret !== false;
        $config = false;
        if ($soapdetscomplete) {
            $config = (object) [
                'server'   => $this->server,
                'username' => $this->username,
                'password' => $this->password
            ];
        } else if ($restdetscomplete) {
            $config = (object) [
                'restserver'   => $this->server,
                'restkey' => $this->key,
                'restsecret' => $this->secret
            ];
        }

        return local::api_verified(true, $config);
    }

    /**
     * Test the api and return array for ajax request
     *
     * @return string
     */
    protected function testapi_ajax() {
        $result = ['success' => self::api_verified()];
        return json_encode($result);
    }

    /**
     * Test the api and return html
     *
     * @return string
     */
    protected function testapi_render() {
        return $this->renderer->connection_verified(self::api_verified());
    }

    /**
     * Handle testing api.
     *
     * @return string|array
     * @throws \coding_exception
     */
    public function handle_testapi() {
        if (AJAX_SCRIPT) {
            return $this->testapi_ajax();
        } else {
            return $this->testapi_render();
        }
    }
}
