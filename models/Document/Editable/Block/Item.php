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

namespace Pimcore\Model\Document\Editable\Block;

use Pimcore\Model\Document;

class Item extends AbstractBlockItem
{
    protected function getItemType(): string
    {
        return 'block';
    }

    public function __call(string $func, array $args): ?Document\Editable
    {
        $element = $this->getEditable($args[0]);
        $class = 'Pimcore\\Model\\Document\\Editable\\' . str_replace('get', '', $func);

        if ($element === null) {
            return new $class;
        }

        if (!strcasecmp(get_class($element), $class)) {
            return $element;
        }

        return null;
    }
}
