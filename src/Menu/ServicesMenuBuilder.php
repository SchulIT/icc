<?php

namespace App\Menu;

use Knp\Menu\ItemInterface;
use LightSaml\SpBundle\Security\Http\Authenticator\SamlToken;

class ServicesMenuBuilder extends AbstractMenuBuilder {

    public function servicesMenu(): ItemInterface {
        $root = $this->factory->createItem('root')
            ->setChildrenAttributes([
                'class' => 'navbar-nav float-lg-right'
            ]);

        $token = $this->tokenStorage->getToken();

        if($token instanceof SamlToken) {
            $menu = $root->addChild('services', [
                'label' => ''
            ])
                ->setExtra('icon', 'fa fa-th')
                ->setExtra('menu', 'services')
                ->setExtra('pull-right', true)
                ->setAttribute('title', $this->translator->trans('services.label'));

            foreach($token->getAttribute('services') as $service) {
                $item = $menu->addChild($service->name, [
                    'uri' => $service->url
                ])
                    ->setAttribute('title', $service->description)
                    ->setLinkAttribute('target', '_blank');

                if(isset($service->icon) && !empty($service->icon)) {
                    $item->setExtra('icon', $service->icon);
                }
            }
        }

        return $root;
    }
}