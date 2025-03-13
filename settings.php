<?php
// YesYouCanQuiz Admin Settings

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_yesyoucanquiz', get_string('pluginname', 'local_yesyoucanquiz'));

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
        'local_yesyoucanquiz/publicuserid',
        get_string('publicuserid', 'local_yesyoucanquiz'),
        get_string('publicuserid_desc', 'local_yesyoucanquiz'),
        0, // Default: No user selected.
        $useroptions
    ));

    $ADMIN->add('localplugins', $settings);
}
