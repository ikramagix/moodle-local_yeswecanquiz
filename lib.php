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
  * Core logic for the YesWeCanQuiz plugin.
  * 
  * @package   yeswecanquiz
  * @author    Ikrame Saadi (@ikramagix)
  * @copyright 2025 Ikrame Saadi (@ikramagix) {@link https://yeswecanquiz.eu}
  * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
  * @contact   hello@yeswecanquiz.eu
  *
  * The following logic is used to control the session and force new quiz attempts for the public user:
  * - On any /mod/quiz/ page, if the user is not logged in (or is a guest), log them in as the public user.
  * - On the quiz view page (view.php), if the user is the public user,
  *   update any unfinished attempt for that quiz to state "finished"
  *   (thus disabling the option to resume) and then redirect to the attempt page.
  * - Outside /mod/quiz/ pages, if the public user is logged in, log them out.
  */


defined('MOODLE_INTERNAL') || die();

function yeswecanquiz_session_control() {
    global $USER, $DB, $SCRIPT;

    // Get the public user ID from plugin settings.
    $publicuserid = get_config('local_yeswecanquiz', 'publicuserid');
    if (!$publicuserid) {
        return;
    }

    // Avoid loops by using a session flag array.
    if (!isset($_SESSION['yeswecanquiz_newattempt'])) {
        $_SESSION['yeswecanquiz_newattempt'] = array();
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
                if (empty($_SESSION['yeswecanquiz_newattempt'][$cmid])) {
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
                    $_SESSION['yeswecanquiz_newattempt'][$cmid] = true;
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
function local_yeswecanquiz_before_http_headers() {
    yeswecanquiz_session_control();
}

/**
 * Fallback hook – triggered when Moodle builds the navigation.
 */
function yeswecanquiz_extend_navigation(global_navigation $navigation) {
    yeswecanquiz_session_control();
}
