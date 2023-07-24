<?php

namespace App\Homework;


class RegistrationSpamFilter
{
    public function filter(string $email): bool
    {
        if ( str_ends_with($email, 'ru') || str_ends_with($email, 'com') || str_ends_with($email, 'org') ) {
            return false;
        } else {
            return true;
        }
    }
}