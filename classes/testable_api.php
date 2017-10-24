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
 * Testable API
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2017 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_collaborate;

defined ('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../vendor/autoload.php');

use mod_collaborate\logging\loggerdb,
    mod_collaborate\traits\api as apitrait,
    Psr\Log\LoggerAwareTrait;

class testable_api {

    use apitrait;

    public function __construct() {
        $logger = new loggerdb();
        $this->setLogger($logger);
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
            $instance = new testable_api($config);
        }
        return $instance;
    }

    public function is_usable() {
        return true;
    }

    public static function configured() {
        return true;
    }

    protected function test_service_reachable($serviceuri) {
        return true;
    }
}
