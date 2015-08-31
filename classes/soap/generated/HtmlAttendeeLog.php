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
 * SOAP API / element: HtmlAttendeeLog
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_collaborate\soap\generated;

class HtmlAttendeeLog
{

    /**
     * @var \DateTime $joined
     */
    protected $joined = null;

    /**
     * @var \DateTime $left
     */
    protected $left = null;

    /**
     * @param \DateTime $joined
     * @param \DateTime $left
     */
    public function __construct(\DateTime $joined, \DateTime $left)
    {
      $this->joined = $joined->format('Y-m-d\TH:i:s\Z');
      $this->left = $left->format('Y-m-d\TH:i:s\Z');
    }

    /**
     * @return \DateTime
     */
    public function getJoined()
    {
      if ($this->joined == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->joined);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $joined
     * @return \mod_collaborate\soap\generated\HtmlAttendeeLog
     */
    public function setJoined(\DateTime $joined)
    {
      $this->joined = $joined->format('Y-m-d\TH:i:s\Z');
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLeft()
    {
      if ($this->left == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->left);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $left
     * @return \mod_collaborate\soap\generated\HtmlAttendeeLog
     */
    public function setLeft(\DateTime $left)
    {
      $this->left = $left->format('Y-m-d\TH:i:s\Z');
      return $this;
    }

}
