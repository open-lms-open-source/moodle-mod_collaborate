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
 * SOAP API / element: HtmlRoomCollection
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_collaborate\soap\generated;

class HtmlRoomCollection {

    /**
     * @var HtmlRoom[] $HtmlRoom
     */
    protected $HtmlRoom = null;

    /**
     * @param HtmlRoom[] $HtmlRoom
     */
    public function __construct(array $HtmlRoom) {
        $this->HtmlRoom = $HtmlRoom;
    }

    /**
     * @return HtmlRoom[]
     */
    public function getHtmlRoom() {
        return $this->HtmlRoom;
    }

    /**
     * @param HtmlRoom[] $HtmlRoom
     * @return \mod_collaborate\soap\generated\HtmlRoomCollection
     */
    public function setHtmlRoom(array $HtmlRoom) {
        $this->HtmlRoom = $HtmlRoom;
        return $this;
    }

}
