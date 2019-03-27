<?php

namespace App\Request;

use App\Response\ErrorResponse;
use JMS\Serializer\ContextFactory\DeserializationContextFactoryInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\Exception as SerializerException;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class JsonParamConverter implements ParamConverterInterface {

    private const ContentType = 'application/json';

    private $prefix;

    private $serializer;
    private $contextFactory;
    private $validator;

    private $defaultOptions = [
        'validate' => true,
        'version' => null,
        'groups' => null
    ];

    public function __construct(string $prefix, SerializerInterface $serializer, ValidatorInterface $validator, DeserializationContextFactoryInterface $contextFactory) {
        $this->prefix = $prefix;

        $this->serializer = $serializer;
        $this->contextFactory = $contextFactory;
        $this->validator = $validator;
    }

    /**
     * @param Request $request
     * @param ParamConverter $configuration
     * @return bool
     * @throws BadRequestException
     */
    public function apply(Request $request, ParamConverter $configuration) {
        $contentType = $request->getContentType();

        if($contentType !== static::ContentType) {
            throw new BadRequestException(sprintf('Request header "Content-Type" must be "application/json", "%s" provided.', $contentType));
        }

        $name = $configuration->getName();
        $class = $configuration->getClass();
        $json = $request->getContent();

        $options = $this->getOptions($configuration);

        try {
            $context = $this->getDeserializationContext($configuration);
            $object = $this->serializer->deserialize($json, $class, 'json', $context);

            if($options['validate'] === true) {
                $validations = $this->validator->validate($object);

                if($validations->count() > 0) {
                    throw new BadRequestException($this->createErrorResponseData($validations));
                }
            }

            $request->attributes->set($name, $object);
        } catch (SerializerException $e) {
            throw new BadRequestException('Request body does not contain valid JSON.');
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function supports(ParamConverter $configuration) {
        $class = $configuration->getClass();

        if(substr($class, 0, strlen($this->prefix)) === $this->prefix) {
            return true;
        }

        return false;
    }

    private function getDeserializationContext(ParamConverter $configuration): DeserializationContext {
        $options = $this->getOptions($configuration);

        $context = $this->contextFactory->createDeserializationContext();

        if(is_array($options['groups']) || is_string($options['groups'])) {
            $context->setGroups($options['groups']);
        }

        if($options['version'] !== null) {
            $context->setVersion($options['version']);
        }

        return $context;
    }

    private function getOptions(ParamConverter $configuration): array {
        return array_replace($this->defaultOptions, $configuration->getOptions());
    }

    /**
     * @param ConstraintViolationListInterface $violationList
     * @return ErrorResponse
     */
    private function createErrorResponseData(ConstraintViolationListInterface $violationList): ErrorResponse {
        $violations = [ ];

        foreach($violationList as $violation) {
            $violations[] = [
                'property' => $violation->getPropertyPath(),
                'message' => $violation->getMessage()
            ];
        }

        $response = new ErrorResponse('Error validating request data.');
        $response->setData($violations);

        return $response;
    }
}