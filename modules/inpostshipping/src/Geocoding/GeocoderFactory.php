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

namespace InPost\Shipping\Geocoding;

use InPost\Shipping\Configuration\CheckoutConfiguration;
use InPost\Shipping\Geocoding\GoogleMaps\GoogleMapsGeocoder;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class GeocoderFactory implements ServiceSubscriberInterface
{
    /**
     * @var CheckoutConfiguration
     */
    private $configuration;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(CheckoutConfiguration $configuration, ContainerInterface $container)
    {
        $this->configuration = $configuration;
        $this->container = $container;
    }

    public static function getSubscribedServices(): array
    {
        return [
            GoogleMapsGeocoder::class,
        ];
    }

    public function create(): GeocoderInterface
    {
        if ($this->configuration->getGoogleApiKey()) {
            return $this->container->get(GoogleMapsGeocoder::class);
        }

        return new NullGeocoder();
    }
}
