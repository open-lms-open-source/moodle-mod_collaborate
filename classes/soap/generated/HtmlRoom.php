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
 * SOAP API / element: HtmlRoom
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_collaborate\soap\generated;

class HtmlRoom
{

    /**
     * @var string $name
     */
    protected $name = null;

    /**
     * @var \DateTime $opened
     */
    protected $opened = null;

    /**
     * @var \DateTime $closed
     */
    protected $closed = null;

    /**
     * @var HtmlAttendeeCollection $htmlAttendees
     */
    protected $htmlAttendees = null;

    /**
     * @param string $name
     * @param \DateTime $opened
     * @param \DateTime $closed
     * @param HtmlAttendeeCollection $htmlAttendees
     */
    public function __construct($name, \DateTime $opened, \DateTime $closed, $htmlAttendees)
    {
      $this->name = $name;
      $this->opened = $opened->format('Y-m-d\TH:i:s\Z');
      $this->closed = $closed->format('Y-m-d\TH:i:s\Z');
      $this->htmlAttendees = $htmlAttendees;
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
     * @return \mod_collaborate\soap\generated\HtmlRoom
     */
    public function setName($name)
    {
      $this->name = $name;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getOpened()
    {
      if ($this->opened == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->opened);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $opened
     * @return \mod_collaborate\soap\generated\HtmlRoom
     */
    public function setOpened(\DateTime $opened)
    {
      $this->opened = $opened->format('Y-m-d\TH:i:s\Z');
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getClosed()
    {
      if ($this->closed == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->closed);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $closed
     * @return \mod_collaborate\soap\generated\HtmlRoom
     */
    public function setClosed(\DateTime $closed)
    {
      $this->closed = $closed->format('Y-m-d\TH:i:s\Z');
      return $this;
    }

    /**
     * @return HtmlAttendeeCollection
     */
    public function getHtmlAttendees()
    {
      return $this->htmlAttendees;
    }

    /**
     * @param HtmlAttendeeCollection $htmlAttendees
     * @return \mod_collaborate\soap\generated\HtmlRoom
     */
    public function setHtmlAttendees($htmlAttendees)
    {
      $this->htmlAttendees = $htmlAttendees;
      return $this;
    }

}
