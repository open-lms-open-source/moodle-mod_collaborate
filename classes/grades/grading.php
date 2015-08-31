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

namespace mod_collaborate\grades;

defined('MOODLE_INTERNAL') || die();

/**
 * Grading functions.
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grading {

    /**
     * (Copied from eluminate module)
     * When updating grades, we set it to the max value that can possibly by acheived.
     * (sessions are graded as all or nothing)
     *
     * This function returns either the max numeric or scaled value.
     *
     * @param String $grade
     * @return string
     */
    public static function get_max_grade_val($grade) {
        if ($grade < 0) {
            $maxval = self::get_scaled_grade_max_val($grade);
        } else {
            $maxval = $grade;
        }
        return $maxval;
    }

    /**
     * (Copied from eluminate module)
     * For scaled grades, use the moodle function make_grades_menu to get an array
     * of all the different items in the selected scale (ID passed in $grade).
     *
     * Then use the php key() function to return the first element in the returned
     * array, which is effectively the "best" grade possible in the scale.
     *
     * @param string $grade
     * @return string
     */
    private static function get_scaled_grade_max_val($grade) {
        $grades = make_grades_menu($grade);
        return key($grades);
    }
}