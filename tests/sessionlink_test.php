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
 * Tests for sessionlink class.
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2017 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use mod_collaborate\testables\sessionlink;
use mod_collaborate\soap\fakeapi;
use mod_collaborate\soap\generated\ListHtmlSession;

class  mod_collaborate_sessionlink_testcase extends advanced_testcase {

    public function test_ensure_session_link() {

        global $DB;

        $this->resetAfterTest();

        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $modgen = $gen->get_plugin_generator('mod_collaborate');

        $collaborate = $modgen->create_instance((object) ['course' => $course->id]);
        $sessionlink = (object) ['collaborateid' => $collaborate->id, 'sessionid' => $collaborate->sessionid];

        $sessionlinkrow = sessionlink::ensure_session_link($collaborate, $sessionlink, $course);
        $this->assertEquals($sessionlink->collaborateid, $sessionlinkrow->collaborateid);
        $this->assertEquals($sessionlink->sessionid, $sessionlinkrow->sessionid);
    }

    public function test_get_group_session_link() {
        global $DB;

        $this->resetAfterTest();

        $collaborate = (object) ['id' => 1];
        $sessionlink = (object) ['collaborateid' => $collaborate->id, 'sessionid' => 100, 'groupid' => 1000];
        $DB->insert_record('collaborate_sessionlink', $sessionlink);
        $sessionlinkrow = sessionlink::get_group_session_link($collaborate, $sessionlink->groupid);
        $this->assertEquals($sessionlink->collaborateid, $sessionlinkrow->collaborateid);
        $this->assertEquals($sessionlink->sessionid, $sessionlinkrow->sessionid);
        $this->assertEquals($sessionlink->groupid, $sessionlinkrow->groupid);
    }

    public function test_apply_session_links_no_groups() {
        global $DB;

        $this->resetAfterTest();

        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $modgen = $gen->get_plugin_generator('mod_collaborate');
        $collaborate = $modgen->create_instance((object) ['course' => $course->id, 'sessionid' => null]);
        $linkcreated = sessionlink::apply_session_links($collaborate);
        $this->assertTrue($linkcreated);
        $collaborate = $DB->get_record('collaborate', ['id' => $collaborate->id]);
        $this->assertNotEmpty($collaborate->sessionid);
        $sessionlink = $DB->get_record('collaborate_sessionlink', ['collaborateid' => $collaborate->id, 'groupid' => null]);
        $this->assertEquals($collaborate->sessionid, $sessionlink->sessionid);
    }

    public function test_apply_session_links_groups() {
        global $DB;

        $this->resetAfterTest();

        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $group1 = $gen->create_group(array('courseid' => $course->id, 'name' => 'group1'));
        $group2 = $gen->create_group(array('courseid' => $course->id, 'name' => 'group2'));

        $modgen = $gen->get_plugin_generator('mod_collaborate');
        $collabdata = (object) [
            'course'    => $course->id,
            'groupmode' => SEPARATEGROUPS
        ];
        $collaborate = $modgen->create_instance($collabdata);
        $linkscreated = sessionlink::apply_session_links($collaborate);
        $this->assertTrue($linkscreated);
        $collaborate = $DB->get_record('collaborate', ['id' => $collaborate->id]);
        $this->assertNotEmpty($collaborate->sessionid);
        $sessionlink = $DB->get_record('collaborate_sessionlink',
                ['collaborateid' => $collaborate->id, 'groupid' => null]
        );
        $this->assertEquals($collaborate->sessionid, $sessionlink->sessionid);
        $gplink1 = sessionlink::get_group_session_link($collaborate, $group1->id);
        $this->assertNotNull($gplink1);
        $gplink2 = sessionlink::get_group_session_link($collaborate, $group2->id);
        $this->assertNotNull($gplink2);

    }

    public function test_delete_sessions() {
        global $DB;

        $this->resetAfterTest();

        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $group1 = $gen->create_group(array('courseid' => $course->id, 'name' => 'group1'));
        $group2 = $gen->create_group(array('courseid' => $course->id, 'name' => 'group2'));

        $modgen = $gen->get_plugin_generator('mod_collaborate');
        $collabdata = (object) [
            'course'    => $course->id,
            'sessionid' => null,
            'groupmode' => SEPARATEGROUPS
        ];
        $collaborate = $modgen->create_instance($collabdata);

        $linkscreated = sessionlink::apply_session_links($collaborate);
        $this->assertTrue($linkscreated);

        $mainlink = $DB->get_record('collaborate_sessionlink',
                ['collaborateid' => $collaborate->id, 'groupid' => null]
        );
        $group1link = $DB->get_record('collaborate_sessionlink',
                ['collaborateid' => $collaborate->id, 'groupid' => $group1->id]
        );
        $group2link = $DB->get_record('collaborate_sessionlink',
            ['collaborateid' => $collaborate->id, 'groupid' => $group2->id]
        );
        $this->assertNotEmpty($mainlink);
        $this->assertNotEmpty($group1link);
        $this->assertNotEmpty($group2link);

        sessionlink::delete_sessions($collaborate->id);

        $mainlink = $DB->get_record('collaborate_sessionlink',
            ['collaborateid' => $collaborate->id, 'groupid' => null]
        );
        $group1link = $DB->get_record('collaborate_sessionlink',
            ['collaborateid' => $collaborate->id, 'groupid' => $group1->id]
        );
        $group2link = $DB->get_record('collaborate_sessionlink',
            ['collaborateid' => $collaborate->id, 'groupid' => $group2->id]
        );
        $this->assertEmpty($mainlink);
        $this->assertEmpty($group1link);
        $this->assertEmpty($group2link);
    }

