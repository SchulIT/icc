<?php

namespace App\Request;

use JMS\Serializer\ContextFactory\DeserializationContextFactoryInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\Exception as SerializerException;
use JMS\Serializer\SerializerInterface;
use ReflectionClass;
use ReflectionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class JsonParamConverter implements ParamConverterInterface {

    private const ContentType = 'json';

    private array $defaultOptions = [
        'validate' => true,
        'version' => null,
        'groups' => null
    ];

    public function __construct(private array $prefixes, private SerializerInterface $serializer, private ValidatorInterface $validator, private DeserializationContextFactoryInterface $contextFactory)
    {
    }

    /**
     * @throws BadRequestHttpException
     * @throws ValidationFailedException
     */
    public function apply(Request $request, ParamConverter $configuration): bool {
        $contentType = $request->getContentType();

        if($contentType !== self::ContentType) {
            throw new BadRequestHttpException(sprintf('Request header "Content-Type" must be "application/json", "%s" provided.', $contentType));
        }

        $name = $configuration->getName();
        $class = $configuration->getClass();
        $json = $request->getContent();

        $options = $this->getOptions($configuration);

        try {
            $context = $this->getDeserializationContext($configuration);
            $object = $this->serializer->deserialize($json, $class, 'json', $context);

            if($options['validate'] === true) {
                $violations = $this->validator->validate($object);

                if($violations->count() > 0) {
                    throw new ValidationFailedException($violations);
                }
            }

            $request->attributes->set($name, $object);
        } catch (SerializerException) {
            throw new BadRequestHttpException('Request body does not contain valid JSON.');
        }

        return true;
    }

    /**
     * @inheritDoc
     * @throws ReflectionException
     */
    public function supports(ParamConverter $configuration): bool {
        $class = $configuration->getClass();

        foreach($this->prefixes as $prefix) {
            if (str_starts_with($class, $prefix)) {
                return true;
            }
        }

        if(empty($class)) {
            return false;
        }

        // Test for attribute
        $reflectionClass = new ReflectionClass($class);
        $attributes = $reflectionClass->getAttributes(JsonParam::class);

        if(count($attributes) > 0) {
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
}