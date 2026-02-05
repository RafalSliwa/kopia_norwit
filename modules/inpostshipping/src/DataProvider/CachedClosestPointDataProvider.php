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

use InPost\Shipping\DataProvider\Util\EnvironmentManager;
use InPost\Shipping\ShipX\Resource\Point;

final class CachedClosestPointDataProvider implements ClosestPointDataProviderInterface
{
    private const CASH_ON_DELIVERY = 1;
    private const WEEKEND_DELIVERY = 2;

    /**
     * @var ClosestPointDataProviderInterface
     */
    private $provider;

    /**
     * @var EnvironmentManager
     */
    private $envManager;

    /**
     * @var \Context
     */
    private $context;

    public function __construct(ClosestPointDataProviderInterface $provider, EnvironmentManager $envManager, \Context $context)
    {
        $this->provider = $provider;
        $this->envManager = $envManager;
        $this->context = $context;
    }

    public function getClosestPoint(\Address $address, array $options = []): ?Point
    {
        $checksum = $this->generateChecksum($address);

        if (false !== $point = $this->getCachedClosestPoint($checksum, $options)) {
            return $point;
        }

        $point = $this->provider->getClosestPoint($address, $options);
        $this->cacheClosestPoint($point, $checksum, $options);

        return $point;
    }

    /**
     * @return false|Point|null
     */
    private function getCachedClosestPoint(string $checksum, array $options)
    {
        if (null === $points = $this->getCachedPoints($checksum)) {
            return false;
        }

        if (false === $data = $this->findCacheItem($points, $options)) {
            return false;
        }

        return $data ? Point::cast($data) : null;
    }

    private function cacheClosestPoint(?Point $point, string $checksum, array $options): void
    {
        $points = $this->getCachedPoints($checksum) ?? [];
        $index = $this->getCacheIndex($options);

        $points[$index] = $point ? [
            'address' => $point->address,
            'distance' => $point->distance,
            'name' => $point->name,
            'options' => $this->serializeOptions($point),
        ] : null;

        if (null !== $point && $index < $points[$index]['options']) {
            $points = $this->filterPointsCache($points, $index, $points[$index]);
        }

        $this->context->cookie->inpost_coordinates = json_encode([
            'sandbox' => $this->envManager->isSandboxMode(),
            'hash' => $checksum,
            'points' => $points,
        ]);
    }

    private function generateChecksum(\Address $address): string
    {
        return md5(sprintf(
            '%s, %s %s',
            trim($address->address1 . ' ' . $address->address2),
            trim($address->postcode),
            trim($address->city)
        ));
    }

    private function getCacheIndex(array $options): int
    {
        $index = 0;

        if (!empty($options['weekendDelivery'])) {
            $index += self::WEEKEND_DELIVERY;
        }

        if (!empty($options['cashOnDelivery'])) {
            $index += self::CASH_ON_DELIVERY;
        }

        return $index;
    }

    private function getCachedPoints(string $checksum): ?array
    {
        if (!isset($this->context->cookie->inpost_coordinates)) {
            return null;
        }

        $data = json_decode($this->context->cookie->inpost_coordinates, true);

        if (!isset($data['hash'], $data['points'], $data['sandbox']) || $data['hash'] !== $checksum) {
            unset($this->context->cookie->inpost_coordinates);

            return null;
        }

        if ($data['sandbox'] !== $this->envManager->isSandboxMode()) {
            return null;
        }

        return $data['points'];
    }

    /**
     * @return array|false|null
     */
    private function findCacheItem(array $points, array $options)
    {
        $index = $this->getCacheIndex($options);

        if (array_key_exists($index, $points)) {
            return $points[$index];
        }

        foreach ($points as $point) {
            if (null === $point) {
                continue;
            }

            if (0 === $index & $point['options']) {
                continue;
            }

            return $point;
        }

        return false;
    }

    private function filterPointsCache(array $points, int $lower, array $point): array
    {
        ['options' => $upper, 'distance' => $distance] = $point;

        foreach ($points as $key => $value) {
            if ($key <= $lower || $key > $upper) {
                continue;
            }

            if (null !== $value && $value['distance'] < $distance) {
                continue;
            }

            unset($points[$key]);
        }

        return $points;
    }

    private function serializeOptions(Point $point): int
    {
        $options = 0;

        if ($point->payment_available) {
            $options += self::CASH_ON_DELIVERY;
        }

        if ($point->location_247) {
            $options += self::WEEKEND_DELIVERY;
        }

        return $options;
    }
}
