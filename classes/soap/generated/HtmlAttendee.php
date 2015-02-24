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
 * SOAP API / element: HtmlAttendee
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_collaborate\soap\generated;

class HtmlAttendee
{

    /**
     * @var string $userId
     */
    protected $userId = null;

    /**
     * @var string $role
     */
    protected $role = null;

    /**
     * @var string $displayName
     */
    protected $displayName = null;

    /**
     * @var string $avatarUrl
     */
    protected $avatarUrl = null;

    /**
     * @var HtmlAttendeeLog[] $HtmlAttendeeLog
     */
    protected $HtmlAttendeeLog = null;

    /**
     * @param string $userId
     * @param string $role
     */
    public function __construct($userId, $role)
    {
      $this->userId = $userId;
      $this->role = $role;
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
     * @return \mod_collaborate\soap\generated\HtmlAttendee
     */
    public function setUserId($userId)
    {
      $this->userId = $userId;
      return $this;
    }

    /**
     * @return string
     */
    public function getRole()
    {
      return $this->role;
    }

    /**
     * @param string $role
     * @return \mod_collaborate\soap\generated\HtmlAttendee
     */
    public function setRole($role)
    {
      $this->role = $role;
      return $this;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
      return $this->displayName;
    }

    /**
     * @param string $displayName
     * @return \mod_collaborate\soap\generated\HtmlAttendee
     */
    public function setDisplayName($displayName)
    {
      $this->displayName = $displayName;
      return $this;
    }

    /**
     * @return string
     */
    public function getAvatarUrl()
    {
      return $this->avatarUrl;
    }

    /**
     * @param string $avatarUrl
     * @return \mod_collaborate\soap\generated\HtmlAttendee
     */
    public function setAvatarUrl($avatarUrl)
    {
      $this->avatarUrl = $avatarUrl;
      return $this;
    }

    /**
     * @return HtmlAttendeeLog[]
     */
    public function getHtmlAttendeeLog()
    {
      return $this->HtmlAttendeeLog;
    }

    /**
     * @param HtmlAttendeeLog[] $HtmlAttendeeLog
     * @return \mod_collaborate\soap\generated\HtmlAttendee
     */
    public function setHtmlAttendeeLog(array $HtmlAttendeeLog)
    {
      $this->HtmlAttendeeLog = $HtmlAttendeeLog;
      return $this;
    }

}
