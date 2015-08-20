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
 * SOAP API / element: HtmlSession
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_collaborate\soap\generated;

class HtmlSession
{

    /**
     * @var int $sessionId
     */
    protected $sessionId = null;

    /**
     * @var string $name
     */
    protected $name = null;

    /**
     * @var string $description
     */
    protected $description = null;

    /**
     * @var \DateTime $startTime
     */
    protected $startTime = null;

    /**
     * @var \DateTime $endTime
     */
    protected $endTime = null;

    /**
     * @var int $boundaryTime
     */
    protected $boundaryTime = null;

    /**
     * @var boolean $noEndDate
     */
    protected $noEndDate = null;

    /**
     * @var boolean $allowGuest
     */
    protected $allowGuest = null;

    /**
     * @var string $guestUrl
     */
    protected $guestUrl = null;

    /**
     * @var string $guestRole
     */
    protected $guestRole = null;

    /**
     * @var boolean $showProfile
     */
    protected $showProfile = null;

    /**
     * @var boolean $canShareVideo
     */
    protected $canShareVideo = null;

    /**
     * @var boolean $canShareAudio
     */
    protected $canShareAudio = null;

    /**
     * @var boolean $canPostMessage
     */
    protected $canPostMessage = null;

    /**
     * @var boolean $canAnnotateWhiteboard
     */
    protected $canAnnotateWhiteboard = null;

    /**
     * @var HtmlAttendeeCollection[] $htmlAttendees
     */
    protected $htmlAttendees = null;

    /**
     * @var string $groupingList
     */
    protected $groupingList = null;

    /**
     * @var string $creatorId
     */
    protected $creatorId = null;

    /**
     * @var boolean $mustBeSupervised
     */
    protected $mustBeSupervised = null;

    /**
     * @var boolean $openChair
     */
    protected $openChair = null;

    /**
     * @var boolean $permissionsOn
     */
    protected $permissionsOn = null;

    /**
     * @var boolean $raiseHandOnEnter
     */
    protected $raiseHandOnEnter = null;

    /**
     * @var boolean $allowInSessionInvitees
     */
    protected $allowInSessionInvitees = null;

    /**
     * @var string $contextId
     */
    protected $contextId = null;

    /**
     * @var string $contextTitle
     */
    protected $contextTitle = null;

    /**
     * @var boolean $recordingEnabled
     */
    protected $recordingEnabled = null;

    /**
     * @var string $recordingCallBackUrl
     */
    protected $recordingCallBackUrl = null;

    /**
     * @var string $sessionType
     */
    protected $sessionType = null;

    /**
     * @var string $sessionRule
     */
    protected $sessionRule = null;

