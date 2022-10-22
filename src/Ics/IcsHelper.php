<?php

namespace App\Ics;

use DateTimeZone;
use Jsvrcek\ICS\CalendarExport;
use Jsvrcek\ICS\CalendarStream;
use Jsvrcek\ICS\Model\Calendar;
use Jsvrcek\ICS\Model\CalendarEvent;
use Jsvrcek\ICS\Utility\Formatter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Twig\Environment;

class IcsHelper {
    private static int $batchSize = 20;

    public function __construct(private string $appName, private string $languageCode, private string $appUrl)
    {
    }

    /**
     * @param CalendarEvent[] $events
     */
    public function getIcsStream(string $name, string $description, array $events): string {
        $calendar = new Calendar();
        $calendar->setTimezone(new DateTimeZone(date_default_timezone_get()));
        $calendar->setProdId(sprintf('-//%s//%s//%s', parse_url($this->appUrl,PHP_URL_HOST), $this->appName, $this->languageCode));
        $calendar->setCustomHeaders([
            'X-WR-CALNAME' => $name,
            'X-WR-CALDESC' => $description
        ]);

        $calendar->setEventsProvider(fn($startIndex) => array_slice($events, $startIndex, self::$batchSize));

        // Fixes empty status field
        foreach($events as $event) {
            $event->setStatus('CONFIRMED');
        }

        $export = new CalendarExport(new CalendarStream(), new Formatter());
        $export->addCalendar($calendar);
        $export->setDateTimeFormat('utc');

        $export->getStreamObject()->setDoImmediateOutput(true);
        return $export->getStream();
    }

    /**
     * @param CalendarEvent[] $events
     * @param string|null $filename
     */
    public function getIcsResponse(string $name, string $description, array $events, $filename = null): Response {
        $response = new StreamedResponse(fn() => $this->getIcsStream($name, $description, $events));

        if($filename !== null) {
            $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
            $response->headers->set('Content-Disposition', $disposition);
        }

        $response->headers->set('Content-Type', 'text/calendar');

        return $response;
    }
}