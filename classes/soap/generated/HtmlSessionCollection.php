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
 * SOAP API / element: HtmlSessionCollection
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_collaborate\soap\generated;

class HtmlSessionCollection
{

    /**
     * @var HtmlSession[] $HtmlSession
     */
    protected $HtmlSession = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return HtmlSession[]
     */
    public function getHtmlSession()
    {
      return $this->HtmlSession;
    }

    /**
     * @param HtmlSession[] $HtmlSession
     * @return \mod_collaborate\soap\generated\HtmlSessionCollection
     */
    public function setHtmlSession(array $HtmlSession)
    {
      $this->HtmlSession = $HtmlSession;
      return $this;
    }

}
