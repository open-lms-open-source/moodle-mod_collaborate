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
 * Fixture for recording_count_helper_test.
 *
 * @package    mod_collaborate
 * @author     Sam Chaffee
 * @copyright  Copyright (c) 2016 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

return [
    'log' => [
        [
            'id' => 1,
            'time' => time(),
            'userid' => 1,
            'ip' => '192.0.0.1',
            'course' => 1,
            'module' => 'collaborate',
            'cmid' => 1,
            'action' => 'recording_viewed',
            'url' => 'yada',
            'info' => 1,
        ],
        [
            'id' => 2,
            'time' => time(),
            'userid' => 1,
            'ip' => '192.0.0.1',
            'course' => 1,
            'module' => 'collaborate',
            'cmid' => 1,
            'action' => 'recording_viewed',
            'url' => 'yada',
            'info' => 1,
        ],
        [
            'id' => 3,
            'time' => time(),
            'userid' => 1,
            'ip' => '192.0.0.1',
            'course' => 1,
            'module' => 'collaborate',
            'cmid' => 1,
            'action' => 'recording_downloaded',
            'url' => 'yada',
            'info' => 1,
        ],
        [
            'id' => 4,
            'time' => time(),
            'userid' => 1,
            'ip' => '192.0.0.1',
            'course' => 1,
            'module' => 'collaborate',
            'cmid' => 1,
            'action' => 'recording_downloaded',
            'url' => 'yada',
            'info' => 1,
        ],
        [
            'id' => 5,
            'time' => time(),
            'userid' => 1,
            'ip' => '192.0.0.1',
            'course' => 1,
            'module' => 'collaborate',
            'cmid' => 1,
            'action' => 'viewed',
            'url' => 'yada',
            'info' => 1,
        ],
        [
            'id' => 6,
            'time' => time(),
            'userid' => 1,
            'ip' => '192.0.0.1',
            'course' => 1,
            'module' => 'collaborate',
            'cmid' => 1,
            'action' => 'recording_downloaded',
            'url' => 'yada',
            'info' => 2,
        ],
        [
            'id' => 7,
            'time' => time(),
            'userid' => 1,
            'ip' => '192.0.0.1',
            'course' => 1,
            'module' => 'collaborate',
            'cmid' => 2,
            'action' => 'recording_downloaded',
            'url' => 'yada',
            'info' => 2,
        ],
    ]
];
