<?php

namespace App\Security\User;

use App\Entity\User;
use App\Entity\UserType;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use LightSaml\ClaimTypes;
use LightSaml\Model\Protocol\Response;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SchoolIT\CommonBundle\Saml\ClaimTypes as SamlClaimTypes;

class UserMapper {
    const ROLES_ASSERTION_NAME = 'urn:roles';

    private $typesMap;
    private $teacherRepository;
    private $studentRepository;
    private $logger;

    public function __construct(array $typesMap, TeacherRepositoryInterface $teacherRepository, StudentRepositoryInterface $studentRepository, LoggerInterface $logger = null) {
        $this->typesMap = $typesMap;
        $this->teacherRepository = $teacherRepository;
        $this->studentRepository = $studentRepository;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param string $type
     * @return UserType
     */
    private function getUserType(string $type): UserType {
        if(!array_key_exists($type, $this->typesMap) || !in_array($type, UserType::values())) {
            $this->logger
                ->notice(sprintf('User type "%s" is not a valid UserType. Setting type "user"', $type));
            return UserType::User();
        }

        return new UserType($type);
    }

    /**
     * @param Response $response
     * @param string[] $valueAttributes
     * @param string[] $valuesAttributes
     * @return array
     */
    private function transformResponseToArray(Response $response, array $valueAttributes, array $valuesAttributes) {
        $result = [ ];

        foreach($valueAttributes as $valueAttribute) {
            $result[$valueAttribute] = $this->getValue($response, $valueAttribute);
        }

        foreach($valuesAttributes as $valuesAttribute) {
            $result[$valuesAttribute] = $this->getValues($response, $valuesAttribute);
        }

        return $result;
    }

    /**
     * @param User $user
     * @param Response|array[] Either a SAMLResponse or an array (keys: SAML Attribute names, values: corresponding values)
     * @return User
     */
    public function mapUser(User $user, $data) {
        if(is_array($data)) {
            return $this->mapUserFromArray($user, $data);
        } else if($data instanceof Response) {
            return $this->mapUserFromResponse($user, $data);
        }
    }

    private function mapUserFromResponse(User $user, Response $response) {
        return $this->mapUserFromArray($user, $this->transformResponseToArray(
            $response,
            [
                ClaimTypes::GIVEN_NAME,
                ClaimTypes::SURNAME,
                ClaimTypes::EMAIL_ADDRESS,
                SamlClaimTypes::INTERNAL_ID,
                SamlClaimTypes::TYPE
            ],
            [
                static::ROLES_ASSERTION_NAME
            ]
        ));
    }

    /**
     * @param User $user User to populate data to
     * @param array<string, mixed> $data
     * @return User
     */
    private function mapUserFromArray(User $user, array $data) {
        $firstname = $data[ClaimTypes::GIVEN_NAME];
        $lastname = $data[ClaimTypes::SURNAME];
        $email = $data[ClaimTypes::EMAIL_ADDRESS];
        $roles = $data[static::ROLES_ASSERTION_NAME];
        $type = $this->getUserType($data[SamlClaimTypes::TYPE]);

        if(!is_array($roles)) {
            $roles = [ $roles ];
        }

        if(count($roles) === 0) {
            $roles = [ 'ROLE_USER' ];
        }

        $user
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setEmail($email)
            ->setUserType($type)
            ->setRoles($roles);

        if(UserType::Teacher()->equals($type)) {
            $acronym = $data[SamlClaimTypes::INTERNAL_ID];
            $teacher = $this->teacherRepository->findOneByAcronym($acronym);

            if($teacher !== null) {
                $user->setTeacher($teacher);
            } else {
                $this->logger
                    ->notice(sprintf('Cannot map teacher with acronym "%s" as such teacher does not exist.', $acronym));
            }
        } else if(UserType::Student()->equals($type) || UserType::Parent()->equals($type)) {
            $studentId = $data[SamlClaimTypes::INTERNAL_ID];
            $student = $this->studentRepository->findOneByExternalId($studentId);

            if($student !== null) {
                $user->setStudent($student);
            } else {
                $this->logger
                    ->notice(sprintf('Cannot map student with student ID "%s" as such student does not exist.', $studentId));
            }
        }

        return $user;
    }

    private function getValue(Response $response, $attributeName) {
        return $response->getFirstAssertion()->getFirstAttributeStatement()
            ->getFirstAttributeByName($attributeName)->getFirstAttributeValue();
    }

    private function getValues(Response $response, $attributeName) {
        return $response->getFirstAssertion()->getFirstAttributeStatement()
            ->getFirstAttributeByName($attributeName)->getAllAttributeValues();
    }
}