    public function test_delete_sessions_for_group() {
        global $DB;

        $this->resetAfterTest();

        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $group1 = $gen->create_group(array('courseid' => $course->id, 'name' => 'group1'));
        $group2 = $gen->create_group(array('courseid' => $course->id, 'name' => 'group2'));

        $modgen = $gen->get_plugin_generator('mod_collaborate');
        $collabdata = (object) [
            'course'    => $course->id,
            'sessionid' => null,
            'groupmode' => SEPARATEGROUPS
        ];
        $collaborate = $modgen->create_instance($collabdata);

        $linkscreated = sessionlink::apply_session_links($collaborate);
        $this->assertTrue($linkscreated);

        $group1link = $DB->get_record('collaborate_sessionlink',
            ['collaborateid' => $collaborate->id, 'groupid' => $group1->id]
        );
        $group2link = $DB->get_record('collaborate_sessionlink',
            ['collaborateid' => $collaborate->id, 'groupid' => $group2->id]
        );
        $this->assertNotEmpty($group1link);
        $this->assertNotEmpty($group2link);

        // Just delete sessions for 1 group (group2).
        sessionlink::delete_sessions_for_group($group2->id);

        $mainlink = $DB->get_record('collaborate_sessionlink',
            ['collaborateid' => $collaborate->id, 'groupid' => null]
        );
        $group1link = $DB->get_record('collaborate_sessionlink',
            ['collaborateid' => $collaborate->id, 'groupid' => $group1->id]
        );
        $group2link = $DB->get_record('collaborate_sessionlink',
            ['collaborateid' => $collaborate->id, 'groupid' => $group2->id]
        );

        // Make sure that all session links are present except for group2.
        $this->assertNotEmpty($mainlink);
        $this->assertNotEmpty($group1link);
        $this->assertEmpty($group2link);
    }

    public function test_update_sessions_for_group() {
        global $DB;

        $this->resetAfterTest();

        $api = fakeapi::get_api();

        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $group1 = $gen->create_group(array('courseid' => $course->id, 'name' => 'group1'));
        $group2 = $gen->create_group(array('courseid' => $course->id, 'name' => 'group2'));

        $modgen = $gen->get_plugin_generator('mod_collaborate');
        $collabdata = (object) [
            'course'    => $course->id,
            'sessionid' => null,
            'groupmode' => SEPARATEGROUPS
        ];
        $collaborate = $modgen->create_instance($collabdata);

        $linkscreated = sessionlink::apply_session_links($collaborate);
        $this->assertTrue($linkscreated);

        // Assert existing session name for group2 as expected.
        $expected = $collaborate->name.' ('.$group2->name.')';
        $groupsesslink = sessionlink::get_group_session_link($collaborate, $group2->id);
        $params = new ListHtmlSession();
        $params->setSessionId($groupsesslink->sessionid);
        $sessioncollection = $api->ListHtmlSession($params);
        $session = $sessioncollection->getHtmlSession()[0];
        $this->assertEquals($expected, $session->getName());

        // Rename group2.
        $group2modified = clone $group2;
        $group2modified->name = 'group2 changed';
        $DB->update_record('groups', $group2modified);
        sessionlink::update_sessions_for_group($group2->id);
        $groupsesslink = sessionlink::get_group_session_link($collaborate, $group2->id);

        // Assert modified group2 name is carried over to session.
        $expected = $collaborate->name.' ('.$group2modified->name.')';
        $groupsesslink = sessionlink::get_group_session_link($collaborate, $group2->id);
        $params = new ListHtmlSession();
        $params->setSessionId($groupsesslink->sessionid);
        $sessioncollection = $api->ListHtmlSession($params);
        $session = $sessioncollection->getHtmlSession()[0];
        $this->assertEquals($expected, $session->getName());
    }

