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
 * SOAP API / element: SASDefaultAdapter
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_collaborate\soap\generated;

class SASDefaultAdapter extends \SoapClient
{

    /**
     * @var array $classmap The defined classes
     */
    private static $classmap = array (
      'BasicAuth' => 'mod_collaborate\\soap\\generated\\BasicAuth',
      'SuccessResponse' => 'mod_collaborate\\soap\\generated\\SuccessResponse',
      'ErrorResponse' => 'mod_collaborate\\soap\\generated\\ErrorResponse',
      'RecordingUrl' => 'mod_collaborate\\soap\\generated\\RecordingUrl',
      'HtmlRecordingUrl' => 'mod_collaborate\\soap\\generated\\HtmlRecordingUrl',
      'SessionUrl' => 'mod_collaborate\\soap\\generated\\SessionUrl',
      'UrlResponse' => 'mod_collaborate\\soap\\generated\\UrlResponse',
      'EmailBody' => 'mod_collaborate\\soap\\generated\\EmailBody',
      'EmailBodyResponse' => 'mod_collaborate\\soap\\generated\\EmailBodyResponse',
      'ConvertRecording' => 'mod_collaborate\\soap\\generated\\ConvertRecording',
      'GetMobilePlatforms' => 'mod_collaborate\\soap\\generated\\GetMobilePlatforms',
      'GetMobilePlatformResponseCollection' => 'mod_collaborate\\soap\\generated\\GetMobilePlatformResponseCollection',
      'MobilePlatformResponse' => 'mod_collaborate\\soap\\generated\\MobilePlatformResponse',
      'GetOptionLicenses' => 'mod_collaborate\\soap\\generated\\GetOptionLicenses',
      'GetOptionLicenseResponseCollection' => 'mod_collaborate\\soap\\generated\\GetOptionLicenseResponseCollection',
      'OptionLicenseResponse' => 'mod_collaborate\\soap\\generated\\OptionLicenseResponse',
      'OptionVariationNameCollection' => 'mod_collaborate\\soap\\generated\\OptionVariationNameCollection',
      'GetQuotaLimits' => 'mod_collaborate\\soap\\generated\\GetQuotaLimits',
      'GetQuotaLimitsResponseCollection' => 'mod_collaborate\\soap\\generated\\GetQuotaLimitsResponseCollection',
      'QuotaLimitsResponse' => 'mod_collaborate\\soap\\generated\\QuotaLimitsResponse',
      'ServerConfiguration' => 'mod_collaborate\\soap\\generated\\ServerConfiguration',
      'ServerConfigurationResponseCollection' => 'mod_collaborate\\soap\\generated\\ServerConfigurationResponseCollection',
      'ServerConfigurationResponse' => 'mod_collaborate\\soap\\generated\\ServerConfigurationResponse',
      'ServerQuotas' => 'mod_collaborate\\soap\\generated\\ServerQuotas',
      'ServerQuotasResponseCollection' => 'mod_collaborate\\soap\\generated\\ServerQuotasResponseCollection',
      'ServerQuotasResponse' => 'mod_collaborate\\soap\\generated\\ServerQuotasResponse',
      'SchedulingManager' => 'mod_collaborate\\soap\\generated\\SchedulingManager',
      'SchedulingManagerResponseCollection' => 'mod_collaborate\\soap\\generated\\SchedulingManagerResponseCollection',
      'SchedulingManagerResponse' => 'mod_collaborate\\soap\\generated\\SchedulingManagerResponse',
      'ServerVersions' => 'mod_collaborate\\soap\\generated\\ServerVersions',
      'ServerVersionResponseCollection' => 'mod_collaborate\\soap\\generated\\ServerVersionResponseCollection',
      'ServerVersionResponse' => 'mod_collaborate\\soap\\generated\\ServerVersionResponse',
      'UploadRepositoryContent' => 'mod_collaborate\\soap\\generated\\UploadRepositoryContent',
      'ListRepositoryMultimediaContent' => 'mod_collaborate\\soap\\generated\\ListRepositoryMultimediaContent',
      'ListRepositoryPresentationContent' => 'mod_collaborate\\soap\\generated\\ListRepositoryPresentationContent',
      'RemoveRepositoryMultimedia' => 'mod_collaborate\\soap\\generated\\RemoveRepositoryMultimedia',
      'RemoveRepositoryPresentation' => 'mod_collaborate\\soap\\generated\\RemoveRepositoryPresentation',
      'ListSessionContent' => 'mod_collaborate\\soap\\generated\\ListSessionContent',
      'SetApiCallbackUrl' => 'mod_collaborate\\soap\\generated\\SetApiCallbackUrl',
      'SetSessionMultimediaContent' => 'mod_collaborate\\soap\\generated\\SetSessionMultimediaContent',
      'SetSessionPresentationContent' => 'mod_collaborate\\soap\\generated\\SetSessionPresentationContent',
      'RemoveSessionMultimediaContent' => 'mod_collaborate\\soap\\generated\\RemoveSessionMultimediaContent',
      'RemoveSessionPresentationContent' => 'mod_collaborate\\soap\\generated\\RemoveSessionPresentationContent',
      'MultimediaResponseCollection' => 'mod_collaborate\\soap\\generated\\MultimediaResponseCollection',
      'MultimediaResponse' => 'mod_collaborate\\soap\\generated\\MultimediaResponse',
      'PresentationResponseCollection' => 'mod_collaborate\\soap\\generated\\PresentationResponseCollection',
      'PresentationResponse' => 'mod_collaborate\\soap\\generated\\PresentationResponse',
      'ListRecordingFiles' => 'mod_collaborate\\soap\\generated\\ListRecordingFiles',
      'ListRecordingFileResponseCollection' => 'mod_collaborate\\soap\\generated\\ListRecordingFileResponseCollection',
      'RecordingFileResponseCollection' => 'mod_collaborate\\soap\\generated\\RecordingFileResponseCollection',
      'RecordingFileResponse' => 'mod_collaborate\\soap\\generated\\RecordingFileResponse',
      'ListRecordings' => 'mod_collaborate\\soap\\generated\\ListRecordings',
      'RecordingResponseCollection' => 'mod_collaborate\\soap\\generated\\RecordingResponseCollection',
      'RecordingResponse' => 'mod_collaborate\\soap\\generated\\RecordingResponse',
      'ListRecordingLong' => 'mod_collaborate\\soap\\generated\\ListRecordingLong',
      'RecordingLongResponseCollection' => 'mod_collaborate\\soap\\generated\\RecordingLongResponseCollection',
      'RecordingLongResponse' => 'mod_collaborate\\soap\\generated\\RecordingLongResponse',
      'RecordingShort' => 'mod_collaborate\\soap\\generated\\RecordingShort',
      'RecordingShortResponseCollection' => 'mod_collaborate\\soap\\generated\\RecordingShortResponseCollection',
      'RecordingShortResponse' => 'mod_collaborate\\soap\\generated\\RecordingShortResponse',
      'HtmlSessionRecording' => 'mod_collaborate\\soap\\generated\\HtmlSessionRecording',
      'HtmlSessionRecordingResponseCollection' => 'mod_collaborate\\soap\\generated\\HtmlSessionRecordingResponseCollection',
      'HtmlSessionRecordingResponse' => 'mod_collaborate\\soap\\generated\\HtmlSessionRecordingResponse',
      'HtmlSessionOccurrence' => 'mod_collaborate\\soap\\generated\\HtmlSessionOccurrence',
      'HtmlSessionOccurrenceCollection' => 'mod_collaborate\\soap\\generated\\HtmlSessionOccurrenceCollection',
      'RemoveHtmlSessionOccurrence' => 'mod_collaborate\\soap\\generated\\RemoveHtmlSessionOccurrence',
      'UpdateHtmlSessionOccurrence' => 'mod_collaborate\\soap\\generated\\UpdateHtmlSessionOccurrence',
      'SetSession' => 'mod_collaborate\\soap\\generated\\SetSession',
      'SetHtmlSession' => 'mod_collaborate\\soap\\generated\\SetHtmlSession',
      'UpdateSession' => 'mod_collaborate\\soap\\generated\\UpdateSession',
      'UpdateSessionAttendees' => 'mod_collaborate\\soap\\generated\\UpdateSessionAttendees',
      'UpdateHtmlSessionAttendee' => 'mod_collaborate\\soap\\generated\\UpdateHtmlSessionAttendee',
      'UpdateHtmlSessionDetails' => 'mod_collaborate\\soap\\generated\\UpdateHtmlSessionDetails',
      'ListSession' => 'mod_collaborate\\soap\\generated\\ListSession',
      'ListHtmlSession' => 'mod_collaborate\\soap\\generated\\ListHtmlSession',
      'BuildHtmlSessionUrl' => 'mod_collaborate\\soap\\generated\\BuildHtmlSessionUrl',
      'SessionResponseCollection' => 'mod_collaborate\\soap\\generated\\SessionResponseCollection',
      'HtmlSessionCollection' => 'mod_collaborate\\soap\\generated\\HtmlSessionCollection',
      'SessionResponse' => 'mod_collaborate\\soap\\generated\\SessionResponse',
      'HtmlSession' => 'mod_collaborate\\soap\\generated\\HtmlSession',
      'ClearSessionUserList' => 'mod_collaborate\\soap\\generated\\ClearSessionUserList',
      'SessionAttendance' => 'mod_collaborate\\soap\\generated\\SessionAttendance',
      'SessionAttendanceResponseCollection' => 'mod_collaborate\\soap\\generated\\SessionAttendanceResponseCollection',
      'SessionAttendanceResponse' => 'mod_collaborate\\soap\\generated\\SessionAttendanceResponse',
      'HtmlSessionAttendance' => 'mod_collaborate\\soap\\generated\\HtmlSessionAttendance',
      'HtmlRoomCollection' => 'mod_collaborate\\soap\\generated\\HtmlRoomCollection',
      'HtmlRoom' => 'mod_collaborate\\soap\\generated\\HtmlRoom',
      'HtmlAttendeeCollection' => 'mod_collaborate\\soap\\generated\\HtmlAttendeeCollection',
      'HtmlAttendee' => 'mod_collaborate\\soap\\generated\\HtmlAttendee',
      'HtmlAttendeeLogCollection' => 'mod_collaborate\\soap\\generated\\HtmlAttendeeLogCollection',
      'HtmlAttendeeLog' => 'mod_collaborate\\soap\\generated\\HtmlAttendeeLog',
      'AttendeeResponseCollection' => 'mod_collaborate\\soap\\generated\\AttendeeResponseCollection',
      'AttendeeResponse' => 'mod_collaborate\\soap\\generated\\AttendeeResponse',
      'SendEmail' => 'mod_collaborate\\soap\\generated\\SendEmail',
      'RemoveSession' => 'mod_collaborate\\soap\\generated\\RemoveSession',
      'RemoveHtmlSession' => 'mod_collaborate\\soap\\generated\\RemoveHtmlSession',
      'RemoveRecording' => 'mod_collaborate\\soap\\generated\\RemoveRecording',
      'RemoveHtmlSessionRecording' => 'mod_collaborate\\soap\\generated\\RemoveHtmlSessionRecording',
      'ThirdPartyTelephonyParams' => 'mod_collaborate\\soap\\generated\\ThirdPartyTelephonyParams',
      'SetTelephony' => 'mod_collaborate\\soap\\generated\\SetTelephony',
      'TelephonyResponseItem' => 'mod_collaborate\\soap\\generated\\TelephonyResponseItem',
      'TelephonyResponseItemCollection' => 'mod_collaborate\\soap\\generated\\TelephonyResponseItemCollection',
      'GetTelephony' => 'mod_collaborate\\soap\\generated\\GetTelephony',
      'GetTelephonyLicenseInfo' => 'mod_collaborate\\soap\\generated\\GetTelephonyLicenseInfo',
      'GetTelephonyLicenseInfoResponse' => 'mod_collaborate\\soap\\generated\\GetTelephonyLicenseInfoResponse',
      'SetSessionTelephony' => 'mod_collaborate\\soap\\generated\\SetSessionTelephony',
      'SessionTelephony' => 'mod_collaborate\\soap\\generated\\SessionTelephony',
      'SessionTelephonyResponseCollection' => 'mod_collaborate\\soap\\generated\\SessionTelephonyResponseCollection',
      'SessionTelephonyResponse' => 'mod_collaborate\\soap\\generated\\SessionTelephonyResponse',
      'RecordingSecureSignOn' => 'mod_collaborate\\soap\\generated\\RecordingSecureSignOn',
      'status' => 'mod_collaborate\\soap\\generated\\status',
    );

