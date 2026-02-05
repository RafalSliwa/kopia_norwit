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

namespace InPost\Shipping\Helper;

use InPost\Shipping\Api\PsrHttpImplementationFinder;
use InPost\Shipping\Geocoding\Address;
use InPost\Shipping\Geocoding\Exception\GeocodingException;
use InPost\Shipping\Geocoding\GoogleMaps\GoogleMapsGeocoder;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * @deprecated use {@see GoogleMapsGeocoder} instead
 */
class CoordinatesExtractor
{
    protected $module;

    public function __construct(\Module $module)
    {
        $this->module = $module;
    }

    public function getCoordinates(string $address, int $idCountry, string $googleApiKey): ?array
    {
        if (!$googleApiKey || !$address || !$idCountry) {
            return null;
        }

        $geocoder = $this->createGeocoder($googleApiKey);
        $country = (string) (new \Country($idCountry))->iso_code;

        try {
            $coordinates = $geocoder->geocode(new Address($address, '', '', $country));

            if (null === $coordinates) {
                return null;
            }

            return ['lat' => $coordinates->getLatitude(), 'lng' => $coordinates->getLongitude()];
        } catch (GeocodingException $e) {
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $message = 'Unexpected error';
        }

        return ['error' => $message];
    }

    private function createGeocoder(string $apiKey): GoogleMapsGeocoder
    {
        static $psrFactory;

        $psrFactory = $psrFactory ?? new PsrHttpImplementationFinder(new class() implements ContainerInterface {
            public function get(string $id)
            {
                throw new ServiceNotFoundException($id);
            }

            public function has(string $id): bool
            {
                return false;
            }
        });

        return new GoogleMapsGeocoder(
            $psrFactory->getClient(),
            $psrFactory->getRequestFactory(),
            $apiKey
        );
    }
}
