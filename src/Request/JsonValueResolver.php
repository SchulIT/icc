<?php

namespace App\Request;

use Exception;
use JMS\Serializer\ContextFactory\DeserializationContextFactoryInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\Exception as SerializerException;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsTargetedValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsTargetedValueResolver('json')]
class JsonValueResolver implements ValueResolverInterface {

    private const string JSON_CONTENT_TYPE = 'json';

    public function __construct(private readonly SerializerInterface $serializer, private readonly ValidatorInterface $validator, private readonly DeserializationContextFactoryInterface $contextFactory) {

    }


    /**
     * @throws ValidationFailedException
     * @throws BadRequestHttpException
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable {
        $contentType = $request->getContentTypeFormat();

        if($contentType !== self::JSON_CONTENT_TYPE) {
            throw new BadRequestHttpException(sprintf('Request header "Content-Type" must be "application/json", "%s" provided.', $contentType));
        }

        $type = $argument->getType();
        $attribute = $argument->getAttributesOfType(JsonPayload::class)[0] ?? null;

        if($attribute === null) {
            return [ ];
        }

        try {
            $context = $this->getDeserializationContext($attribute);
            $object = $this->serializer->deserialize($request->getContent(),  $type,'json', $context);

            if($attribute->validate) {
                $violations = $this->validator->validate($object);

                if($violations->count() > 0) {
                    throw new ValidationFailedException($violations);
                }
            }

            return [ $object ];
        } catch (SerializerException) {
            throw new BadRequestHttpException('Request body does not contain valid JSON.');
        }
    }

    private function getDeserializationContext(JsonPayload $attribute): DeserializationContext {
        $context = $this->contextFactory->createDeserializationContext();

        if(!empty($attribute->groups)) {
            $context->setGroups($attribute->groups);
        }

        if(!empty($attribute->version)) {
            $context->setVersion($attribute->version);
        }

        return $context;
    }
}