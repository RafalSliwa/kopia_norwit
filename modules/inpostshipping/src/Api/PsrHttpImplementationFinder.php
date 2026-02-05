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

namespace InPost\Shipping\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Psr18Client;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class PsrHttpImplementationFinder implements ServiceSubscriberInterface
{
    private $locator;

    private $client;
    private $requestFactory;
    private $streamFactory;
    private $responseFactory;

    private $cache = [];

    public function __construct(ContainerInterface $locator)
    {
        $this->locator = $locator;
    }

    public static function getSubscribedServices(): array
    {
        return [
            '?' . ClientInterface::class,
            '?' . RequestFactoryInterface::class,
            '?' . StreamFactoryInterface::class,
            '?' . ResponseFactoryInterface::class,
        ];
    }

    public function getClient(): ClientInterface
    {
        return $this->client ?? $this->client = $this->findClient();
    }

    public function getRequestFactory(): RequestFactoryInterface
    {
        return $this->requestFactory ?? $this->requestFactory = $this->findRequestFactory();
    }

    public function getStreamFactory(): StreamFactoryInterface
    {
        return $this->streamFactory ?? $this->streamFactory = $this->findStreamFactory();
    }

    public function getResponseFactory(): ResponseFactoryInterface
    {
        return $this->responseFactory ?? $this->responseFactory = $this->findResponseFactory();
    }

    private function findClient(): ClientInterface
    {
        $client = $this->getService(ClientInterface::class)
            ?? $this->getGuzzleClient()
            ?? $this->createSymfonyClient();

        if ($client instanceof ClientInterface) {
            return $client;
        }

        throw new \RuntimeException(sprintf('No %s implementation found', ClientInterface::class));
    }

    private function getGuzzleClient(): ?ClientInterface
    {
        return class_exists(Client::class) && is_subclass_of(Client::class, ClientInterface::class)
            ? new Client([
                'connect_timeout' => 3.,
                'timeout' => 10.,
            ])
            : null;
    }

    private function createSymfonyClient(): ClientInterface
    {
        $client = HttpClient::create([
            'max_redirects' => 0,
            'timeout' => 3.,
            'max_duration' => 10.,
        ]);

        return new Psr18Client(
            $client,
            $this->getResponseFactory(),
            $this->getStreamFactory()
        );
    }

    private function findRequestFactory(): RequestFactoryInterface
    {
        $factory = $this->getService(RequestFactoryInterface::class)
            ?? $this->getGuzzleFactory()
            ?? $this->getNyholmFactory();

        if ($factory instanceof RequestFactoryInterface) {
            return $factory;
        }

        throw new \RuntimeException(sprintf('No %s implementation found', RequestFactoryInterface::class));
    }

    private function findStreamFactory(): StreamFactoryInterface
    {
        $factory = $this->getService(StreamFactoryInterface::class)
            ?? $this->getGuzzleFactory()
            ?? $this->getNyholmFactory();

        if ($factory instanceof StreamFactoryInterface) {
            return $factory;
        }

        throw new \RuntimeException(sprintf('No %s implementation found', StreamFactoryInterface::class));
    }

    private function getGuzzleFactory(): ?HttpFactory
    {
        if (array_key_exists('guzzle_factory', $this->cache)) {
            return $this->cache['guzzle_factory'];
        }

        return $this->cache['guzzle_factory'] = class_exists(HttpFactory::class) ? new HttpFactory() : null;
    }

    private function getNyholmFactory(): ?Psr17Factory
    {
        if (array_key_exists('nyholm_factory', $this->cache)) {
            return $this->cache['nyholm_factory'];
        }

        return $this->cache['nyholm_factory'] = class_exists(Psr17Factory::class) ? new Psr17Factory() : null;
    }

    private function findResponseFactory(): ResponseFactoryInterface
    {
        $factory = $this->getService(ResponseFactoryInterface::class)
            ?? $this->getGuzzleFactory()
            ?? $this->getNyholmFactory();

        if ($factory instanceof ResponseFactoryInterface) {
            return $factory;
        }

        throw new \RuntimeException(sprintf('No %s implementation found', ResponseFactoryInterface::class));
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $name
     *
     * @return T|null
     */
    private function getService(string $name): ?object
    {
        if (!$this->locator->has($name)) {
            return null;
        }

        try {
            return $this->locator->get($name);
        } catch (\LogicException $e) {
            /* @see Psr18Client::__construct */

            return null;
        }
    }
}
