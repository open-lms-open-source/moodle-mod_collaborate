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
 * SOAP API / element: HtmlAttendeeCollection
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_collaborate\soap\generated;

class HtmlAttendeeCollection
{

    /**
     * @var HtmlAttendee[] $HtmlAttendee
     */
    protected $HtmlAttendee = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return HtmlAttendee[]
     */
    public function getHtmlAttendee()
    {
      return $this->HtmlAttendee;
    }

    /**
     * @param HtmlAttendee[] $HtmlAttendee
     * @return \mod_collaborate\soap\generated\HtmlAttendeeCollection
     */
    public function setHtmlAttendee(array $HtmlAttendee)
    {
      $this->HtmlAttendee = $HtmlAttendee;
      return $this;
    }

}
