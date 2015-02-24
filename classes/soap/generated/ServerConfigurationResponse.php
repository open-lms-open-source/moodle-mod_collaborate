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
 * SOAP API / element: ServerConfigurationResponse
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_collaborate\soap\generated;

class ServerConfigurationResponse
{

    /**
     * @var int $boundaryTime
     */
    protected $boundaryTime = null;

    /**
     * @var int $maxAvailableTalkers
     */
    protected $maxAvailableTalkers = null;

    /**
     * @var int $maxAvailableCameras
     */
    protected $maxAvailableCameras = null;

    /**
     * @var boolean $raiseHandOnEnter
     */
    protected $raiseHandOnEnter = null;

    /**
     * @var boolean $mayUseTelephony
     */
    protected $mayUseTelephony = null;

    /**
     * @var boolean $mayUseSecureSignOn
     */
    protected $mayUseSecureSignOn = null;

    /**
     * @var boolean $mustReserveSeats
     */
    protected $mustReserveSeats = null;

    /**
     * @var string $timeZone
     */
    protected $timeZone = null;

    /**
     * @param int $boundaryTime
     * @param int $maxAvailableTalkers
     * @param int $maxAvailableCameras
     * @param boolean $raiseHandOnEnter
     * @param boolean $mayUseTelephony
     * @param boolean $mayUseSecureSignOn
     * @param boolean $mustReserveSeats
     * @param string $timeZone
     */
    public function __construct($boundaryTime, $maxAvailableTalkers, $maxAvailableCameras, $raiseHandOnEnter, $mayUseTelephony, $mayUseSecureSignOn, $mustReserveSeats, $timeZone)
    {
      $this->boundaryTime = $boundaryTime;
      $this->maxAvailableTalkers = $maxAvailableTalkers;
      $this->maxAvailableCameras = $maxAvailableCameras;
      $this->raiseHandOnEnter = $raiseHandOnEnter;
      $this->mayUseTelephony = $mayUseTelephony;
      $this->mayUseSecureSignOn = $mayUseSecureSignOn;
      $this->mustReserveSeats = $mustReserveSeats;
      $this->timeZone = $timeZone;
    }

    /**
     * @return int
     */
    public function getBoundaryTime()
    {
      return $this->boundaryTime;
    }

    /**
     * @param int $boundaryTime
     * @return \mod_collaborate\soap\generated\ServerConfigurationResponse
     */
    public function setBoundaryTime($boundaryTime)
    {
      $this->boundaryTime = $boundaryTime;
      return $this;
    }

    /**
     * @return int
     */
    public function getMaxAvailableTalkers()
    {
      return $this->maxAvailableTalkers;
    }

    /**
     * @param int $maxAvailableTalkers
     * @return \mod_collaborate\soap\generated\ServerConfigurationResponse
     */
    public function setMaxAvailableTalkers($maxAvailableTalkers)
    {
      $this->maxAvailableTalkers = $maxAvailableTalkers;
      return $this;
    }

    /**
     * @return int
     */
    public function getMaxAvailableCameras()
    {
      return $this->maxAvailableCameras;
    }

    /**
     * @param int $maxAvailableCameras
     * @return \mod_collaborate\soap\generated\ServerConfigurationResponse
     */
    public function setMaxAvailableCameras($maxAvailableCameras)
    {
      $this->maxAvailableCameras = $maxAvailableCameras;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getRaiseHandOnEnter()
    {
      return $this->raiseHandOnEnter;
    }

    /**
     * @param boolean $raiseHandOnEnter
     * @return \mod_collaborate\soap\generated\ServerConfigurationResponse
     */
    public function setRaiseHandOnEnter($raiseHandOnEnter)
    {
      $this->raiseHandOnEnter = $raiseHandOnEnter;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getMayUseTelephony()
    {
      return $this->mayUseTelephony;
    }

    /**
     * @param boolean $mayUseTelephony
     * @return \mod_collaborate\soap\generated\ServerConfigurationResponse
     */
    public function setMayUseTelephony($mayUseTelephony)
    {
      $this->mayUseTelephony = $mayUseTelephony;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getMayUseSecureSignOn()
    {
      return $this->mayUseSecureSignOn;
    }

    /**
     * @param boolean $mayUseSecureSignOn
     * @return \mod_collaborate\soap\generated\ServerConfigurationResponse
     */
    public function setMayUseSecureSignOn($mayUseSecureSignOn)
    {
      $this->mayUseSecureSignOn = $mayUseSecureSignOn;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getMustReserveSeats()
    {
      return $this->mustReserveSeats;
    }

    /**
     * @param boolean $mustReserveSeats
     * @return \mod_collaborate\soap\generated\ServerConfigurationResponse
     */
    public function setMustReserveSeats($mustReserveSeats)
    {
      $this->mustReserveSeats = $mustReserveSeats;
      return $this;
    }

    /**
     * @return string
     */
    public function getTimeZone()
    {
      return $this->timeZone;
    }

    /**
     * @param string $timeZone
     * @return \mod_collaborate\soap\generated\ServerConfigurationResponse
     */
    public function setTimeZone($timeZone)
    {
      $this->timeZone = $timeZone;
      return $this;
    }

}
