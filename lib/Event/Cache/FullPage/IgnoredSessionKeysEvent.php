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

namespace Pimcore\Event\Cache\FullPage;

use Symfony\Contracts\EventDispatcher\Event;

class IgnoredSessionKeysEvent extends Event
{
    /**
     * Session keys which will be ignored when determining
     * if the full page cache should be disabled due to
     * existing session data.
     *
     * @var string[]
     */
    private array $keys = [];

    /**
     * @param string[] $keys
     */
    public function __construct(array $keys = [])
    {
        $this->keys = $keys;
    }

    /**
     * @return string[]
     */
    public function getKeys(): array
    {
        return $this->keys;
    }

    /**
     * @param string[] $keys
     */
    public function setKeys(array $keys): void
    {
        $this->keys = $keys;
    }
}
