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

namespace Pimcore\Model\DataObject\ClassDefinition\Loader;

use Pimcore\Loader\ImplementationLoader\ImplementationLoader;
use Pimcore\Model\DataObject\ClassDefinition\Layout;

/**
 * @internal
 */
final class LayoutLoader extends ImplementationLoader implements LayoutLoaderInterface
{
    public function build(string $name, array $params = []): Layout
    {
        return parent::build($name, $params);
    }
}