    /**
     * @param int $sessionId
     * @param string $name
     * @param string $description
     * @param \DateTime $startTime
     * @param \DateTime $endTime
     * @param int $boundaryTime
     * @param boolean $noEndDate
     * @param boolean $allowGuest
     * @param string $guestUrl
     * @param string $guestRole
     * @param boolean $showProfile
     * @param boolean $canShareVideo
     * @param boolean $canShareAudio
     * @param boolean $canPostMessage
     * @param boolean $canAnnotateWhiteboard
     * @param HtmlAttendeeCollection[] $htmlAttendees
     * @param string $creatorId
     * @param boolean $recordingEnabled
     */
    public function __construct($sessionId, $name, $description, \DateTime $startTime, \DateTime $endTime, $boundaryTime, $noEndDate, $allowGuest, $guestUrl, $guestRole, $showProfile, $canShareVideo, $canShareAudio, $canPostMessage, $canAnnotateWhiteboard, array $htmlAttendees, $creatorId, $recordingEnabled)
    {
      $this->sessionId = $sessionId;
      $this->name = $name;
      $this->description = $description;
      $this->startTime = $startTime->format('Y-m-d\TH:i:s\Z');
      $this->endTime = $endTime->format('Y-m-d\TH:i:s\Z');
      $this->boundaryTime = $boundaryTime;
      $this->noEndDate = $noEndDate;
      $this->allowGuest = $allowGuest;
      $this->guestUrl = $guestUrl;
      $this->guestRole = $guestRole;
      $this->showProfile = $showProfile;
      $this->canShareVideo = $canShareVideo;
      $this->canShareAudio = $canShareAudio;
      $this->canPostMessage = $canPostMessage;
      $this->canAnnotateWhiteboard = $canAnnotateWhiteboard;
      $this->htmlAttendees = $htmlAttendees;
      $this->creatorId = $creatorId;
      $this->recordingEnabled = $recordingEnabled;
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
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setSessionId($sessionId)
    {
      $this->sessionId = $sessionId;
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
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setName($name)
    {
      $this->name = $name;
      return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
      return $this->description;
    }

    /**
     * @param string $description
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setDescription($description)
    {
      $this->description = $description;
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
     * @return \mod_collaborate\soap\generated\HtmlSession
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
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setEndTime(\DateTime $endTime)
    {
      $this->endTime = $endTime->format('Y-m-d\TH:i:s\Z');
      return $this;
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
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setBoundaryTime($boundaryTime)
    {
      $this->boundaryTime = $boundaryTime;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getNoEndDate()
    {
      return $this->noEndDate;
    }

    /**
     * @param boolean $noEndDate
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setNoEndDate($noEndDate)
    {
      $this->noEndDate = $noEndDate;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getAllowGuest()
    {
      return $this->allowGuest;
    }

    /**
     * @param boolean $allowGuest
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setAllowGuest($allowGuest)
    {
      $this->allowGuest = $allowGuest;
      return $this;
    }

    /**
     * @return string
     */
    public function getGuestUrl()
    {
      return $this->guestUrl;
    }

    /**
     * @param string $guestUrl
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setGuestUrl($guestUrl)
    {
      $this->guestUrl = $guestUrl;
      return $this;
    }

    /**
     * @return string
     */
    public function getGuestRole()
    {
      return $this->guestRole;
    }

    /**
     * @param string $guestRole
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setGuestRole($guestRole)
    {
      $this->guestRole = $guestRole;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getShowProfile()
    {
      return $this->showProfile;
    }

    /**
     * @param boolean $showProfile
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setShowProfile($showProfile)
    {
      $this->showProfile = $showProfile;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getCanShareVideo()
    {
      return $this->canShareVideo;
    }

    /**
     * @param boolean $canShareVideo
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setCanShareVideo($canShareVideo)
    {
      $this->canShareVideo = $canShareVideo;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getCanShareAudio()
    {
      return $this->canShareAudio;
    }

    /**
     * @param boolean $canShareAudio
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setCanShareAudio($canShareAudio)
    {
      $this->canShareAudio = $canShareAudio;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getCanPostMessage()
    {
      return $this->canPostMessage;
    }

    /**
     * @param boolean $canPostMessage
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setCanPostMessage($canPostMessage)
    {
      $this->canPostMessage = $canPostMessage;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getCanAnnotateWhiteboard()
    {
      return $this->canAnnotateWhiteboard;
    }

    /**
     * @param boolean $canAnnotateWhiteboard
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setCanAnnotateWhiteboard($canAnnotateWhiteboard)
    {
      $this->canAnnotateWhiteboard = $canAnnotateWhiteboard;
      return $this;
    }

    /**
     * @return HtmlAttendeeCollection[]
     */
    public function getHtmlAttendees()
    {
      return $this->htmlAttendees;
    }

    /**
     * @param HtmlAttendeeCollection[] $htmlAttendees
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setHtmlAttendees(array $htmlAttendees)
    {
      $this->htmlAttendees = $htmlAttendees;
      return $this;
    }

    /**
     * @return string
     */
    public function getGroupingList()
    {
      return $this->groupingList;
    }

    /**
     * @param string $groupingList
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setGroupingList($groupingList)
    {
      $this->groupingList = $groupingList;
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
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setCreatorId($creatorId)
    {
      $this->creatorId = $creatorId;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getMustBeSupervised()
    {
      return $this->mustBeSupervised;
    }

    /**
     * @param boolean $mustBeSupervised
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setMustBeSupervised($mustBeSupervised)
    {
      $this->mustBeSupervised = $mustBeSupervised;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getOpenChair()
    {
      return $this->openChair;
    }

    /**
     * @param boolean $openChair
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setOpenChair($openChair)
    {
      $this->openChair = $openChair;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getPermissionsOn()
    {
      return $this->permissionsOn;
    }

    /**
     * @param boolean $permissionsOn
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setPermissionsOn($permissionsOn)
    {
      $this->permissionsOn = $permissionsOn;
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
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setRaiseHandOnEnter($raiseHandOnEnter)
    {
      $this->raiseHandOnEnter = $raiseHandOnEnter;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getAllowInSessionInvitees()
    {
      return $this->allowInSessionInvitees;
    }

    /**
     * @param boolean $allowInSessionInvitees
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setAllowInSessionInvitees($allowInSessionInvitees)
    {
      $this->allowInSessionInvitees = $allowInSessionInvitees;
      return $this;
    }

    /**
     * @return string
     */
    public function getContextId()
    {
      return $this->contextId;
    }

    /**
     * @param string $contextId
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setContextId($contextId)
    {
      $this->contextId = $contextId;
      return $this;
    }

    /**
     * @return string
     */
    public function getContextTitle()
    {
      return $this->contextTitle;
    }

    /**
     * @param string $contextTitle
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setContextTitle($contextTitle)
    {
      $this->contextTitle = $contextTitle;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getRecordingEnabled()
    {
      return $this->recordingEnabled;
    }

    /**
     * @param boolean $recordingEnabled
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setRecordingEnabled($recordingEnabled)
    {
      $this->recordingEnabled = $recordingEnabled;
      return $this;
    }

    /**
     * @return string
     */
    public function getRecordingCallBackUrl()
    {
      return $this->recordingCallBackUrl;
    }

    /**
     * @param string $recordingCallBackUrl
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setRecordingCallBackUrl($recordingCallBackUrl)
    {
      $this->recordingCallBackUrl = $recordingCallBackUrl;
      return $this;
    }

    /**
     * @return string
     */
    public function getSessionType()
    {
      return $this->sessionType;
    }

    /**
     * @param string $sessionType
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setSessionType($sessionType)
    {
      $this->sessionType = $sessionType;
      return $this;
    }

    /**
     * @return string
     */
    public function getSessionRule()
    {
      return $this->sessionRule;
    }

    /**
     * @param string $sessionRule
     * @return \mod_collaborate\soap\generated\HtmlSession
     */
    public function setSessionRule($sessionRule)
    {
      $this->sessionRule = $sessionRule;
      return $this;
    }

}
