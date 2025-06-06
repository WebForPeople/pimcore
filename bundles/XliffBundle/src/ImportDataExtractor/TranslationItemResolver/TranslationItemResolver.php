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

namespace Pimcore\Bundle\XliffBundle\ImportDataExtractor\TranslationItemResolver;

use Pimcore\Bundle\XliffBundle\TranslationItemCollection\TranslationItem;
use Pimcore\Model\Element;

class TranslationItemResolver implements TranslationItemResolverInterface
{
    public function resolve(string $type, string $id): ?TranslationItem
    {
        if (!$element = Element\Service::getElementById($type, (int) $id)) {
            return null;
        }

        return new TranslationItem($type, $id, $element);
    }
}
