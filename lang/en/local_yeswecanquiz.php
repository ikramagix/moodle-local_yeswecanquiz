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
 * English language file for YesWeCanQuiz plugin.
 *
 * @package    local_yeswecanquiz
 * @copyright  2025 Ikrame Saadi (@ikramagix) <hello@yeswecanquiz.eu>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

$string['pluginname']        = 'YesWeCanQuiz';
$string['settings']          = 'YesWeCanQuiz Settings';

$string['publicuserid']      = 'Public quiz user ID';
$string['publicuserid_desc'] = '
Enter the <strong>numeric user ID</strong> of a manual-account user to serve as the public quiz user. 
You can find this <strong>ID</strong> in the profile URL, for example:  
<code>https://your_moodle_site.com/user/profile.php?id=12345</code>.  
Using the exact ID ensures you select the correct user and avoids confusion with duplicate names or emails.
';
$string['publicuserid_invalid']   = 'No user found with that ID, please enter a valid numeric user ID.';
$string['publicuserid_notmanual'] = 'The selected user must be a manually created account (auth = manual) to avoid hijack.';

$string['privacy:metadata']  = 'The YesWeCanQuiz plugin does not store any personal user data.';