    /**
     * @param array $options A array of config values
     * @param string $wsdl The wsdl file to use
     */
    public function __construct(array $options = array(), $wsdl = 'http://joule2.dev/mod/collaborate/wsdl.xml')
    {
      foreach (self::$classmap as $key => $value) {
        if (!isset($options['classmap'][$key])) {
          $options['classmap'][$key] = $value;
        }
      }
      $options = array_merge(array (
      'features' => 1,
    ), $options);
      parent::__construct($wsdl, $options);
    }

    /**
     * @param RecordingUrl $parameters
     * @return UrlResponse
     */
    public function BuildRecordingUrl(RecordingUrl $parameters)
    {
      return $this->__soapCall('BuildRecordingUrl', array($parameters));
    }

    /**
     * @param HtmlRecordingUrl $parameters
     * @return UrlResponse
     */
    public function BuildHtmlRecordingUrl(HtmlRecordingUrl $parameters)
    {
      return $this->__soapCall('BuildHtmlRecordingUrl', array($parameters));
    }

    /**
     * @param SessionUrl $parameters
     * @return UrlResponse
     */
    public function BuildSessionUrl(SessionUrl $parameters)
    {
      return $this->__soapCall('BuildSessionUrl', array($parameters));
    }

    /**
     * @param ConvertRecording $parameters
     * @return RecordingFileResponseCollection
     */
    public function ConvertRecording(ConvertRecording $parameters)
    {
      return $this->__soapCall('ConvertRecording', array($parameters));
    }

