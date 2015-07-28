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
 * SOAP API / element: HtmlSessionRecordingResponse
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_collaborate\soap\generated;

class HtmlSessionRecordingResponse
{

    /**
     * @var int $recordingId
     */
    protected $recordingId = null;

    /**
     * @var string $createdTs
     */
    protected $createdTs = null;

    /**
     * @var string $startTs
     */
    protected $startTs = null;

    /**
     * @var string $endTs
     */
    protected $endTs = null;

    /**
     * @var int $durationMillis
     */
    protected $durationMillis = null;

    /**
     * @var string $recordingUrl
     */
    protected $recordingUrl = null;

    /**
     * @var string $displayName
     */
    protected $displayName = null;

    /**
     * @var int $sessionId
     */
    protected $sessionId = null;

    /**
     * @param int $recordingId
     * @param string $createdTs
     * @param string $startTs
     * @param string $endTs
     * @param int $durationMillis
     * @param string $recordingUrl
     * @param string $displayName
     * @param int $sessionId
     */
    public function __construct($recordingId, $createdTs, $startTs, $endTs, $durationMillis, $recordingUrl, $displayName, $sessionId)
    {
      $this->recordingId = $recordingId;
      $this->createdTs = $createdTs;
      $this->startTs = $startTs;
      $this->endTs = $endTs;
      $this->durationMillis = $durationMillis;
      $this->recordingUrl = $recordingUrl;
      $this->displayName = $displayName;
      $this->sessionId = $sessionId;
    }

    /**
     * @return int
     */
    public function getRecordingId()
    {
      return $this->recordingId;
    }

    /**
     * @param int $recordingId
     * @return \mod_collaborate\soap\generated\HtmlSessionRecordingResponse
     */
    public function setRecordingId($recordingId)
    {
      $this->recordingId = $recordingId;
      return $this;
    }

    /**
     * @return string
     */
    public function getCreatedTs()
    {
      return $this->createdTs;
    }

    /**
     * @param string $createdTs
     * @return \mod_collaborate\soap\generated\HtmlSessionRecordingResponse
     */
    public function setCreatedTs($createdTs)
    {
      $this->createdTs = $createdTs;
      return $this;
    }

    /**
     * @return string
     */
    public function getStartTs()
    {
      return $this->startTs;
    }

    /**
     * @param string $startTs
     * @return \mod_collaborate\soap\generated\HtmlSessionRecordingResponse
     */
    public function setStartTs($startTs)
    {
      $this->startTs = $startTs;
      return $this;
    }

    /**
     * @return string
     */
    public function getEndTs()
    {
      return $this->endTs;
    }

    /**
     * @param string $endTs
     * @return \mod_collaborate\soap\generated\HtmlSessionRecordingResponse
     */
    public function setEndTs($endTs)
    {
      $this->endTs = $endTs;
      return $this;
    }

    /**
     * @return int
     */
    public function getDurationMillis()
    {
      return $this->durationMillis;
    }

    /**
     * @param int $durationMillis
     * @return \mod_collaborate\soap\generated\HtmlSessionRecordingResponse
     */
    public function setDurationMillis($durationMillis)
    {
      $this->durationMillis = $durationMillis;
      return $this;
    }

    /**
     * @return string
     */
    public function getRecordingUrl()
    {
      return $this->recordingUrl;
    }

    /**
     * @param string $recordingUrl
     * @return \mod_collaborate\soap\generated\HtmlSessionRecordingResponse
     */
    public function setRecordingUrl($recordingUrl)
    {
      $this->recordingUrl = $recordingUrl;
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
     * @return \mod_collaborate\soap\generated\HtmlSessionRecordingResponse
     */
    public function setDisplayName($displayName)
    {
      $this->displayName = $displayName;
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
     * @return \mod_collaborate\soap\generated\HtmlSessionRecordingResponse
     */
    public function setSessionId($sessionId)
    {
      $this->sessionId = $sessionId;
      return $this;
    }

}
