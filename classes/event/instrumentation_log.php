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
 * Instrumentation log.
 *
 * @package   local_student_course_enrolment
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_file_write\event;

defined('MOODLE_INTERNAL') || die();


class instrumentation_log extends \core\event\base
{
    /**
     * Init method.
     */
    protected function init()
    {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'logger_file_write';

    }

    /**
     * Return localised event name.
     *
     * @return string
     * @throws \coding_exception
     */
    public static function get_name(): string {
        return get_string('eventfilewritelogs', 'logstore_file_write');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description(): string {
        return "The user with id '$this->userid' logging plugin activities ";
    }

    protected function get_legacy_logdata(): array {
        return array(
            $this->userid,
            "logging plugin",
            "instrumentaion log"
        );
    }
    public static function get_other_mapping(): array {
        return [];
    }

}