    /**
     * @param EmailBody $parameters
     * @return EmailBodyResponse
     */
    public function GetEmailBody(EmailBody $parameters)
    {
      return $this->__soapCall('GetEmailBody', array($parameters));
    }

    /**
     * @param GetMobilePlatforms $parameters
     * @return GetMobilePlatformResponseCollection
     */
    public function GetMobilePlatforms(GetMobilePlatforms $parameters)
    {
      return $this->__soapCall('GetMobilePlatforms', array($parameters));
    }

    /**
     * @param GetOptionLicenses $parameters
     * @return GetOptionLicenseResponseCollection
     */
    public function GetOptionLicenses(GetOptionLicenses $parameters)
    {
      return $this->__soapCall('GetOptionLicenses', array($parameters));
    }

    /**
     * @param GetQuotaLimits $parameters
     * @return GetQuotaLimitsResponseCollection
     */
    public function GetQuotaLimits(GetQuotaLimits $parameters)
    {
      return $this->__soapCall('GetQuotaLimits', array($parameters));
    }

    /**
     * @param ServerConfiguration $parameters
     * @return ServerConfigurationResponseCollection
     */
    public function GetServerConfiguration(ServerConfiguration $parameters)
    {
      return $this->__soapCall('GetServerConfiguration', array($parameters));
    }