    public function test_task_cleanup_failed_deletions() {
        global $DB;

        $this->resetAfterTest();

        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $gen->create_group(array('courseid' => $course->id, 'name' => 'group1'));
        $gen->create_group(array('courseid' => $course->id, 'name' => 'group2'));

        $modgen = $gen->get_plugin_generator('mod_collaborate');
        $collabdata = (object) [
            'course'    => $course->id,
            'sessionid' => null,
            'groupmode' => SEPARATEGROUPS
        ];
        $collaborate = $modgen->create_instance($collabdata);

        $linkscreated = sessionlink::apply_session_links($collaborate);
        $this->assertTrue($linkscreated);

        $links = $DB->get_records('collaborate_sessionlink');
        foreach ($links as $link) {
            $link->deletionattempted++;
            $DB->update_record('collaborate_sessionlink', $link);
        }

        sessionlink::task_cleanup_failed_deletions();
        $links = $DB->get_records('collaborate_sessionlink');
        $this->assertEmpty($links);
    }

    /**
     * Do these session links contain the specific groupid?
     * @param $links
     * @param null|int $groupid - note, null means the instance level link (no group).
     * @return bool
     */
    private function links_contain_group($links, $groupid = null) {
        foreach ($links as $link) {
            if ($link->groupid == $groupid) {
                return true;
            }
        }
        return false;
    }

    /**
     * Test active links for someone who can access all groups.
     * @throws coding_exception
     */
    public function test_my_active_links_aag() {
        global $DB;

        $this->resetAfterTest();

        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $group1 = $gen->create_group(array('courseid' => $course->id, 'name' => 'group1'));
        $group2 = $gen->create_group(array('courseid' => $course->id, 'name' => 'group2'));

        $modgen = $gen->get_plugin_generator('mod_collaborate');
        $collabdata = (object) [
            'course'    => $course->id,
            'sessionid' => null,
            'groupmode' => SEPARATEGROUPS
        ];
        $collaborate = $modgen->create_instance($collabdata);
        list($course, $cm) = get_course_and_cm_from_instance($collaborate, 'collaborate');

        // Enrol teacher to created course.
        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');

        $linkscreated = sessionlink::apply_session_links($collaborate);
        $this->assertTrue($linkscreated);

        // Teachers get all session links for collab instance and all groups.
        $this->setUser($teacher);

        $links = sessionlink::my_active_links($collaborate, $cm);
        // 1 Link for course, 2 for groups.
        $this->assertCount(3, $links);
        $this->assertTrue($this->links_contain_group($links, null));
        $this->assertTrue($this->links_contain_group($links, $group1->id));
        $this->assertTrue($this->links_contain_group($links, $group2->id));

        // Make sure that adding a teacher to group has no affect on the links returned - they should still see all.
        $gen->create_group_member(['userid' => $teacher->id, 'groupid' => $group1->id]);
        $links = sessionlink::my_active_links($collaborate, $cm);
        $this->assertCount(3, $links);
        $this->assertTrue($this->links_contain_group($links, null));
        $this->assertTrue($this->links_contain_group($links, $group1->id));
        $this->assertTrue($this->links_contain_group($links, $group2->id));

        // Mark one group link to have an outstanding deletion.
        $link = $DB->get_record('collaborate_sessionlink',
                [
                    'collaborateid' => $collaborate->id,
                    'groupid'       => $group1->id
                ]
        );
        $link->deletionattempted = 1;
        $DB->update_record('collaborate_sessionlink', $link);

        $links = sessionlink::my_active_links($collaborate, $cm);
        // 1 Link for course, 1 for groups.
        $this->assertCount(2, $links);
        $this->assertTrue($this->links_contain_group($links, null));
        $this->assertFalse($this->links_contain_group($links, $group1->id));
        $this->assertTrue($this->links_contain_group($links, $group2->id));

    }

