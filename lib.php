<?php
// Core logic

defined('MOODLE_INTERNAL') || die();

function yesyoucanquiz_attempt_quiz() {
    global $USER, $DB;
    
    // Get public user ID from plugin settings
    $publicuserid = get_config('yesyoucanquiz', 'publicuserid');
    
    // Only proceed if a public user ID is set
    if ($publicuserid) {
        $publicuser = $DB->get_record('user', ['id' => $publicuserid, 'deleted' => 0, 'suspended' => 0]);
        
        // If the user is not logged in or is a guest, log them in as the public user
        if ($publicuser && (!isloggedin() || isguestuser())) {
            complete_user_login($publicuser);
        }
    }
}

// Hook into the quiz attempt process
function yesyoucanquiz_extend_navigation(global_navigation $navigation) {
    yesyoucanquiz_attempt_quiz();
}