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
 * Trait for classes that need to use the logger
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2017 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_collaborate\traits;

defined('MOODLE_INTERNAL') || die();

use mod_collaborate\logging\loggerdb,
    Psr\Log\LoggerAwareTrait,
    mod_collaborate\logging\constants as loggingconstants;

defined('MOODLE_INTERNAL') || die();

trait loggable {
    use LoggerAwareTrait;

    protected $silent = false;

    /**
     * Set up logger to use logger db.
     */
    public function setup_logger() {
        $logger = new loggerdb();
        $this->setLogger($logger);
    }


    /**
     * Log error and display an error if appropriate.
     *
     * @param string $errorcode
     * @param string $errorlevel
     * @param string|null $a - value to go into errorcode string
     * @param string $debuginfo
     * @param array $errorarr
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function process_error($errorcode, $errorlevel, $a = null, $debuginfo = '', array $errorarr = []) {
        global $COURSE;

        $errorstring = get_string($errorcode, 'mod_collaborate', $a);

        if (!empty($debuginfo)) {
            // Add debuginfo to start of error array (for logging).
            $debuginfarr = ['debug_info' => $debuginfo];
            $errorarr = array_merge($debuginfarr, $errorarr);
        }

        if (empty($this->logger)) {
            $this->setup_logger();
        }

        switch ($errorlevel) {
            case loggingconstants::SEV_EMERGENCY :
                $this->logger->emergency($errorstring, $errorarr);
                break;
            case loggingconstants::SEV_ALERT :
                $this->logger->alert($errorstring, $errorarr);
                break;
            case loggingconstants::SEV_CRITICAL :
                $this->logger->critical($errorstring, $errorarr);
                break;
            case loggingconstants::SEV_ERROR :
                $this->logger->error($errorstring, $errorarr);
                break;
            case loggingconstants::SEV_WARNING :
                $this->logger->warning($errorstring, $errorarr);
                break;
            case loggingconstants::SEV_NOTICE :
                $this->logger->notice($errorstring, $errorarr);
                break;
            case loggingconstants::SEV_INFO :
                $this->logger->info($errorstring, $errorarr);
                break;
            case loggingconstants::SEV_DEBUG :
                $this->logger->info($errorstring, $errorarr);
                break;
        }

        if ($this->silent) {
            return;
        }

        // Developer orinetated error message.
        $url = new \moodle_url('/course/view.php', ['id' => $COURSE->id]);
        if (!empty($errorarr)) {
            if (!empty($debuginfo)) {
                $debuginfo .= "\n\n" .
                    var_export($errorarr, true);
            } else {
                $debuginfo = var_export($errorarr, true);
            }
        }
        throw new \moodle_exception($errorcode, 'mod_collaborate', $url, null, $debuginfo);
    }
}
