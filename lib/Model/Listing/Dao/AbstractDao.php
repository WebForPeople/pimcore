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

namespace Pimcore\Model\Listing\Dao;

use Pimcore\Model;

abstract class AbstractDao extends Model\Dao\AbstractDao
{
    /**
     * @var Model\DataObject\Listing
     */
    protected $model;

    abstract public function load(): array;

    abstract public function getTotalCount(): int;

    protected function getOrder(): string
    {
        $orderKey = $this->model->getOrderKey();
        $order = $this->model->getOrder();

        if (!empty($order) || !empty($orderKey)) {
            $c = 0;
            $lastOrder = $order[0] ?? null;
            $parts = [];

            foreach ($orderKey as $key) {
                if (isset($order[$c])) {
                    $lastOrder = $order[$c];
                }

                $parts[] = $key . ' ' . $lastOrder;

                $c++;
            }

            if (!empty($parts)) {
                return ' ORDER BY ' . implode(', ', $parts);
            }
        }

        return '';
    }

    protected function getGroupBy(): string
    {
        if ($this->model->getGroupBy()) {
            return ' GROUP BY ' . $this->model->getGroupBy();
        }

        return '';
    }

    protected function getOffsetLimit(): string
    {
        if (($limit = $this->model->getLimit()) && ($offset = $this->model->getOffset())) {
            return ' LIMIT ' . $offset . ',' . $limit;
        }

        if ($limit = $this->model->getLimit()) {
            return ' LIMIT ' . $limit;
        }

        return '';
    }

    protected function getCondition(): string
    {
        if ($cond = $this->model->getCondition()) {
            return ' WHERE ' . $cond . ' ';
        }

        return '';
    }
}
