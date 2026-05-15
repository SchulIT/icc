<?php

namespace App\Framework\Settings\Repository;

use App\Framework\Settings\Entity\Setting;

interface SettingRepositoryInterface {

    /**
     * @param string $key
     * @return Setting|null
     */
    public function findOneByKey(string $key): ?Setting;

    /**
     * @return Setting[]
     */
    public function findAll();

    /**
     * @param Setting $setting
     */
    public function persist(Setting $setting): void;
}