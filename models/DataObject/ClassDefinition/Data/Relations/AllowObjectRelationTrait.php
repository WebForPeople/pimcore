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

namespace Pimcore\Model\DataObject\ClassDefinition\Data\Relations;

use Pimcore\Logger;
use Pimcore\Model\DataObject;

/**
 * @internal
 */
trait AllowObjectRelationTrait
{
    /**
     * Checks if an object is an allowed relation
     *
     *
     *
     * @internal
     */
    protected function allowObjectRelation(DataObject\AbstractObject $object): bool
    {
        if ($object->getId() <= 0) {
            return false;
        }

        $allowedClasses = $this->getClasses();
        $allowed = true;
        if (!$this->getObjectsAllowed()) {
            $allowed = false;
        } elseif (count($allowedClasses) > 0) {
            $allowedClassnames = array_column($allowedClasses, 'classes');

            //check for allowed classes
            if ($object instanceof DataObject\Concrete) {
                $classname = $object->getClassName();
                if (!in_array($classname, $allowedClassnames, true)) {
                    $allowed = false;
                }
            } elseif ($object instanceof DataObject\Folder) {
                if (!in_array('folder', $allowedClassnames, true)) {
                    $allowed = false;
                }
            } else {
                $allowed = false;
            }
        } else {
            //don't check if no allowed classes set
        }

        Logger::debug('checked object relation to target object [' . $object->getId() . '] in field [' . $this->getName() . '], allowed:' . $allowed);

        return $allowed;
    }
}
