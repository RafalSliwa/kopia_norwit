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

namespace InPost\Shipping\Geocoding\GoogleMaps\Exception;

use InPost\Shipping\Geocoding\Exception\GeocodingException;
use InPost\Shipping\Geocoding\GoogleMaps\Model\GeocodingResponse;

class GoogleMapsException extends GeocodingException
{
    /**
     * @var string
     */
    private $statusCode;

    /**
     * @var string|null
     */
    private $errorMessage;

    public function __construct(GeocodingResponse $response)
    {
        $this->statusCode = $response->status;
        $this->errorMessage = $response->error_message;

        parent::__construct($response->error_message ?? sprintf('Geocoding failed with status code "%s".', $response->status));
    }

    public static function create(GeocodingResponse $response): self
    {
        switch ($response->status) {
            case OverDailyLimitException::STATUS:
                return new OverDailyLimitException($response);
            default:
                return new self($response);
        }
    }

    public function getStatusCode(): string
    {
        return $this->statusCode;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }
}
