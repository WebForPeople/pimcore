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

namespace Pimcore\Model\Asset;

use Pimcore\Model;

/**
 * @method \Pimcore\Model\Asset\Dao getDao()
 */
class Text extends Model\Asset
{
    protected string $type = 'text';
}
