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
 * Makes a class with private / protected methods testable.
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2016 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_collaborate\traits;

defined('MOODLE_INTERNAL') || die();

/**
 * Makes a class with private / protected methods testable.
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2016 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait testable_class {
    /**
     * Magic method for getting protected / private properties.
     * @param string $name
     * @return mixed
     * @throws \coding_exception
     */
    // @codingStandardsIgnoreLine
    public function __get($name) {
        $reflection = new \ReflectionObject($this);
        $parentreflection = $reflection->getParentClass();
        $property = $parentreflection->getProperty($name);
        $property->setAccessible(true);
        return $property->getValue($this);
    }

    /**
     * Magic method for setting protected / private properties.
     * @param string $name
     * @param mixed $value
     * @return mixed
     * @throws \coding_exception
     */
    // @codingStandardsIgnoreLine
    public function __set($name, $value) {
        $reflection = new \ReflectionObject($this);
        $parentreflection = $reflection->getParentClass();
        $property = $parentreflection->getProperty($name);
        $property->setAccessible(true);
        return $property->setValue($this, $value);
    }

    /**
     * Magic method to allow protected / private methods to be called.
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    // @codingStandardsIgnoreLine
    public function __call($name, $arguments) {
        $reflection = new \ReflectionObject($this);
        $parentreflection = $reflection->getParentClass();
        if ($parentreflection) {
            $method = $parentreflection->getMethod($name);
            $method->setAccessible(true);
        } else {
            $method = $reflection->getMethod($name);
            $method->setAccessible(true);;
        }
        return $method->invokeArgs($this, $arguments);
    }

    /**
     * Magic method to allow protected / private methods to be called.
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    // @codingStandardsIgnoreLine
    public static function __callStatic($name, $arguments) {
        $reflection = new \ReflectionClass(__CLASS__);
        $parentreflection = $reflection->getParentClass();
        $method = $parentreflection->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs(null, $arguments);
    }

}
