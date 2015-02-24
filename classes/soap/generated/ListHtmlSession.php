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
 * SOAP API / element: ListHtmlSession
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_collaborate\soap\generated;

class ListHtmlSession
{

    /**
     * @var string $userId
     */
    protected $userId = null;

    /**
     * @var string $groupingId
     */
    protected $groupingId = null;

    /**
     * @var int $sessionId
     */
    protected $sessionId = null;

    /**
     * @var string $creatorId
     */
    protected $creatorId = null;

    /**
     * @var \DateTime $startTime
     */
    protected $startTime = null;

    /**
     * @var \DateTime $endTime
     */
    protected $endTime = null;

    /**
     * @var string $name
     */
    protected $name = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return string
     */
    public function getUserId()
    {
      return $this->userId;
    }

    /**
     * @param string $userId
     * @return \mod_collaborate\soap\generated\ListHtmlSession
     */
    public function setUserId($userId)
    {
      $this->userId = $userId;
      return $this;
    }

    /**
     * @return string
     */
    public function getGroupingId()
    {
      return $this->groupingId;
    }

    /**
     * @param string $groupingId
     * @return \mod_collaborate\soap\generated\ListHtmlSession
     */
    public function setGroupingId($groupingId)
    {
      $this->groupingId = $groupingId;
      return $this;
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
     * @return \mod_collaborate\soap\generated\ListHtmlSession
     */
    public function setSessionId($sessionId)
    {
      $this->sessionId = $sessionId;
      return $this;
    }

    /**
     * @return string
     */
    public function getCreatorId()
    {
      return $this->creatorId;
    }

    /**
     * @param string $creatorId
     * @return \mod_collaborate\soap\generated\ListHtmlSession
     */
    public function setCreatorId($creatorId)
    {
      $this->creatorId = $creatorId;
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
     * @return \mod_collaborate\soap\generated\ListHtmlSession
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
     * @return \mod_collaborate\soap\generated\ListHtmlSession
     */
    public function setEndTime(\DateTime $endTime)
    {
      $this->endTime = $endTime->format('Y-m-d\TH:i:s\Z');
      return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
      return $this->name;
    }

    /**
     * @param string $name
     * @return \mod_collaborate\soap\generated\ListHtmlSession
     */
    public function setName($name)
    {
      $this->name = $name;
      return $this;
    }

}
