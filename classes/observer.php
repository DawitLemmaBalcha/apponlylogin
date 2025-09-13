<?php
namespace local_apponlylogin;

defined('MOODLE_INTERNAL') || die();

use core\event\user_loggedin;

class observer {

    public static function user_loggedin(user_loggedin $event): void {
        global $DB, $SESSION;

        // Immediately skip if it's an admin or a mobile app login.
        if (is_siteadmin($event->userid) || isset($SESSION->wstoken)) {
            return;
        }

        // --- Define the roles you want to block from web login ---
        $roles_to_block = [
            'student',
        ];

        // Get all role shortnames for the user from the database.
        $sql = "SELECT r.shortname
                  FROM {role_assignments} ra
                  JOIN {role} r ON ra.roleid = r.id
                 WHERE ra.userid = :userid";

        $user_role_shortnames = $DB->get_fieldset_sql($sql, ['userid' => $event->userid]);

        // Check if any of the user's roles are in our block list.
        foreach ($roles_to_block as $role_to_block) {
            if (in_array($role_to_block, $user_role_shortnames)) {
                // Display our friendly error message.
                \core\notification::error(get_string('student_login_blocked', 'local_apponlylogin'));

                // Log the user out.
                require_logout();

                // IMPORTANT: Stop the script immediately to prevent other errors.
                exit;
            }
        }
    }
}
