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
 * Admin settings file.
 *
 * @package   yeswecanquiz
 * @author    Ikrame Saadi (@ikramagix)
 * @copyright 2025 Ikrame Saadi (@ikramagix) {@link http://ikramagix.com}
 * @license   hhttps://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_yeswecanquiz', get_string('pluginname', 'local_yeswecanquiz'));

    // Build user selection dropdown for Public User (only manual accounts).
    $useroptions = [];
    $sql = "SELECT u.id, u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename
            FROM {user} u
            WHERE u.auth = 'manual' AND u.deleted = 0
            ORDER BY u.lastname, u.firstname";
    $users = $DB->get_records_sql($sql);
    foreach ($users as $user) {
        $useroptions[$user->id] = fullname($user);
    }

    $settings->add(new admin_setting_configselect(
        'local_yeswecanquiz/publicuserid',
        get_string('publicuserid', 'local_yeswecanquiz'),
        get_string('publicuserid_desc', 'local_yeswecanquiz'),
        0, // Default: No user selected.
        $useroptions
    ));

    $ADMIN->add('localplugins', $settings);
}
