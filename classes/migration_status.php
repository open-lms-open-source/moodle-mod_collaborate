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

namespace mod_collaborate;

use mod_collaborate\task\soap_migrator_task;
/**
 * Singleton to manage the REST migration status.
 * @package   mod_collaborate
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class migration_status {
    /**
     * Singleton instance.
     * @var migration_status
     */
    private static $instance = null;

    /**
     * private constructor.
     */
    private function __construct() {

    }

    /**
     * Get the SIngleton instance
     * @return migration_status
     */
    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new migration_status();
        }
        return self::$instance;
    }

    /**
     * Get the REST migreation status.
     * @return bool|numeric
     * @throws \dml_exception
     */
    public function get_migration_status() {
        return get_config('collaborate', 'migrationstatus');
    }

    /**
     * Create a notification if the migration status is different than MIGRATED
     * @param string $message
     * @return bool
     * @throws \dml_exception
     */
    public function show_migration_notification($message) {
        $shown = false;
        // Check if REST migration is on course.
        $migrationstatus = $this->get_migration_status();
        if ($migrationstatus && $migrationstatus < soap_migrator_task::STATUS_MIGRATED) {
            \core\notification::error($message);
            $shown = true;
        }
        return $shown;
    }
}
