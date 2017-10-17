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

namespace mod_collaborate\logging;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../vendor/autoload.php');
use \Psr\Log as log;

/**
 * Define database logging class.
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class loggerdb extends loggerbase {

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array()) {
        global $DB;
        $data = '';
        foreach ($context as $key => $val) {
            $data .= $data != '' ? "\n\n" : '';
            if (!is_number($key)) {
                $data .= $key."\n\n";
            }
            $data .= $val;
            $data .= "\n".str_repeat('-', 80);
        }

        $record = (object) ['time' => time(), 'level' => $level, 'message' => $message, 'data' => $data];

        return $DB->insert_record('collaborate_log', $record);
    }
}
