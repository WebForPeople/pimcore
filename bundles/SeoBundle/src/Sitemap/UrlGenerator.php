<?php

declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\SeoBundle\Sitemap;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RequestContext;

/**
 * A simple absolute URL generator accepting a path and generating absolute URLs
 * for the current request context. Parts of the URL (e.g. host or scheme) can be
 * influenced by passing them as options.
 */
class UrlGenerator implements UrlGeneratorInterface
{
    private RequestContext $requestContext;

    private OptionsResolver $optionsResolver;

    public function __construct(RequestContext $requestContext)
    {
        $this->requestContext = $requestContext;

        $this->optionsResolver = new OptionsResolver();
        $this->configureOptions($this->optionsResolver);
    }

    protected function configureOptions(OptionsResolver $options): void
    {
        $options->setDefaults([
            'scheme' => $this->requestContext->getScheme(),
            'host' => $this->requestContext->getHost(),
            'base_url' => $this->requestContext->getBaseUrl(),
        ]);

        $options->setDefault('port', function (Options $options) {
            if ('http' === $options['scheme'] && 80 !== $this->requestContext->getHttpPort()) {
                return $this->requestContext->getHttpPort();
            }

            if ('https' === $options['scheme'] && 443 !== $this->requestContext->getHttpsPort()) {
                return $this->requestContext->getHttpsPort();
            }

            return null;
        });

        $options->setAllowedValues('scheme', ['http', 'https']);
        $options->setAllowedTypes('host', 'string');
        $options->setAllowedTypes('port', ['int', 'null']);
        $options->setAllowedTypes('base_url', 'string');
    }

    protected function resolveOptions(array $options): array
    {
        return $this->optionsResolver->resolve($options);
    }

    public function generateUrl(string $path, array $options = []): string
    {
        $options = $this->resolveOptions($options);

        $scheme = $options['scheme'];
        $host = $options['host'];
        $port = $options['port'];

        if (!empty($port)) {
            $port = ':' . $port;
        }

        $path = $options['base_url'] . $path;
        if (!empty($path)) {
            $path = '/' . ltrim($path, '/');
        }

        return $scheme . '://' . $host . $port . $path;
    }
}
