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

use Exception;
use InPost\Shipping\Configuration\ShipXConfiguration;
use InPost\Shipping\DataProvider\Util\EnvironmentManager;
use InPost\Shipping\Entity\PointCache;
use InPost\Shipping\GeoWidget\GeoWidgetTokenProvider;
use InPost\Shipping\Repository\PointRepository;
use InPost\Shipping\ShipX\Exception\AccessForbiddenException;
use InPost\Shipping\ShipX\Exception\InternalServerErrorException;
use InPost\Shipping\ShipX\Exception\ResourceNotFoundException;
use InPost\Shipping\ShipX\Exception\TokenInvalidException;
use InPost\Shipping\ShipX\Resource\NewApiPoint;
use InPost\Shipping\ShipX\Resource\Point;
use InPost\Shipping\Traits\ErrorsTrait;
use Psr\Http\Client\ClientExceptionInterface;

class PointDataProvider
{
    use ErrorsTrait;

    protected $shipXConfiguration;
    protected $tokenProvider;
    protected $context;
    protected $pointRepository;

    /** @var Point[] */
    protected $points = [];
    protected $useNewApi = true;

    /**
     * @var EnvironmentManager
     */
    private $envManager;

    public function __construct(ShipXConfiguration $shipXConfiguration, GeoWidgetTokenProvider $tokenProvider, PointRepository $pointRepository, \Context $context)
    {
        $this->shipXConfiguration = $shipXConfiguration;
        $this->tokenProvider = $tokenProvider;
        $this->pointRepository = $pointRepository;
        $this->context = $context;
        $this->envManager = new EnvironmentManager($shipXConfiguration, $tokenProvider, $context);
    }

    public function getPointData($pointId): ?Point
    {
        if (null === $pointId || '' === $pointId) {
            return null;
        }

        if (!array_key_exists($pointId, $this->points)) {
            $this->initPointData($pointId);
        }

        return $this->points[$pointId] ?? null;
    }

    protected function initPointData($pointId)
    {
        try {
            $isSandbox = $this->envManager->adjustEnvironment();
            $cachedPoint = $isSandbox ? null : $this->getCachedPoint($pointId);

            if ($cachedPoint && $cachedPoint->isFresh()) {
                $this->points[$pointId] = $cachedPoint->getPoint();

                return;
            }

            $this->points[$pointId] = $this->fetchPoint($pointId);

            if ($isSandbox) {
                return;
            }

            if ($cachedPoint) {
                $this->pointRepository->update($this->points[$pointId]);
            } else {
                $this->pointRepository->insert($this->points[$pointId]);
            }
        } catch (ResourceNotFoundException $exception) {
            $this->points[$pointId] = null;
        } catch (Exception $exception) {
            $this->addError($exception->getMessage());
        } finally {
            $this->envManager->restoreEnvironment();
        }
    }

    protected function fetchPoint($pointId)
    {
        if (!$this->useNewApi) {
            return Point::get($pointId);
        }

        try {
            return NewApiPoint::get($pointId);
        } catch (Exception $exception) {
            if (
                $exception instanceof AccessForbiddenException ||
                $exception instanceof TokenInvalidException ||
                $exception instanceof InternalServerErrorException ||
                $exception instanceof ClientExceptionInterface
            ) {
                $this->useNewApi = false;

                return $this->fetchPoint($pointId);
            }

            throw $exception;
        }
    }

    private function getCachedPoint(string $pointId): ?PointCache
    {
        try {
            return $this->pointRepository->findByPointId($pointId);
        } catch (Exception $exception) {
            return null;
        }
    }
}
