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

namespace Pimcore\Model\Property;

use Pimcore\Db\Helper;
use Pimcore\Model;

/**
 * @internal
 *
 * @property \Pimcore\Model\Property $model
 */
class Dao extends Model\Dao\AbstractDao
{
    /**
     * Save object to database
     */
    public function save(): void
    {
        $data = $this->model->getData();

        if ($this->model->getType() == 'object' || $this->model->getType() == 'asset' || $this->model->getType() == 'document') {
            if ($data instanceof Model\Element\ElementInterface) {
                $data = $data->getId();
            } else {
                $data = null;
            }
        }

        if (is_array($data) || is_object($data)) {
            $data = \Pimcore\Tool\Serialize::serialize($data);
        }

        $cpath = $this->model->getCpath();
        if (empty($cpath)) {
            $element = Model\Element\Service::getElementById($this->model->getCtype(), $this->model->getCid());
            if ($element instanceof Model\Element\ElementInterface) {
                $cpath = $element->getRealFullPath();
            }
        }

        $saveData = [
            'cid' => $this->model->getCid(),
            'ctype' => $this->model->getCtype(),
            'cpath' => $this->model->getCpath(),
            'name' => $this->model->getName(),
            'type' => $this->model->getType(),
            'inheritable' => (int)$this->model->getInheritable(),
            'data' => $data,
        ];

        Helper::upsert($this->db, 'properties', $saveData, $this->getPrimaryKey('properties'));
    }
}
