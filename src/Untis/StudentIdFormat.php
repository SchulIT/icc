<?php

namespace App\Untis;

enum StudentIdFormat: string {
    case LastnameFirstname = 'lastname_firstname';
    case FirstnameLastname = 'firstname_lastname';
    case LastnameFirstnameBirthday = 'lastname_firstname_birthday';
}