    /**
     * @param ServerQuotas $parameters
     * @return ServerQuotasResponseCollection
     */
    public function GetServerQuotas(ServerQuotas $parameters)
    {
      return $this->__soapCall('GetServerQuotas', array($parameters));
    }

    /**
     * @param SchedulingManager $parameters
     * @return SchedulingManagerResponseCollection
     */
    public function GetSchedulingManager(SchedulingManager $parameters)
    {
      return $this->__soapCall('GetSchedulingManager', array($parameters));
    }

    /**
     * @param ServerVersions $parameters
     * @return ServerVersionResponseCollection
     */
    public function GetServerVersions(ServerVersions $parameters)
    {
      return $this->__soapCall('GetServerVersions', array($parameters));
    }

    /**
     * @param UploadRepositoryContent $parameters
     * @return MultimediaResponseCollection
     */
    public function UploadRepositoryMultimedia(UploadRepositoryContent $parameters)
    {
      return $this->__soapCall('UploadRepositoryMultimedia', array($parameters));
    }

    /**
     * @param UploadRepositoryContent $parameters
     * @return PresentationResponseCollection
     */
    public function UploadRepositoryPresentation(UploadRepositoryContent $parameters)
    {
      return $this->__soapCall('UploadRepositoryPresentation', array($parameters));
    }

