<?php

/**
 * Steps definitions for Collaborate module.
 *
 * @package   mod_collaborate
 * @category  test
 * @copyright Copyright (c) 2016 Blackboard Inc. (http://www.blackboard.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

class behat_mod_collaborate extends behat_base {

    /**
     * Returns to Moodle tab after Joining collab session.
     * @Given /^I change to main window$/
     */

    public function i_change_to_main_window()
    {
        global $CFG;
        $session = $this->getSession();
        $mainwindow = $session->getWindowName();
        $session->switchToWindow($mainwindow);
    }
}