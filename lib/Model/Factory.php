<?php

declare(strict_types = 1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Model;

use Pimcore\Loader\ImplementationLoader\ClassMapLoader;
use Pimcore\Loader\ImplementationLoader\ImplementationLoader;

/**
 * @internal
 */
final class Factory extends ImplementationLoader implements FactoryInterface
{
    public function getClassMap(): array
    {
        $map = [];
        foreach ($this->loaders as $loader) {
            if ($loader instanceof ClassMapLoader) {
                $map = array_merge($map, $loader->getClassMap());
            }
        }

        return $map;
    }

    public function build(string $name, array $params = []): AbstractModel
    {
        return parent::build($name, $params);
    }
}
