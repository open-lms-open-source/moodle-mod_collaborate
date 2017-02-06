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
 * Test exportable trait.
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2017 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use mod_collaborate\testables\trait_exportable;

class  mod_collaborate_trait_exportable_testcase extends advanced_testcase {

    public function test_array_keys_numeric() {
        $object = new trait_exportable();
        $array = [
            200 => 'a',
            300 => 'b',
            400 => 'c'
        ];
        $expected = true;
        $actual = $object->array_keys_numeric($array);
        $this->assertEquals($expected, $actual);

        $array = [
            'one' => 'a',
            'two' => 'b',
            'three' => 'c'
        ];
        $expected = false;
        $actual = $object->array_keys_numeric($array);
        $this->assertEquals($expected, $actual);
    }

    public function test_export_for_template() {
        global $PAGE;
        $object = new trait_exportable();
        $exported = $object->export_for_template($PAGE->get_renderer('core'));
        $this->assertTrue(is_array($exported->arr));
        $expected = [0, 1];
        $keys = array_keys($exported->arr);
        $this->assertEquals($expected, $keys);
        $this->assertTrue(is_string($exported->arr[0]->url));
    }
}
