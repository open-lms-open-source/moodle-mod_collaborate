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
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * View services
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_collaborate\renderables;

defined('MOODLE_INTERNAL') || die();

/**
 * Data structure representing a user picture.
 *
 * @copyright 2009 Nicolas Connault, 2010 Petr Skoda
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Modle 2.0
 * @package core
 * @category output
 */
class collab_picture implements \renderable {
    /**
     * @var array List of mandatory fields in user record here. (do not include
     * TEXT columns because it would break SELECT DISTINCT in MSSQL and ORACLE)
     */
    protected static $fields = array('id', 'picture', 'firstname', 'lastname', 'firstnamephonetic', 'lastnamephonetic',
        'middlename', 'alternatename', 'imagealt', 'email');

    /**
     * @var stdClass A user object with at least fields all columns specified
     * in $fields array constant set.
     */
    public $user;

    /**
     * @var int Size in pixels. Special values are (true/1 = 100px) and
     * (false/0 = 35px)
     * for backward compatibility.
     * This was modified so the user picture in Collaborate have an adequate size, since Collaborate avatar container only have 200px width.
     */
    public $size = 200;

    /**
     * @var string Image class attribute
     */
    public $class = 'userpicture';

    /**
     * @var bool Whether to be visible to screen readers.
     */
    public $visibletoscreenreaders = true;

    /**
     * User picture constructor.
     *
     * @param \stdClass $user user record with at least id, picture, imagealt, firstname and lastname set.
     *                 It is recommended to add also contextid of the user for performance reasons.
     */
    public function __construct(\stdClass $user) {
        global $DB;

        if (empty($user->id)) {
            throw new coding_exception('User id is required when printing user avatar image.');
        }

        // only touch the DB if we are missing data and complain loudly...
        $needrec = false;
        foreach (self::$fields as $field) {
            if (!array_key_exists($field, $user)) {
                $needrec = true;
                debugging('Missing '.$field.' property in $user object, this is a performance problem that needs to be fixed by a developer. '
                    .'Please use collab_picture::fields() to get the full list of required fields.', DEBUG_DEVELOPER);
                break;
            }
        }

        if ($needrec) {
            $this->user = $DB->get_record('user', array('id'=>$user->id), self::fields(), MUST_EXIST);
        } else {
            $this->user = clone($user);
        }
    }

    /**
     * Works out the URL for the users picture.
     *
     * This method is recommended as it avoids costly redirects of user pictures
     * if requests are made for non-existent files etc.
     *
     * @param \moodle_page $page
     * @param renderer_base $renderer
     * @return moodle_url
     */
    public function get_url(\moodle_page $page, renderer_base $renderer = null) {
        global $CFG;

        if (is_null($renderer)) {
            $renderer = $page->get_renderer('core');
        }

        // Sort out the filename and size. Size is only required for the gravatar.
        // implementation presently.
        if (empty($this->size)) {
            $filename = 'f2';
            $size = 35;
        } else if ($this->size === true or $this->size == 1) {
            $filename = 'f1';
            $size = 100;
        } else if ($this->size > 100) {
            $filename = 'f3';
            $size = (int)$this->size;
        } else if ($this->size >= 50) {
            $filename = 'f1';
            $size = (int)$this->size;
        } else {
            $filename = 'f2';
            $size = (int)$this->size;
        }

        $defaulturl = $renderer->image_url('u/'.$filename); // default image

        if ((!empty($CFG->forcelogin) and !isloggedin()) ||
            (!empty($CFG->forceloginforprofileimage) && (!isloggedin() || isguestuser()))) {
            // Protect images if login required and not logged in;
            // also if login is required for profile images and is not logged in or guest
            // do not use require_login() because it is expensive and not suitable here anyway.
            return $defaulturl;
        }

        // First try to detect deleted users - but do not read from database for performance reasons!
        if (!empty($this->user->deleted) or strpos($this->user->email, '@') === false) {
            // All deleted users should have email replaced by md5 hash,
            // all active users are expected to have valid email.
            return $defaulturl;
        }

        // Did the user upload a picture?
        if ($this->user->picture > 0) {
            if (!empty($this->user->contextid)) {
                $contextid = $this->user->contextid;
            } else {
                $context = \context_user::instance($this->user->id, IGNORE_MISSING);
                if (!$context) {
                    // This must be an incorrectly deleted user, all other users have context.
                    return $defaulturl;
                }
                $contextid = $context->id;
            }

            $path = '/';
            if (clean_param($page->theme->name, PARAM_THEME) == $page->theme->name) {
                // We append the theme name to the file path if we have it so that
                // in the circumstance that the profile picture is not available
                // when the user actually requests it they still get the profile
                // picture for the correct theme.
                $path .= $page->theme->name.'/';
            }
            // Set the image URL to the URL for the uploaded file and return.
            $url = \moodle_url::make_pluginfile_url($contextid, 'user', 'icon', NULL, $path, $filename);
            $url->param('rev', $this->user->picture);
            return $url;
        }

        if ($this->user->picture == 0 and !empty($CFG->enablegravatar)) {
            // Normalise the size variable to acceptable bounds
            if ($size < 1 || $size > 512) {
                $size = 35;
            }
            // Hash the users email address
            $md5 = md5(strtolower(trim($this->user->email)));
            // Build a gravatar URL with what we know.

            // Find the best default image URL we can (MDL-35669)
            if (empty($CFG->gravatardefaulturl)) {
                $absoluteimagepath = $page->theme->resolve_image_location('u/'.$filename, 'core');
                if (strpos($absoluteimagepath, $CFG->dirroot) === 0) {
                    $gravatardefault = $CFG->wwwroot . substr($absoluteimagepath, strlen($CFG->dirroot));
                } else {
                    $gravatardefault = $CFG->wwwroot . '/pix/u/' . $filename . '.png';
                }
            } else {
                $gravatardefault = $CFG->gravatardefaulturl;
            }

            // If the currently requested page is https then we'll return an
            // https gravatar page.
            if (is_https()) {
                $gravatardefault = str_replace($CFG->wwwroot, $CFG->httpswwwroot, $gravatardefault); // Replace by secure url.
                return new \moodle_url("https://secure.gravatar.com/avatar/{$md5}", array('s' => $size, 'd' => $gravatardefault));
            } else {
                return new \moodle_url("http://www.gravatar.com/avatar/{$md5}", array('s' => $size, 'd' => $gravatardefault));
            }
        }

        return $defaulturl;
    }
}
