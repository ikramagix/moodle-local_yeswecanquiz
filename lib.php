<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Controls the session and forces new quiz attempts for the public user.
 *
 * - On any /mod/quiz/ page, if the user is not logged in (or is a guest), log them in as the public user.
 * - On the quiz view page (view.php), if the user is the public user,
 *   update any unfinished attempt for that quiz to state "finished"
 *   (thus disabling the option to resume) and then redirect to the attempt page.
 * - Outside /mod/quiz/ pages, if the public user is logged in, log them out.
 */
function yesyoucanquiz_session_control() {
    global $USER, $DB, $SCRIPT;

    // Get the public user ID from plugin settings.
    $publicuserid = get_config('local_yesyoucanquiz', 'publicuserid');
    if (!$publicuserid) {
        return;
    }

    // Avoid loops by using a session flag array.
    if (!isset($_SESSION['yesyoucanquiz_newattempt'])) {
        $_SESSION['yesyoucanquiz_newattempt'] = array();
    }

    // CASE 1: On quiz view page.
    if (strpos($SCRIPT, '/mod/quiz/view.php') !== false) {

        // Auto-login as public user if necessary.
        if (!isloggedin() || isguestuser()) {
            $publicuser = $DB->get_record('user', array('id' => $publicuserid, 'deleted' => 0, 'suspended' => 0));
            if ($publicuser) {
                complete_user_login($publicuser);
                session_write_close();
                redirect(new moodle_url($SCRIPT, $_GET));
            }
        }
        // Now, if we are the public user…
        if (isloggedin() && !isguestuser() && $USER->id == $publicuserid) {
            if (isset($_GET['id'])) {
                $cmid = required_param('id', PARAM_INT);
                // Only process once per quiz view to avoid looping.
                if (empty($_SESSION['yesyoucanquiz_newattempt'][$cmid])) {
                    // Get course module and quiz records.
                    $cm = get_coursemodule_from_id('quiz', $cmid, 0, false, MUST_EXIST);
                    $quiz = $DB->get_record('quiz', array('id' => $cm->instance), '*', MUST_EXIST);
                    // Update any unfinished attempts (state not "finished") to "finished".
                    if ($attempts = $DB->get_records_select('quiz_attempts',
                            "quiz = ? AND userid = ? AND state <> ?",
                            array($quiz->id, $publicuserid, 'finished'))) {
                        foreach ($attempts as $attempt) {
                            $DB->set_field('quiz_attempts', 'state', 'finished', array('id' => $attempt->id));
                        }
                    }
                    // Mark that we have processed this quiz.
                    $_SESSION['yesyoucanquiz_newattempt'][$cmid] = true;
                    // Redirect to the attempt page so Moodle creates a new attempt.
                    $attempturl = new moodle_url('/mod/quiz/attempt.php', array('attempt' => 0, 'id' => $cmid));
                    redirect($attempturl);
                }
            }
        }
    }
    // CASE 2: On other /mod/quiz/ pages (for example attempt.php).
    else if (strpos($SCRIPT, '/mod/quiz/') !== false) {
        if (!isloggedin() || isguestuser()) {
            $publicuser = $DB->get_record('user', array('id' => $publicuserid, 'deleted' => 0, 'suspended' => 0));
            if ($publicuser) {
                complete_user_login($publicuser);
                session_write_close();
                redirect(new moodle_url($SCRIPT, $_GET));
            }
        }
    }
    // CASE 3: Outside /mod/quiz/ pages.
    else {
        if (isloggedin() && !isguestuser() && $USER->id == $publicuserid) {
            require_logout();
            redirect(new moodle_url($SCRIPT, $_GET));
        }
    }
}

/**
 * Hook callback: triggers session control early.
 */
function local_yesyoucanquiz_before_http_headers() {
    yesyoucanquiz_session_control();
}

/**
 * Fallback hook – triggered when Moodle builds the navigation.
 */
function yesyoucanquiz_extend_navigation(global_navigation $navigation) {
    yesyoucanquiz_session_control();
}
