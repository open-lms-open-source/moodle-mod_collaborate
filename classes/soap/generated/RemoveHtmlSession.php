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
 * SOAP API / element: RemoveHtmlSession
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_collaborate\soap\generated;

class RemoveHtmlSession
{

    /**
     * @var int $sessionId
     */
    protected $sessionId = null;

    /**
     * @param int $sessionId
     */
    public function __construct($sessionId)
    {
      $this->sessionId = $sessionId;
    }

    /**
     * @return int
     */
    public function getSessionId()
    {
      return $this->sessionId;
    }

    /**
     * @param int $sessionId
     * @return \mod_collaborate\soap\generated\RemoveHtmlSession
     */
    public function setSessionId($sessionId)
    {
      $this->sessionId = $sessionId;
      return $this;
    }

}
