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

namespace InPost\Shipping\DataProvider\Util;

use InPost\Shipping\Configuration\ShipXConfiguration;
use InPost\Shipping\GeoWidget\GeoWidgetTokenProvider;

final class EnvironmentManager
{
    /**
     * @var ShipXConfiguration
     */
    private $configuration;

    /**
     * @var GeoWidgetTokenProvider
     */
    private $tokenProvider;

    /**
     * @var \Context
     */
    private $context;

    /**
     * @var bool
     */
    private $initialConfig;

    public function __construct(ShipXConfiguration $configuration, GeoWidgetTokenProvider $tokenProvider, \Context $context)
    {
        $this->configuration = $configuration;
        $this->tokenProvider = $tokenProvider;
        $this->context = $context;
    }

    public function isSandboxMode(): bool
    {
        if ($this->context->controller instanceof \FrontController && $token = $this->tokenProvider->getToken()) {
            return $token->isSandbox();
        }

        return $this->configuration->useSandboxMode();
    }

    /**
     * @return bool whether sandbox mode is enabled
     */
    public function adjustEnvironment(): bool
    {
        if (!isset($this->initialConfig)) {
            $this->initialConfig = $this->configuration->useSandboxMode();
        }

        $sandbox = $this->isSandboxMode();
        $this->configuration->setSandboxMode($sandbox);

        return $sandbox;
    }

    public function restoreEnvironment(): void
    {
        if (!isset($this->initialConfig)) {
            return;
        }

        $this->configuration->setSandboxMode($this->initialConfig);
        unset($this->initialConfig);
    }
}
