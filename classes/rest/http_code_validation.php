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
 * Http code validation.
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2017 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_collaborate\rest;

defined('MOODLE_INTERNAL') || die();

use mod_collaborate\traits\loggable,
    mod_collaborate\logging\constants as loggingconstants;

class http_code_validation {

    use loggable;

    /**
     * @var int[]
     */
    private $expectedcodes;

    /**
     * @var http_validation_code_error[] | null - errors hashed by response code (as string - e.g. '400' => ...).
     */
    private $errorsbycode;

    /**
     * http_code_validation constructor.
     * @param int[] $expectedcodes
     * @param array|null $errorsbycode
     */
    public function __construct(array $expectedcodes = [200], array $errorsbycode = null) {
        $this->expectedcodes = $expectedcodes;
        $this->errorsbycode = $errorsbycode;
    }

    /**
     * Validate the response code for a response.
     * @param response $response
     */
    public function validate_response(response $response) {
        if (!in_array($response->httpcode, $this->expectedcodes)) {
            if (!empty($this->errorsbycode[strval($response->httpcode)])) {
                $valerr = $this->errorsbycode[$response->httpcode];
                $this->process_error($valerr->errorcode, $valerr->severity,
                        $valerr->a, $valerr->debuginfo);
            } else {
                $this->process_error('error:restapiunexpectedresponsecode', loggingconstants::SEV_CRITICAL,
                        $response->httpcode, 'Expected response codes : '.implode(',', $this->expectedcodes));
            }
        }
    }

}
