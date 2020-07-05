<?php

namespace App\Security\User;

use LightSaml\SpBundle\Security\Authentication\Token\SamlSpResponseToken;
use LightSaml\SpBundle\Security\User\AttributeMapperInterface;
use SchulIT\CommonBundle\Saml\ClaimTypes;

class AttributeMapper implements AttributeMapperInterface {

    public function getAttributes(SamlSpResponseToken $token) {
        return [
            'name_id' => $token->getResponse()->getFirstAssertion()->getSubject()->getNameID()->getValue(),
            'internal_id' => $this->getValue($token, ClaimTypes::EXTERNAL_ID),
            'services' => $this->getServices($token)
        ];
    }

    private function getServices(SamlSpResponseToken $token) {
        $values = $this->getValues($token, ClaimTypes::SERVICES);

        $services = [ ];

        foreach($values as $value) {
            $services[] = json_decode($value);
        }

        return $services;
    }

    private function getValue(SamlSpResponseToken $token, $attributeName) {
        return $token->getResponse()->getFirstAssertion()->getFirstAttributeStatement()
            ->getFirstAttributeByName($attributeName)->getFirstAttributeValue();
    }

    private function getValues(SamlSpResponseToken $token, $attributeName) {
        return $token->getResponse()->getFirstAssertion()->getFirstAttributeStatement()
            ->getFirstAttributeByName($attributeName)->getAllAttributeValues();
    }
}