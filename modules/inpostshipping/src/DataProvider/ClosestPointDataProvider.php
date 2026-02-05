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

namespace InPost\Shipping\DataProvider;

use InPost\Shipping\Configuration\ShipXConfiguration;
use InPost\Shipping\DataProvider\Util\EnvironmentManager;
use InPost\Shipping\Geocoding\Address;
use InPost\Shipping\Geocoding\Coordinates;
use InPost\Shipping\Geocoding\Exception\GeocodingException;
use InPost\Shipping\Geocoding\GeocoderInterface;
use InPost\Shipping\Geocoding\NullGeocoder;
use InPost\Shipping\GeoWidget\GeoWidgetTokenProvider;
use InPost\Shipping\ShipX\Exception\AccessForbiddenException;
use InPost\Shipping\ShipX\Exception\InternalServerErrorException;
use InPost\Shipping\ShipX\Exception\TokenInvalidException;
use InPost\Shipping\ShipX\Resource\NewApiPoint;
use InPost\Shipping\ShipX\Resource\Point;
use InPost\Shipping\Traits\ErrorsTrait;
use Psr\Http\Client\ClientExceptionInterface;

class ClosestPointDataProvider implements ClosestPointDataProviderInterface
{
    use ErrorsTrait;

    protected $shipXConfiguration;
    protected $tokenProvider;
    protected $context;

    /**
     * @var EnvironmentManager
     */
    private $envManager;

    /**
     * @var GeocoderInterface
     */
    private $geocoder;

    /**
     * @var array<int, Coordinates|null> coordinates by address ID
     */
    private $coordinates = [];

    private $useNewApi = true;

    public function __construct(ShipXConfiguration $shipXConfiguration, GeoWidgetTokenProvider $tokenProvider, \Context $context, ?GeocoderInterface $geocoder = null)
    {
        $this->shipXConfiguration = $shipXConfiguration;
        $this->tokenProvider = $tokenProvider;
        $this->context = $context;
        $this->geocoder = $geocoder ?? new NullGeocoder();
        $this->envManager = new EnvironmentManager($shipXConfiguration, $tokenProvider, $context);
    }

    public function getClosestPoint(\Address $address, array $options = []): ?Point
    {
        if ('' === $postcode = trim($address->postcode)) {
            return null;
        }

        $options = array_merge([
            'cashOnDelivery' => false,
            'weekendDelivery' => false,
        ], $options);

        $coordinates = $this->geocode($address);

        return $coordinates
            ? $this->getClosestPointByCoordinates($coordinates->getLatitude(), $coordinates->getLongitude(), $options)
            : $this->getClosestPointByPostCode($postcode, $options);
    }

    public function getClosestPointByPostCode(string $postCode, array $carrierData): ?Point
    {
        $searchParams = [
            'relative_post_code' => $postCode,
            'limit' => 1,
            'sort_order' => 'asc',
            'sort_by' => 'distance_to_relative_point',
        ];

        if ($carrierData['cashOnDelivery']) {
            $searchParams['payment_available'] = true;
        }
        if ($carrierData['weekendDelivery']) {
            $searchParams['location_247'] = true;
        }

        return $this->initPointData($searchParams);
    }

    public function getClosestPointByCoordinates(float $latitude, float $longitude, array $carrierData): ?Point
    {
        $searchParams = [
            'relative_point' => $latitude . ',' . $longitude,
            'limit' => 1,
            'sort_order' => 'asc',
            'sort_by' => 'distance_to_relative_point',
        ];

        if ($carrierData['cashOnDelivery']) {
            $searchParams['payment_available'] = true;
        }
        if ($carrierData['weekendDelivery']) {
            $searchParams['location_247'] = true;
        }

        return $this->initPointData($searchParams);
    }

    protected function initPointData(array $searchParams): ?Point
    {
        try {
            $this->envManager->adjustEnvironment();

            return $this->searchPoint($searchParams);
        } catch (\Exception $exception) {
            $this->addError($exception->getMessage());

            return null;
        } finally {
            $this->envManager->restoreEnvironment();
        }
    }

    protected function searchPoint($searchParams)
    {
        if (!$this->useNewApi) {
            return Point::getCollection($searchParams, 'distance_to_relative_point', 'asc')->current();
        }

        try {
            return NewApiPoint::getCollection($searchParams, 'distance_to_relative_point', 'asc')->current();
        } catch (AccessForbiddenException|TokenInvalidException|InternalServerErrorException|ClientExceptionInterface $e) {
            $this->useNewApi = false;

            return $this->searchPoint($searchParams);
        }
    }

    private function geocode(\Address $addressModel): ?Coordinates
    {
        $key = (int) $addressModel->id;

        if ($key && array_key_exists($key, $this->coordinates)) {
            return $this->coordinates[$key];
        }

        $address = $this->createAddress($addressModel);

        try {
            return $this->coordinates[$key] = $this->geocoder->geocode($address);
        } catch (GeocodingException $e) {
            return null;
        }
    }

    private function createAddress(\Address $address): Address
    {
        $countryId = $address->id_country ?: \Configuration::get('PS_COUNTRY_DEFAULT');
        $country = new \Country($countryId);

        return new Address(
            trim(sprintf('%s %s', $address->address1, $address->address2)),
            (string) $address->city,
            (string) $address->postcode,
            (string) $country->iso_code
        );
    }
}
