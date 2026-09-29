<?php

declare(strict_types=1);

namespace Invis1ble\SerializerExtensionBundle\Tests\Fixtures;

use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class UriController
{
    public function __construct(
        public readonly UriFactoryInterface $uriFactory,
        public readonly SerializerInterface $serializer,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $uri = $this->serializer->deserialize($request->getContent(), UriInterface::class, 'json');

        return JsonResponse::fromJsonString($this->serializer->serialize($uri, 'json'));
    }
}
