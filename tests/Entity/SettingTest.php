<?php

namespace App\Tests\Entity;

use App\Entity\Setting;
use PHPUnit\Framework\TestCase;

class SettingTest extends TestCase {
    public function testGettersSetters() {
        $setting = new Setting();

        $setting->setKey('key');
        $this->assertEquals('key', $setting->getKey());

        $setting->setValue(null);
        $this->assertNull($setting->getValue());

        $setting->setValue('');
        $this->assertEquals('', $setting->getValue());

        $setting->setValue(['foo']);
        $this->assertEquals(['foo'], $setting->getValue());
    }
}