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
 *logstore_file_write/writer.
 *
 * @package    logstore_standard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_file_write\log;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/backup/util/interfaces/checksumable.class.php');
require_once($CFG->dirroot . '/backup/backup.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/base_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/error_log_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/file_logger.class.php');

use file_logger;
use backup;

class store implements \tool_log\log\writer{
    use \tool_log\helper\store,
        \tool_log\helper\buffered_writer;

    /** @var string $logguests true if logging guest access */
    protected $logguests;

    public function __construct(\tool_log\log\manager $manager) {
        $this->helper_setup($manager);
        // Log everything before setting is saved for the first time.
        $this->logguests = $this->get_config('logguests', 1);
        // JSON writing defaults to false (table format compatibility with older versions).
        // Note: This variable is defined in the buffered_writer trait.
        $this->jsonformat = (bool)$this->get_config('jsonformat', false);
    }

    /**
     * Should the event be ignored (== not logged)?
     * @param \core\event\base $event
     * @return bool
     */
    protected function is_event_ignored(\core\event\base $event) {
        if ((!CLI_SCRIPT or PHPUNIT_TEST) and !$this->logguests) {
            // Always log inside CLI scripts because we do not login there.
            if (!isloggedin() or isguestuser()) {
                return true;
            }
        }
        return false;
    }

    protected function insert_event_entries($evententries)
    {
        global $CFG;
        $file = $CFG->logstore_file_write_error_log_path;

        if (!check_dir_exists(dirname($file), true, true)) {
            throw new moodle_exception('error_creating_temp_dir', 'error', dirname($file));
        }

        $options = array('depth' => 1);

        try {
            $lo = new file_logger(backup::LOG_ERROR, true, true, $file);
            $message1 = json_encode($evententries);
            $lo->process($message1, backup::LOG_ERROR, $options);
            $lo->close();
        } catch (\base_logger_exception $e) {
        }

    }

}