    /**
     * @param ListRepositoryMultimediaContent $parameters
     * @return MultimediaResponseCollection
     */
    public function ListRepositoryMultimedia(ListRepositoryMultimediaContent $parameters)
    {
      return $this->__soapCall('ListRepositoryMultimedia', array($parameters));
    }

    /**
     * @param ListRepositoryPresentationContent $parameters
     * @return PresentationResponseCollection
     */
    public function ListRepositoryPresentation(ListRepositoryPresentationContent $parameters)
    {
      return $this->__soapCall('ListRepositoryPresentation', array($parameters));
    }

    /**
     * @param RemoveRepositoryMultimedia $parameters
     * @return SuccessResponse
     */
    public function RemoveRepositoryMultimedia(RemoveRepositoryMultimedia $parameters)
    {
      return $this->__soapCall('RemoveRepositoryMultimedia', array($parameters));
    }

    /**
     * @param RemoveRepositoryPresentation $parameters
     * @return SuccessResponse
     */
    public function RemoveRepositoryPresentation(RemoveRepositoryPresentation $parameters)
    {
      return $this->__soapCall('RemoveRepositoryPresentation', array($parameters));
    }

    /**
     * @param ListSessionContent $parameters
     * @return MultimediaResponseCollection
     */
    public function ListSessionMultimedia(ListSessionContent $parameters)
    {
      return $this->__soapCall('ListSessionMultimedia', array($parameters));
    }

    /**
     * @param ListSessionContent $parameters
     * @return PresentationResponseCollection
     */
    public function ListSessionPresentation(ListSessionContent $parameters)
    {
      return $this->__soapCall('ListSessionPresentation', array($parameters));
    }

    /**
     * @param SetApiCallbackUrl $parameters
     * @return SuccessResponse
     */
    public function SetApiCallbackUrl(SetApiCallbackUrl $parameters)
    {
      return $this->__soapCall('SetApiCallbackUrl', array($parameters));
    }

    /**
     * @param SetSessionMultimediaContent $parameters
     * @return SuccessResponse
     */
    public function SetSessionMultimedia(SetSessionMultimediaContent $parameters)
    {
      return $this->__soapCall('SetSessionMultimedia', array($parameters));
    }

    /**
     * @param SetSessionPresentationContent $parameters
     * @return SuccessResponse
     */
    public function SetSessionPresentation(SetSessionPresentationContent $parameters)
    {
      return $this->__soapCall('SetSessionPresentation', array($parameters));
    }

    /**
     * @param RemoveSessionMultimediaContent $parameters
     * @return SuccessResponse
     */
    public function RemoveSessionMultimedia(RemoveSessionMultimediaContent $parameters)
    {
      return $this->__soapCall('RemoveSessionMultimedia', array($parameters));
    }

    /**
     * @param RemoveSessionPresentationContent $parameters
     * @return SuccessResponse
     */
    public function RemoveSessionPresentation(RemoveSessionPresentationContent $parameters)
    {
      return $this->__soapCall('RemoveSessionPresentation', array($parameters));
    }

    /**
     * @param ListRecordingFiles $parameters
     * @return ListRecordingFileResponseCollection
     */
    public function ListRecordingFiles(ListRecordingFiles $parameters)
    {
      return $this->__soapCall('ListRecordingFiles', array($parameters));
    }

    /**
     * @param ListRecordings $parameters
     * @return RecordingResponseCollection
     */
    public function ListRecordings(ListRecordings $parameters)
    {
      return $this->__soapCall('ListRecordings', array($parameters));
    }

