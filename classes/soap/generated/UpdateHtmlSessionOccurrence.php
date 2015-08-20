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
 * SOAP API / element: UpdateHtmlSessionOccurrence
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_collaborate\soap\generated;

class UpdateHtmlSessionOccurrence
{

    /**
     * @var int $occurrenceId
     */
    protected $occurrenceId = null;

    /**
     * @var \DateTime $startTime
     */
    protected $startTime = null;

    /**
     * @var \DateTime $endTime
     */
    protected $endTime = null;

    /**
     * @param int $occurrenceId
     */
    public function __construct($occurrenceId)
    {
      $this->occurrenceId = $occurrenceId;
    }

    /**
     * @return int
     */
    public function getOccurrenceId()
    {
      return $this->occurrenceId;
    }

    /**
     * @param int $occurrenceId
     * @return \mod_collaborate\soap\generated\UpdateHtmlSessionOccurrence
     */
    public function setOccurrenceId($occurrenceId)
    {
      $this->occurrenceId = $occurrenceId;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime()
    {
      if ($this->startTime == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->startTime);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $startTime
     * @return \mod_collaborate\soap\generated\UpdateHtmlSessionOccurrence
     */
    public function setStartTime(\DateTime $startTime)
    {
      $this->startTime = $startTime->format('Y-m-d\TH:i:s\Z');
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndTime()
    {
      if ($this->endTime == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->endTime);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $endTime
     * @return \mod_collaborate\soap\generated\UpdateHtmlSessionOccurrence
     */
    public function setEndTime(\DateTime $endTime)
    {
      $this->endTime = $endTime->format('Y-m-d\TH:i:s\Z');
      return $this;
    }

}
