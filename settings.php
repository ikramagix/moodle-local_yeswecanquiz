<?php
// YesYouCanQuiz Admin Settings

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('yesyoucanquiz', get_string('pluginname', 'yesyoucanquiz'));

    // User selection dropdown for Public User ID (only manual accounts)
    $useroptions = [];
    $users = $DB->get_records_sql(
        "SELECT u.id, u.firstname, u.lastname FROM {user} u 
         JOIN {auth} a ON u.auth = 'manual' 
         WHERE u.deleted = 0 
         ORDER BY u.lastname, u.firstname"
    );
    
    foreach ($users as $user) {
        $useroptions[$user->id] = fullname($user);
    }

    $settings->add(new admin_setting_configselect(
        'yesyoucanquiz/publicuserid',
        get_string('publicuserid', 'yesyoucanquiz'),
        get_string('publicuserid_desc', 'yesyoucanquiz'),
        0, // Default: No user selected
        $useroptions
    ));

    $ADMIN->add('localplugins', $settings);
}