    /**
     * @param ListRecordingLong $parameters
     * @return RecordingLongResponseCollection
     */
    public function ListRecordingLong(ListRecordingLong $parameters)
    {
      return $this->__soapCall('ListRecordingLong', array($parameters));
    }

    /**
     * @param RecordingShort $parameters
     * @return RecordingShortResponseCollection
     */
    public function ListRecordingShort(RecordingShort $parameters)
    {
      return $this->__soapCall('ListRecordingShort', array($parameters));
    }

    /**
     * @param HtmlSessionRecording $parameters
     * @return HtmlSessionRecordingResponseCollection
     */
    public function ListHtmlSessionRecording(HtmlSessionRecording $parameters)
    {
      return $this->__soapCall('ListHtmlSessionRecording', array($parameters));
    }

    /**
     * @param ListSession $parameters
     * @return SessionResponseCollection
     */
    public function ListSession(ListSession $parameters)
    {
      return $this->__soapCall('ListSession', array($parameters));
    }

    /**
     * @param SessionAttendance $parameters
     * @return SessionAttendanceResponseCollection
     */
    public function ListSessionAttendance(SessionAttendance $parameters)
    {
      return $this->__soapCall('ListSessionAttendance', array($parameters));
    }

    /**
     * @param HtmlSessionAttendance $parameters
     * @return HtmlRoomCollection
     */
    public function ListHtmlSessionAttendance(HtmlSessionAttendance $parameters)
    {
      return $this->__soapCall('ListHtmlSessionAttendance', array($parameters));
    }

    /**
     * @param HtmlSessionOccurrence $parameters
     * @return HtmlSessionOccurrenceCollection
     */
    public function ListHtmlSessionOccurrences(HtmlSessionOccurrence $parameters)
    {
      return $this->__soapCall('ListHtmlSessionOccurrences', array($parameters));
    }

    /**
     * @param RemoveHtmlSessionOccurrence $parameters
     * @return SuccessResponse
     */
    public function RemoveHtmlSessionOccurrence(RemoveHtmlSessionOccurrence $parameters)
    {
      return $this->__soapCall('RemoveHtmlSessionOccurrence', array($parameters));
    }

    /**
     * @param UpdateHtmlSessionOccurrence $parameters
     * @return SuccessResponse
     */
    public function UpdateHtmlSessionOccurrence(UpdateHtmlSessionOccurrence $parameters)
    {
      return $this->__soapCall('UpdateHtmlSessionOccurrence', array($parameters));
    }

    /**
     * @param SetSession $parameters
     * @return SessionResponseCollection
     */
    public function SetSession(SetSession $parameters)
    {
      return $this->__soapCall('SetSession', array($parameters));
    }

    /**
     * @param UpdateSession $parameters
     * @return SessionResponseCollection
     */
    public function UpdateSession(UpdateSession $parameters)
    {
      return $this->__soapCall('UpdateSession', array($parameters));
    }

    /**
     * @param UpdateSessionAttendees $parameters
     * @return UrlResponse
     */
    public function UpdateSessionAttendee(UpdateSessionAttendees $parameters)
    {
      return $this->__soapCall('UpdateSessionAttendee', array($parameters));
    }

    /**
     * @param UpdateHtmlSessionAttendee $parameters
     * @return UrlResponse
     */
    public function UpdateHtmlSessionAttendee(UpdateHtmlSessionAttendee $parameters)
    {
      return $this->__soapCall('UpdateHtmlSessionAttendee', array($parameters));
    }

    /**
     * @param UpdateHtmlSessionDetails $parameters
     * @return HtmlSessionCollection
     */
    public function UpdateHtmlSession(UpdateHtmlSessionDetails $parameters)
    {
      return $this->__soapCall('UpdateHtmlSession', array($parameters));
    }

    /**
     * @param SetHtmlSession $parameters
     * @return HtmlSessionCollection
     */
    public function SetHtmlSession(SetHtmlSession $parameters)
    {
      return $this->__soapCall('SetHtmlSession', array($parameters));
    }

