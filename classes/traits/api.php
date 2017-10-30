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
 * Base API class.
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2017 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_collaborate\traits;

defined('MOODLE_INTERNAL') || die();

use stdClass,
    mod_collaborate\local;

trait api {

    use loggable;

    /**
     * @var strdClass
     */
    protected $config;

    /**
     * @var bool
     */
    protected $usable = false;

    /**
     * @return bool
     */
    abstract public function is_usable();

    /**
     * @param stdClass $config
     * @return mixed
     */
    abstract public static function configured(stdClass $config = null);

    /**
     * Make sure module is configured for API or throw error.
     * @throws \moodle_exception
     */
    public static function require_configured() {
        if (!self::configured() && !local::duringtesting()) {
            throw new \moodle_exception('error:noconfiguration', 'mod_collaborate');
        }
    }

    /**
     * To be called on construct by parent class.
     * @param stdClass|null $config
     */
    protected function setup(stdClass $config = null) {
        if (!$config) {
            $config = get_config('collaborate');
        }
        $this->config = $config;
        $this->setup_logger();
    }

    /**
     * Return a date suitable for the API.
     *
     * NOTE: date('c', $data->timestart) doesn't work with the API as it treates any time date with a + symbol in it as
     * invalid. Therefore, this function expects the date passed in to already be a UTC date WITHOUT an offset.
     * @param int $uts unix time stamp
     * @param boolean $converttoutc - adjust server time to be a UTC time.
     *
     * @return string
     */
    public function api_datetime($uts, $converttoutc = false) {
        if ($converttoutc) {
            $uts = local::servertime_to_utc($uts);
        }
        $dt = new \DateTime(date('Y-m-d H:i:s', $uts), new \DateTimeZone('UTC'));
        return $dt->format('Y-m-d\TH:i:s\Z');
    }

    /**
     * Quickly test service is reachable
     *
     * @param $serviceuri
     * @return bool
     */
    abstract protected function test_service_reachable($serviceuri);

    /**
     * Set silent - i.e. no errors output to page.
     *
     * @param bool $silent
     */
    public function set_silent($silent = true) {
        $this->silent = $silent;
    }
}
