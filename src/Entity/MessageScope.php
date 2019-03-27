<?php

namespace App\Entity;

use MyCLabs\Enum\Enum;

/**
 * @method static MessageScope Dashboard()
 * @method static MessageScope Substitutions()
 * @method static MessageScope Exams()
 * @method static MessageScope Appointments()
 * @method static MessageScope None()
 * @method static MessageScope Login()
 * @method static MessageScope Timetable()
 */
class MessageScope extends Enum {
    private const Dashboard = 'Dashboard';
    private const Substitutions = 'Substitutions';
    private const Exams = 'Exams';
    private const Appointments = 'Appointments';
    private const None = 'None';
    private const Login = 'Login';
    private const Timetable = 'Timetable';
}