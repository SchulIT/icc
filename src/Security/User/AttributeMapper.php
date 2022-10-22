<?php

namespace App\Security\User;

use LightSaml\ClaimTypes;
use LightSaml\Model\Protocol\Response;
use LightSaml\SpBundle\Security\User\AttributeMapperInterface;
use SchulIT\CommonBundle\Saml\ClaimTypes as SamlClaimTypes;
use SchulIT\CommonBundle\Security\User\AbstractUserMapper;

class AttributeMapper extends AbstractUserMapper implements AttributeMapperInterface {

    public function getAttributes(Response $response): array {
        $attributes = [ ];

        foreach($response->getFirstAssertion()->getFirstAttributeStatement()->getAllAttributes() as $attribute) {
            $values = $attribute->getAllAttributeValues();

            if(count($values) > 1) {
                $attributes[$attribute->getName()] = $values;
            } else if(count($values) === 1) {
                $attributes[$attribute->getName()] = $values[0];
            } else {
                $attributes[$attribute->getName()] = null;
            }
        }

        $attributes['name_id'] = $response->getFirstAssertion()->getSubject()->getNameID()->getValue();
        $attributes['services'] = $this->getServices($response);

        return $attributes;
    }

    private function getServices(Response $response): array {
        $values = $this->getValues($response, SamlClaimTypes::SERVICES);

        $services = [ ];

        foreach($values as $value) {
            $services[] = json_decode($value, null, 512, JSON_THROW_ON_ERROR);
        }

        return $services;
    }

    private function getValues(Response $response, $attributeName) {
        return $response->getFirstAssertion()->getFirstAttributeStatement()
            ->getFirstAttributeByName($attributeName)->getAllAttributeValues();
    }
}