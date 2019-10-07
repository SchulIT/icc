<?php

namespace App\Entity;

use MyCLabs\Enum\Enum;

/**
 * @method static MessageScope Dashboard()
 * @method static MessageScope Substitutions()
 * @method static MessageScope Exams()
 * @method static MessageScope Appointments()
 * @method static MessageScope Login()
 * @method static MessageScope Timetable()
 * @method static MessageScope Messages()
 * @method static MessageScope Lists()
 */
class MessageScope extends Enum {
    private const Dashboard = 'dashboard';
    private const Substitutions = 'substitutions';
    private const Exams = 'exams';
    private const Appointments = 'appointments';
    private const Login = 'login';
    private const Timetable = 'timetable';
    private const Messages = 'messages';
    private const Lists = 'lists';
}