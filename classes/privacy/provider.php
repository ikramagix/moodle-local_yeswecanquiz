<?php
namespace local_yeswecanquiz\privacy;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy "null" provider for local_yeswecanquiz.
 *
 * This plugin does not store any personal user data.
 */
class provider implements
    \core_privacy\local\metadata\null_provider {

    /**
     * Returns the language string identifier that explains
     * why this plugin stores no data.
     *
     * @return  string
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }
}
