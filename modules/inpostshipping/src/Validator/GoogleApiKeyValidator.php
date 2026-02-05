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

namespace InPost\Shipping\Validator;

use InPost\Shipping\Geocoding\Address;
use InPost\Shipping\Geocoding\Exception\GeocodingException;
use InPost\Shipping\Geocoding\GoogleMaps\Exception\GoogleMapsException;
use InPost\Shipping\Geocoding\GoogleMaps\GoogleMapsGeocoder;
use InPostShipping;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

class GoogleApiKeyValidator extends AbstractValidator
{
    private const TRANSLATION_SOURCE = 'GoogleApiKeyValidator';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    public function __construct(InPostShipping $module, ClientInterface $client, RequestFactoryInterface $requestFactory)
    {
        parent::__construct($module);

        $this->client = $client;
        $this->requestFactory = $requestFactory;
    }

    public function validate(array $data)
    {
        $this->resetErrors();

        if (empty($data['google_api_key'])) {
            return true;
        }

        $geocoder = new GoogleMapsGeocoder($this->client, $this->requestFactory, (string) $data['google_api_key']);

        try {
            $geocoder->geocode(new Address('address', 'city', '00-000', 'PL'));
        } catch (GoogleMapsException $e) {
            $this->addError(trim(sprintf($this->module->l('Geocoding failed with status code "%s". %s', self::TRANSLATION_SOURCE), $e->getStatusCode(), $e->getErrorMessage())));
        } catch (GeocodingException $e) {
            $this->addError(sprintf($this->module->l('An error occurred while trying to geocode a test address: %s', self::TRANSLATION_SOURCE), $e->getMessage()));
        } catch (\Throwable $e) {
            $this->addError($this->module->l('An unexpected error occurred', self::TRANSLATION_SOURCE));

            if (_PS_MODE_DEV_) {
                throw $e;
            }
        }

        return !$this->hasErrors();
    }
}
