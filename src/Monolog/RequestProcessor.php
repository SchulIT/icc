<?php

namespace App\Monolog;

use Monolog\Processor\ProcessorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestProcessor implements ProcessorInterface {

    private ?string $userAgent = null;
    private ?string $url = null;

    public function __construct(private RequestStack $requestStack)
    {
    }

    /**
     * @inheritDoc
     */
    public function __invoke(array $records): array {
        $records['extra']['useragent'] = $this->getUserAgent();
        $records['extra']['url'] = $this->getUrl();

        return $records;
    }

    private function getUserAgent(): ?string {
        if($this->userAgent === null && $this->requestStack->getMainRequest() !== null) {
            $this->userAgent = $this->requestStack->getMainRequest()->headers->get('User-Agent');
        }

        return $this->userAgent;
    }

    private function getUrl(): ?string {
        if($this->url === null && $this->requestStack->getMainRequest() !== null) {
            $this->url = $this->requestStack->getMainRequest()->getRequestUri();
        }

        return $this->url;
    }
}