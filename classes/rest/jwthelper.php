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
 * JSON Web Token helpers.
 *
 * @copyright   Blackboard Inc 2017
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_collaborate\rest;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../vendor/autoload.php');

use Firebase\JWT\JWT;

/**
 * Class jwthelper
 * @package filter_ally
 */
class jwthelper {
    const ALGO = 'HS256';

    /**
     * Returns generated JSON Web Token according to the RFC 7519 (https://tools.ietf.org/html/rfc7519)
     * Adds appropriate payload.
     *
     * @return bool|string
     */
    public static function get_token($key, $secret) {

        $token = false;

        if (!empty($secret)) {

            $exp = time() + (60 * 60); // Expires in 1 hour.

            $payload = [
                'iss' => $key,
                'sub' => $key,
                'exp' => $exp
            ];

            try {
                /* @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
                $token = JWT::encode($payload, $secret, self::ALGO);
            } catch (\Exception $e) {
                debugging('Cannot encode JWT: ' . $e->getMessage(), DEBUG_DEVELOPER);
            }
        }

        return $token;
    }

}
