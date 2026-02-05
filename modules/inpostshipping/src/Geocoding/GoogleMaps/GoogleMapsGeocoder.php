<?php
/**
 * Copyright since 2021 InPost S.A.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the EUPL-1.2 or later.
 * You may not use this work except in compliance with the Licence.
 *
 * You may obtain a copy of the Licence at:
 * https://joinup.ec.europa.eu/software/page/eupl
 * It is also bundled with this package in the file LICENSE.txt
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the Licence is distributed on an AS IS basis,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the Licence for the specific language governing permissions
 * and limitations under the Licence.
 *
 * @author    InPost S.A.
 * @copyright Since 2021 InPost S.A.
 * @license   https://joinup.ec.europa.eu/software/page/eupl
 */

declare(strict_types=1);

namespace InPost\Shipping\Geocoding\GoogleMaps;

use InPost\Shipping\Geocoding\Address;
use InPost\Shipping\Geocoding\Coordinates;
use InPost\Shipping\Geocoding\Exception\GeocodingException;
use InPost\Shipping\Geocoding\Exception\HttpException;
use InPost\Shipping\Geocoding\Exception\NetworkException;
use InPost\Shipping\Geocoding\GeocoderInterface;
use InPost\Shipping\Geocoding\GoogleMaps\Exception\GoogleMapsException;
use InPost\Shipping\Geocoding\GoogleMaps\Model\GeocodingResponse;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;

final class GoogleMapsGeocoder implements GeocoderInterface
{
    private const API_URL = 'https://maps.google.com/maps/api/geocode/json';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var string
     */
    private $apiKey;

    public function __construct(ClientInterface $client, RequestFactoryInterface $requestFactory, string $apiKey)
    {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
        $this->apiKey = $apiKey;
    }

    public function geocode(Address $address): ?Coordinates
    {
        if ('' === $address->getAddress()) {
            return null;
        }

        $response = $this->sendRequest($address);

        if (GeocodingResponse::STATUS_ZERO_RESULTS === $response->status) {
            return null;
        }

        if (GeocodingResponse::STATUS_OK !== $response->status) {
            throw GoogleMapsException::create($response);
        }

        if (null === $result = $response->getFirstResult()) {
            return null;
        }

        $location = $result->geometry->location;

        return new Coordinates($location->lat, $location->lng);
    }

    private function sendRequest(Address $address): GeocodingResponse
    {
        $request = $this->createRequest($address);

        try {
            $response = $this->client->sendRequest($request);
        } catch (NetworkExceptionInterface $e) {
            throw new NetworkException($e);
        }

        if (200 !== $response->getStatusCode()) {
            throw new HttpException($request, $response);
        }

        try {
            return GeocodingResponse::fromJson((string) $response->getBody());
        } catch (\Exception $e) {
            throw new GeocodingException('Could not deserialize the Google Maps API response.', 0, $e);
        }
    }

    private function createRequest(Address $address): RequestInterface
    {
        $query = http_build_query([
            'key' => $this->apiKey,
            'address' => $this->formatAddress($address),
            'region' => $address->getCountry(),
        ], '', '&', PHP_QUERY_RFC3986);

        $uri = sprintf('%s?%s', self::API_URL, $query);

        return $this->requestFactory->createRequest('GET', $uri);
    }

    private function formatAddress(Address $address): string
    {
        if ('' === $address->getPostcode() || '' === $address->getCity()) {
            return $address->getAddress();
        }

        return sprintf('%s, %s %s', $address->getAddress(), $address->getPostcode(), $address->getCity());
    }
}