    /**
     * Test active links for someone who cant access all groups.
     * @throws coding_exception
     */
    public function test_my_active_links_no_aag() {
        global $DB;

        $this->resetAfterTest();

        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $group1 = $gen->create_group(array('courseid' => $course->id, 'name' => 'group1'));
        $group2 = $gen->create_group(array('courseid' => $course->id, 'name' => 'group2'));

        $modgen = $gen->get_plugin_generator('mod_collaborate');
        $collabdata = (object) [
            'course'    => $course->id,
            'sessionid' => null,
            'groupmode' => SEPARATEGROUPS
        ];
        $collaborate = $modgen->create_instance($collabdata);
        list($course, $cm) = get_course_and_cm_from_instance($collaborate, 'collaborate');

        // Enrol student to created course.
        $student = $this->getDataGenerator()->create_user();
        $gen->enrol_user($student->id, $course->id, 'student');

        $linkscreated = sessionlink::apply_session_links($collaborate);
        $this->assertTrue($linkscreated);

        // Students only get session links for collab instance if they aren't in a group.
        $this->setUser($student);

        $links = sessionlink::my_active_links($collaborate, $cm);
        // 1 Link for course, none for groups.
        $this->assertCount(1, $links);
        $this->assertTrue($this->links_contain_group($links, null));
        $this->assertFalse($this->links_contain_group($links, $group1->id));
        $this->assertFalse($this->links_contain_group($links, $group2->id));

        // Add student to group1 and assert only get link for group1.
        $gen->create_group_member(['userid' => $student->id, 'groupid' => $group1->id]);
        $links = sessionlink::my_active_links($collaborate, $cm);
        $this->assertCount(2, $links);
        $this->assertTrue($this->links_contain_group($links, null));
        $this->assertTrue($this->links_contain_group($links, $group1->id));
        $this->assertFalse($this->links_contain_group($links, $group2->id));

        // Add student to group2 and assert get links for group1 and group2.
        $gen->create_group_member(['userid' => $student->id, 'groupid' => $group2->id]);
        $links = sessionlink::my_active_links($collaborate, $cm);
        $this->assertCount(3, $links);
        $this->assertTrue($this->links_contain_group($links, null));
        $this->assertTrue($this->links_contain_group($links, $group1->id));
        $this->assertTrue($this->links_contain_group($links, $group2->id));

        // Mark one group link to have an outstanding deletion.
        $link = $DB->get_record('collaborate_sessionlink',
            [
                'collaborateid' => $collaborate->id,
                'groupid'       => $group1->id
            ]
        );
        $link->deletionattempted = 1;
        $DB->update_record('collaborate_sessionlink', $link);

        $links = sessionlink::my_active_links($collaborate, $cm);
        // Assert 1 Link for group 2 and group1 deleted.
        $this->assertCount(2, $links);
        $this->assertTrue($this->links_contain_group($links, null));
        $this->assertFalse($this->links_contain_group($links, $group1->id));
        $this->assertTrue($this->links_contain_group($links, $group2->id));
    }

    public function test_get_titles_by_sessionids() {
        $this->resetAfterTest();

        $this->setAdminUser();

        $gen = $this->getDataGenerator();
        $course = $gen->create_course();

        $modgen = $gen->get_plugin_generator('mod_collaborate');
        $collabdata = (object) [
            'course'    => $course->id,
            'sessionid' => null,
            'groupmode' => SEPARATEGROUPS,
            'name' => 'Z Collaborate test' // Z is for checking ordering.
        ];
        $collaborate = $modgen->create_instance($collabdata);
        list($course, $cm) = get_course_and_cm_from_instance($collaborate, 'collaborate');

        $group1 = $gen->create_group(array('courseid' => $course->id, 'name' => 'group1'));
        $group2 = $gen->create_group(array('courseid' => $course->id, 'name' => 'group2'));

        $linkscreated = sessionlink::apply_session_links($collaborate);
        $this->assertTrue($linkscreated);

        $sessionids = [];
        $links = sessionlink::my_active_links($collaborate, $cm);
        foreach ($links as $link) {
            $sessionids[] = $link->sessionid;
        }

        $sessiontitles = sessionlink::get_titles_by_sessionids($sessionids, $collaborate->sessionid);
        $group1link = sessionlink::get_group_session_link($collaborate, $group1->id);
        $group2link = sessionlink::get_group_session_link($collaborate, $group2->id);
        // Test that main session title is always at the top:.
        $this->assertEquals('Z Collaborate test', reset($sessiontitles));
        $this->assertEquals('Z Collaborate test', $sessiontitles['_'.$collaborate->sessionid]);
        $this->assertEquals('Group group1', $sessiontitles['_'.$group1link->sessionid]);
        $this->assertEquals('Group group2', $sessiontitles['_'.$group2link->sessionid]);
    }

    /**
     * @param bool $sessionid
     */
    private function assert_session_link_row_by_sessionid_or_sessionuid($sessionid = true) {
        global $DB;

        $this->resetAfterTest(true);

        $object = (object) [
            'collaborateid' => 1,
            'sessionid' => 1234,
            'sessionuid' => 'ABCD'
        ];
        $DB->insert_record('collaborate_sessionlink', $object);
        if ($sessionid) {
            $row = sessionlink::get_session_link_row_by_sessionuid('ABCD');
        } else {
            $row = sessionlink::get_session_link_row_by_sessionid(1234);
        }
        foreach ((array) $object as $key => $val) {
            $this->assertEquals($val, $row->$key);
        }
    }

    public function test_get_session_link_row_by_sessionid() {
        $this->assert_session_link_row_by_sessionid_or_sessionuid(true);
    }

    public function test_get_session_link_row_by_sessionuid() {
        $this->assert_session_link_row_by_sessionid_or_sessionuid(false);
    }
}


