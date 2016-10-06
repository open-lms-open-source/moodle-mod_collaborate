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
 * Generate SOAP class files for this plugin.
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require_once(__DIR__.'../../../config.php');
require(__DIR__.'/vendor/autoload.php');

$wsdl = 'http://testing.dev/apollo.xml?wsdl';
$outputdir = __DIR__.'/classes/soap/generated';

// Pull complex types out of wsdl which contain 'html' (apollo api methods).
$wsdlcontents = file_get_contents($wsdl);
if (!$wsdlcontents) {
    die ('Failed to load wsdl '.$wsdl);
}
$wsdlxe = new simpleXMLElement($wsdlcontents);

// Remove doc elements (causing issues with wsdl parser).
$docels = $wsdlxe->xpath('//wsdl:documentation');
$docelsarr = [];
foreach ($docels as $docel) {
    $docelsarr[] = $docel;
}
foreach ($docelsarr as $docel) {
    unset($docel[0]);
}
// Create local wsdl.
$wsdlxe->asXML(__DIR__.'/wsdl.xml');

$whitelist = ['Html', 'Apollo', 'UrlResponse', 'ServerConfiguration', 'Success'];

$htmlels = $wsdlxe->xpath('//xs:complexType | //xs:element');
$soapclasses = ['SASDefaultAdapter'];
foreach ($htmlels as $htmlel) {
    $name = '' . $htmlel->attributes()['name'];
    foreach ($whitelist as $witem) {
        if (stripos($name, $witem) !== false) {
            $soapclasses[] = $name;
            break;
        }
    }
}

$localwsdl = $CFG->wwwroot.'/mod/collaborate/wsdl.xml';
$generator = new \Wsdl2PhpGenerator\Generator();
$generator->generate(
    new \Wsdl2PhpGenerator\Config(array(
        'inputFile' => $localwsdl,
        'outputDir' => $outputdir,
        'namespaceName' => 'mod_collaborate\soap\generated',
        'classNames' => implode(',' , $soapclasses)
    ))
);

// Remove the autoloader, we don't need it!
unlink($outputdir.'/autoload.php');

echo "\n".'Done'."\n";