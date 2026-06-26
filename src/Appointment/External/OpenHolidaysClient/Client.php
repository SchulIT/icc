<?php

namespace App\Appointment\External\OpenHolidaysClient;

use App\Appointment\External\OpenHolidaysClient\Model\CountryResponse;
use App\Appointment\External\OpenHolidaysClient\Model\HolidayResponse;
use App\Appointment\External\OpenHolidaysClient\Model\ProblemDetails;
use App\Appointment\External\OpenHolidaysClient\Model\SubdivisionResponse;
use DateTime;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

readonly class Client {

    public function __construct(
        private ClientInterface $openholidaysClient,
        private SerializerInterface $serializer,
    ) { }

    /**
     * @throws GuzzleException
     */
    private function get(string $url, array $query) {
        return $this
            ->openholidaysClient
            ->request(
                'GET',
                $url,
                [
                    'query' => $query,
                ]
            );
    }

    /**
     * @template T
     *
     * @param string $json
     * @param class-string<T> $fqcn
     * @return T[]
     * @throws ExceptionInterface
     */
    private function fromJsonArray(string $json, string $fqcn): array {
        return $this->serializer->deserialize($json, $fqcn . '[]', 'json');
    }

    /**
     * @template T
     *
     * @param string $json
     * @param class-string<T> $fqcn
     * @return T
     * @throws ExceptionInterface
     */
    private function fromJson(string $json, string $fqcn) {
        return $this->serializer->deserialize($json, $fqcn, 'json');
    }

    /**
     * @param string $languageIsoCode
     * @return CountryResponse[]|ProblemDetails
     * @throws GuzzleException|ExceptionInterface
     */
    public function countries(string $languageIsoCode): array|ProblemDetails {
        $response = $this->get('/Countries', [
            'languageIsoCode' => $languageIsoCode,
        ]);

        if($response->getStatusCode() !== 200) {
            return $this->fromJson($response->getBody()->getContents(), ProblemDetails::class);
        }

        return $this->fromJsonArray($response->getBody()->getContents(), CountryResponse::class);
    }

    /**
     * @param string $countryIsoCode
     * @param string $languageIsoCode
     * @return SubdivisionResponse[]|ProblemDetails
     * @throws GuzzleException|ExceptionInterface
     */
    public function subdivisions(string $countryIsoCode, string $languageIsoCode): array|ProblemDetails {
        $response = $this->get( '/Subdivisions', [
            'countryIsoCode' => $countryIsoCode,
            'languageIsoCode' => $languageIsoCode,
        ]);

        if($response->getStatusCode() !== 200) {
            return $this->fromJson($response->getBody()->getContents(), ProblemDetails::class);
        }

        return $this->fromJsonArray($response->getBody()->getContents(), SubdivisionResponse::class);
    }

    /**
     * @param string $countryIsoCode
     * @param DateTime $validFrom
     * @param DateTime $validTo
     * @param string $languageIsoCode
     * @param string|null $subdivisionCode
     * @return HolidayResponse[]|ProblemDetails
     * @throws GuzzleException|ExceptionInterface
     */
    public function publicHolidays(string $countryIsoCode, DateTime $validFrom, DateTime $validTo, string $languageIsoCode, string|null $subdivisionCode = null): array|ProblemDetails {
        $response = $this->get( '/PublicHolidays', [
            'countryIsoCode' => $countryIsoCode,
            'languageIsoCode' => $languageIsoCode,
            'validFrom' => $validFrom->format('Y-m-d'),
            'validTo' => $validTo->format('Y-m-d'),
            'subdivisionCode' => $subdivisionCode
        ]);

        if($response->getStatusCode() !== 200) {
            return $this->fromJson($response->getBody()->getContents(), ProblemDetails::class);
        }

        return $this->fromJsonArray($response->getBody()->getContents(), HolidayResponse::class);
    }

    /**
     * @param string $countryIsoCode
     * @param DateTime $validFrom
     * @param DateTime $validTo
     * @param string $languageIsoCode
     * @param string|null $subdivisionCode
     * @return HolidayResponse[]|ProblemDetails
     * @throws GuzzleException|ExceptionInterface
     */
    public function schoolHolidays(string $countryIsoCode, DateTime $validFrom, DateTime $validTo, string $languageIsoCode, string|null $subdivisionCode = null): array|ProblemDetails {
        $response = $this->get( '/SchoolHolidays', [
            'countryIsoCode' => $countryIsoCode,
            'languageIsoCode' => $languageIsoCode,
            'validFrom' => $validFrom->format('Y-m-d'),
            'validTo' => $validTo->format('Y-m-d'),
            'subdivisionCode' => $subdivisionCode
        ]);

        if($response->getStatusCode() !== 200) {
            return $this->fromJson($response->getBody()->getContents(), ProblemDetails::class);
        }

        return $this->fromJsonArray($response->getBody()->getContents(), HolidayResponse::class);
    }
}
