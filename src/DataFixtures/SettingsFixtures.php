<?php

namespace App\DataFixtures;

use App\Entity\Setting;
use App\Settings\TimetableSettings;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SettingsFixtures extends Fixture {

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) {
        $startTimes = [
            '07:45',
            '08:30',
            '09:30',
            '10:15',
            '11:20',
            '12:10',
            '13:25',
            '14:15'
        ];
        $endTimes = [
            '08:30',
            '09:15',
            '10:15',
            '11:00',
            '12:05',
            '12:55',
            '14:10',
            '15:00'
        ];

        $collapsible = [ 2, 4, 6, 8];

        $i = 1;
        foreach($startTimes as $startTime) {
            $key = sprintf('timetable.' . TimetableSettings::StartKey, $i);
            $manager->persist((new Setting())->setKey($key)->setValue($startTime));
            $i++;
        }

        $i = 1;
        foreach($endTimes as $endTime) {
            $key = sprintf('timetable.' . TimetableSettings::EndKey, $i);
            $manager->persist((new Setting())->setKey($key)->setValue($endTime));
            $i++;
        }

        foreach($collapsible as $lesson) {
            $key = sprintf('timetable.' . TimetableSettings::CollapsibleKey, $lesson);
            $manager->persist((new Setting())->setKey($key)->setValue(true));
        }

        $manager->persist((new Setting())->setKey('timetable.' . TimetableSettings::MaxLessonsKey)->setValue(8));

        $manager->flush();
    }
}