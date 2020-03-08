<?php

namespace App\ArgumentResolver;

use App\Request\BadRequestException;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class PostBodyRequestDataValueResolver implements ArgumentValueResolverInterface {

    private const Namespace = "App\Request\Data";

    private $serializer;

    public function __construct(SerializerInterface $serializer) {
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request, ArgumentMetadata $argument) {
        $type = $argument->getType();

        return substr($type, 0, strlen(static::Namespace)) == static::Namespace;
    }

    /**
     * @inheritDoc
     * @throws BadRequestException
     */
    public function resolve(Request $request, ArgumentMetadata $argument) {
        $json = $request->getContent();
        $type = $argument->getType();

        try {
            $object = $this->serializer->deserialize($json, $type, 'json');
            yield $object;
        } catch (\Exception $e) {
            throw new BadRequestException('The request data was not correct.');
        }
    }
}