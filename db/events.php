<?php

defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname'   => '\\core\\event\\user_loggedin', // Corrected line
        'callback'    => '\\local_apponlylogin\\observer::user_loggedin',
    ],
];
