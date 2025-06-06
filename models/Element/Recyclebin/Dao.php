<?php

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Model\Element\Recyclebin;

use Pimcore\Model;

/**
 * @internal
 *
 * @property \Pimcore\Model\Element\Recyclebin $model
 */
class Dao extends Model\Dao\AbstractDao
{
    public function flush(): void
    {
        $this->db->executeStatement('DELETE FROM recyclebin');
    }
}
