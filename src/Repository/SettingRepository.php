<?php

namespace App\Repository;

use App\Entity\Setting;

class SettingRepository extends AbstractRepository implements SettingRepositoryInterface {

    /**
     * @param string $key
     * @return Setting|null
     */
    public function findOneByKey(string $key): ?Setting {
        return $this->em->getRepository(Setting::class)
            ->findOneBy([
                'key' => $key
            ]);
    }

    /**
     * @return Setting[]
     */
    public function findAll() {
        return $this->em->getRepository(Setting::class)
            ->findAll();
    }

    /**
     * @param Setting $setting
     */
    public function persist(Setting $setting): void {
        $this->em->persist($setting);
        $this->em->flush();
    }

}