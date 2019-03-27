<?php

namespace App\Ics;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Twig\Environment;

class IcsHelper {
    private $twig;
    private $baseUrl;

    public function __construct(string $baseUrl, Environment $twig) {
        $this->baseUrl = $baseUrl;
        $this->twig = $twig;
    }

    /**
     * @param string $name
     * @param string $description
     * @param array $items
     * @return string
     */
    public function getIcsContent(string $name, string $description, array $items): string {
        $dtstamp = new \DateTime();
        $dtstamp->setTimezone(new \DateTimeZone('UTC'));

        $ics = $this->twig->render('ics/ics.html.twig', [
            'host'          => $this->baseUrl,
            'name'          => $name,
            'description'   => $description,
            'dtstamp'       => $dtstamp,
            'timezone'      => date_default_timezone_get(),
            'items'         => $items
        ]);

        // Remove empty lines and ensure correct line endings (CRLF)
        $ics = str_replace("\r", '', $ics);
        $ics = explode("\n", $ics);
        $ics = array_filter($ics);
        $ics = implode("\r\n", $ics);

        return $ics;
    }

    /**
     * @param string $name
     * @param string $description
     * @param IcsItemInterface[] $items
     * @param string|null $filename
     * @return Response
     */
    public function getIcsResponse(string $name, string $description, array $items, $filename = null): Response {
        $content = $this->getIcsContent($name, $description, $items);

        $response = new Response($content);

        if($filename !== null) {
            $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        }

        $response->headers->set('Content-Type', 'text/calendar');

        return $response;
    }
}