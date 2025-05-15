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
 * @package    local_yeswecanquiz
 * @copyright  2025 Ikrame Saadi (@ikramagix) <hello@yeswecanquiz.eu>
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

/**
 * Hook registrations for YesWeCanQuiz.
 * Only the before_http_headers hook is required to enforce session control.
 * Moodle <4.4 ignores this file; Moodle ≥4.4 uses the PSR-14 Hooks API.
 *
 * @package   local_yeswecanquiz
 */
return [
    \core\hook\output\before_http_headers::class => [
        ['callback' => 'local_yeswecanquiz_before_http_headers'],
    ],
];
