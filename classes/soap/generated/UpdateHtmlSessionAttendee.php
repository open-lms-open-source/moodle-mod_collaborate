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
 * SOAP API / element: UpdateHtmlSessionAttendee
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_collaborate\soap\generated;

class UpdateHtmlSessionAttendee
{

    /**
     * @var int $sessionId
     */
    protected $sessionId = null;

    /**
     * @var HtmlAttendee $htmlAttendee
     */
    protected $htmlAttendee = null;

    /**
     * @var string $locale
     */
    protected $locale = null;

    /**
     * @var string $returnUrl
     */
    protected $returnUrl = null;

    /**
     * @var string $reconnectUrl
     */
    protected $reconnectUrl = null;

    /**
     * @var string $originDomain
     */
    protected $originDomain = null;

    /**
     * @param int $sessionId
     * @param HtmlAttendee $htmlAttendee
     */
    public function __construct($sessionId, $htmlAttendee)
    {
      $this->sessionId = $sessionId;
      $this->htmlAttendee = $htmlAttendee;
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
     * @return \mod_collaborate\soap\generated\UpdateHtmlSessionAttendee
     */
    public function setSessionId($sessionId)
    {
      $this->sessionId = $sessionId;
      return $this;
    }

    /**
     * @return HtmlAttendee
     */
    public function getHtmlAttendee()
    {
      return $this->htmlAttendee;
    }

    /**
     * @param HtmlAttendee $htmlAttendee
     * @return \mod_collaborate\soap\generated\UpdateHtmlSessionAttendee
     */
    public function setHtmlAttendee($htmlAttendee)
    {
      $this->htmlAttendee = $htmlAttendee;
      return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
      return $this->locale;
    }

    /**
     * @param string $locale
     * @return \mod_collaborate\soap\generated\UpdateHtmlSessionAttendee
     */
    public function setLocale($locale)
    {
      $this->locale = $locale;
      return $this;
    }

    /**
     * @return string
     */
    public function getReturnUrl()
    {
      return $this->returnUrl;
    }

    /**
     * @param string $returnUrl
     * @return \mod_collaborate\soap\generated\UpdateHtmlSessionAttendee
     */
    public function setReturnUrl($returnUrl)
    {
      $this->returnUrl = $returnUrl;
      return $this;
    }

    /**
     * @return string
     */
    public function getReconnectUrl()
    {
      return $this->reconnectUrl;
    }

    /**
     * @param string $reconnectUrl
     * @return \mod_collaborate\soap\generated\UpdateHtmlSessionAttendee
     */
    public function setReconnectUrl($reconnectUrl)
    {
      $this->reconnectUrl = $reconnectUrl;
      return $this;
    }

    /**
     * @return string
     */
    public function getOriginDomain()
    {
      return $this->originDomain;
    }

    /**
     * @param string $originDomain
     * @return \mod_collaborate\soap\generated\UpdateHtmlSessionAttendee
     */
    public function setOriginDomain($originDomain)
    {
      $this->originDomain = $originDomain;
      return $this;
    }

}
