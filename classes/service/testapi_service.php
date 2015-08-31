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
     * Constructor.
     *
     * @param \mod_collaborate_renderer $renderer
     */
    public function __construct(\mod_collaborate_renderer $renderer) {
        $this->renderer = $renderer;
    }

    /**
     * Handle testing api.
     *
     * @return string
     * @throws \coding_exception
     */
    public function handle_testapi() {
        return $this->renderer->connection_verified(local::api_verified(true));
    }
}