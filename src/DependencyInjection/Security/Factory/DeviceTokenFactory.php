<?php

namespace App\DependencyInjection\Security\Factory;

use App\Security\Authentication\Provider\DeviceTokenProvider;
use App\Security\Firewall\DeviceTokenListener;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DeviceTokenFactory implements SecurityFactoryInterface {

    /**
     * @inheritDoc
     */
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint) {
        $providerId = 'security.authentication.provider.device.' . $id;
        $container
            ->setDefinition($providerId, new ChildDefinition(DeviceTokenProvider::class));

        $listenerId = 'security.authentication.listener.device.' . $id;
        $container->setDefinition($listenerId, new ChildDefinition(DeviceTokenListener::class));

        return [ $providerId, $listenerId, $defaultEntryPoint ];
    }

    /**
     * @inheritDoc
     */
    public function getPosition() {
        return 'pre_auth';
    }

    /**
     * @inheritDoc
     */
    public function getKey() {
        return 'device';
    }

    public function addConfiguration(NodeDefinition $builder) {

    }
}