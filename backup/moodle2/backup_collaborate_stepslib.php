<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Define all the backup steps that will be used by the {@see backup_collaborate_activity_task}.
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define the complete collaborate structure for backup, with file and id annotations.
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_collaborate_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines the backup structure of the module.
     *
     * @return backup_nested_element
     */
    protected function define_structure() {

        // Define the root element describing the collaborate instance.
        $collaborate = new backup_nested_element('collaborate', ['id'], [
            'name', 'intro', 'introformat', 'grade', 'timestart', 'timeend',
            'duration', 'boundaryminutes', 'sessionid', 'completionlaunch',
            'guestaccessenabled', 'guestrole', 'guesturl', 'canpostmessages',
            'canannotatewhiteboard', 'cansharevideo', 'canshareaudio',
            'candownloadrecordings', 'largesessionenable',
        ]);

        // If we had more elements, we would build the tree here.

        // Define data sources.
        $collaborate->set_source_table('collaborate', ['id' => backup::VAR_ACTIVITYID]);

        // If we were referring to other tables, we would annotate
        // the relation with the element's annotate_ids() method.

        // Define file annotations (we do not use itemid in this example).
        $collaborate->annotate_files('mod_collaborate', 'intro', null);

        // Return the root element (collaborate), wrapped into standard activity structure.
        return $this->prepare_activity_structure($collaborate);
    }
}
