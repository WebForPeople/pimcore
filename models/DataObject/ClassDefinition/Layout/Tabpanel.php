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

namespace Pimcore\Model\DataObject\ClassDefinition\Layout;

use Pimcore\Model;

class Tabpanel extends Model\DataObject\ClassDefinition\Layout
{
    /**
     * Static type of this element
     *
     * @internal
     *
     */
    public string $fieldtype = 'tabpanel';

    /**
     * @internal
     *
     */
    public bool $border = false;

    /**
     * @internal
     *
     */
    public ?string $tabPosition = 'top';

    public function getBorder(): bool
    {
        return $this->border;
    }

    public function setBorder(bool $border): void
    {
        $this->border = $border;
    }

    public function getTabPosition(): string
    {
        return $this->tabPosition ?? 'top';
    }

    public function setTabPosition(?string $tabPosition): void
    {
        $this->tabPosition = $tabPosition;
    }
}
