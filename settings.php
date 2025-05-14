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

defined('MOODLE_INTERNAL') || die();

/**
 * Plugin settings definition for the YesWeCanQuiz plugin.
 *
 * Adds a page under "Local plugins" where the admin enters
 * the user ID of the public quiz account.
 *
 * @package    local_yeswecanquiz
 * @copyright  2025 Ikrame Saadi (@ikramagix) <hello@yeswecanquiz.eu>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

/**
 * A text setting that only accepts a valid manual-account user ID.
 *
 * Validates that the entered ID exists and belongs to a manual-account user.
 *
 * @package   local_yeswecanquiz
 */
class local_yeswecanquiz_admin_setting_publicuserid extends admin_setting_configtext {

    /**
     * Validate that the given value is an existing user ID with auth = 'manual'.
     *
     * @param string $data The submitted setting value.
     * @return true|string True if valid, or an error message string.
     */
    public function validate($data) {
        $error = parent::validate($data);
        if ($error !== true) {
            return $error;
        }
        $userid = (int)$data;
        global $DB;
        if (!$DB->record_exists('user', ['id' => $userid])) {
            return get_string('publicuserid_invalid', 'local_yeswecanquiz');
        }
        $user = $DB->get_record('user', ['id' => $userid], 'auth', MUST_EXIST);
        if ($user->auth !== 'manual') {
            return get_string('publicuserid_notmanual', 'local_yeswecanquiz');
        }
        return true;
    }
}

if ($hassiteconfig) {
    /** 
     * Define the YesWeCanQuiz admin settings page.
     *
     * - Creates an admin_settingpage under "Local plugins"
     * - Adds a text input for the public quiz user ID
     *
     * @global \moodle_database $DB    Moodle DB API
     * @global \admin_root      $ADMIN Admin settings tree
     * @return void
     */
    $settings = new admin_settingpage(
        'local_yeswecanquiz',
        get_string('pluginname', 'local_yeswecanquiz')
    );

    // Public-quiz user: admin enters the manual-account user ID.
    $settings->add(new local_yeswecanquiz_admin_setting_publicuserid(
        'local_yeswecanquiz/publicuserid',
        get_string('publicuserid', 'local_yeswecanquiz'),
        get_string('publicuserid_desc', 'local_yeswecanquiz'),
        '' // default = empty
    ));

    $ADMIN->add('localplugins', $settings);
}