    /**
     * @param BuildHtmlSessionUrl $parameters
     * @return UrlResponse
     */
    public function BuildHtmlSessionUrl(BuildHtmlSessionUrl $parameters)
    {
      return $this->__soapCall('BuildHtmlSessionUrl', array($parameters));
    }

    /**
     * @param ListHtmlSession $parameters
     * @return HtmlSessionCollection
     */
    public function ListHtmlSession(ListHtmlSession $parameters)
    {
      return $this->__soapCall('ListHtmlSession', array($parameters));
    }

    /**
     * @param RemoveHtmlSession $parameters
     * @return SuccessResponse
     */
    public function RemoveHtmlSession(RemoveHtmlSession $parameters)
    {
      return $this->__soapCall('RemoveHtmlSession', array($parameters));
    }

    /**
     * @param SendEmail $parameters
     * @return SuccessResponse
     */
    public function SendEmail(SendEmail $parameters)
    {
      return $this->__soapCall('SendEmail', array($parameters));
    }

    /**
     * @param RemoveSession $parameters
     * @return SuccessResponse
     */
    public function RemoveSession(RemoveSession $parameters)
    {
      return $this->__soapCall('RemoveSession', array($parameters));
    }

    /**
     * @param RemoveRecording $parameters
     * @return SuccessResponse
     */
    public function RemoveRecording(RemoveRecording $parameters)
    {
      return $this->__soapCall('RemoveRecording', array($parameters));
    }

    /**
     * @param RemoveHtmlSessionRecording $parameters
     * @return SuccessResponse
     */
    public function RemoveHtmlSessionRecording(RemoveHtmlSessionRecording $parameters)
    {
      return $this->__soapCall('RemoveHtmlSessionRecording', array($parameters));
    }

    /**
     * @param SetTelephony $parameters
     * @return TelephonyResponseItemCollection
     */
    public function SetTelephony(SetTelephony $parameters)
    {
      return $this->__soapCall('SetTelephony', array($parameters));
    }

    /**
     * @param GetTelephony $parameters
     * @return TelephonyResponseItemCollection
     */
    public function GetTelephony(GetTelephony $parameters)
    {
      return $this->__soapCall('GetTelephony', array($parameters));
    }

    /**
     * @param GetTelephonyLicenseInfo $parameters
     * @return GetTelephonyLicenseInfoResponse
     */
    public function GetTelephonyLicenseInfo(GetTelephonyLicenseInfo $parameters)
    {
      return $this->__soapCall('GetTelephonyLicenseInfo', array($parameters));
    }

    /**
     * @param SetSessionTelephony $parameters
     * @return SessionTelephonyResponseCollection
     */
    public function SetSessionTelephony(SetSessionTelephony $parameters)
    {
      return $this->__soapCall('SetSessionTelephony', array($parameters));
    }

    /**
     * @param SessionTelephony $parameters
     * @return SessionTelephonyResponseCollection
     */
    public function ListSessionTelephony(SessionTelephony $parameters)
    {
      return $this->__soapCall('ListSessionTelephony', array($parameters));
    }

    /**
     * @param SessionTelephony $parameters
     * @return SuccessResponse
     */
    public function RemoveSessionTelephony(SessionTelephony $parameters)
    {
      return $this->__soapCall('RemoveSessionTelephony', array($parameters));
    }

    /**
     * @param RecordingSecureSignOn $parameters
     * @return SuccessResponse
     */
    public function SetRecordingSecureSignOn(RecordingSecureSignOn $parameters)
    {
      return $this->__soapCall('SetRecordingSecureSignOn', array($parameters));
    }

    /**
     * @param ClearSessionUserList $parameters
     * @return SuccessResponse
     */
    public function ClearSessionChairList(ClearSessionUserList $parameters)
    {
      return $this->__soapCall('ClearSessionChairList', array($parameters));
    }

    /**
     * @param ClearSessionUserList $parameters
     * @return SuccessResponse
     */
    public function ClearSessionNonChairList(ClearSessionUserList $parameters)
    {
      return $this->__soapCall('ClearSessionNonChairList', array($parameters));
    }

}
