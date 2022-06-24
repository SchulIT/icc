<?php

namespace App\Request;

use JMS\Serializer\ContextFactory\DeserializationContextFactoryInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\Exception as SerializerException;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class JsonParamConverter implements ParamConverterInterface {

    private const ContentType = 'json';

    private array $prefixes;

    private SerializerInterface $serializer;
    private DeserializationContextFactoryInterface $contextFactory;
    private ValidatorInterface $validator;

    private array $defaultOptions = [
        'validate' => true,
        'version' => null,
        'groups' => null
    ];

    public function __construct(array $prefixes, SerializerInterface $serializer, ValidatorInterface $validator, DeserializationContextFactoryInterface $contextFactory) {
        $this->prefixes = $prefixes;

        $this->serializer = $serializer;
        $this->contextFactory = $contextFactory;
        $this->validator = $validator;
    }

    /**
     * @param Request $request
     * @param ParamConverter $configuration
     * @return bool
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
        } catch (SerializerException $e) {
            throw new BadRequestHttpException('Request body does not contain valid JSON.');
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function supports(ParamConverter $configuration): bool {
        $class = $configuration->getClass();

        foreach($this->prefixes as $prefix) {
            if (substr($class, 0, strlen($prefix)) === $prefix) {
                return true;
            }
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