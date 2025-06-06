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

namespace Pimcore\Model\Asset\MetaData\ClassDefinition\Data;

use Pimcore\Model\Element\Service;

class Document extends Data
{
    public function normalize(mixed $value, array $params = []): mixed
    {
        $element = $value;
        if (is_string($value)) {
            $element = Service::getElementByPath('document', $value);
        }
        if ($element instanceof \Pimcore\Model\Document) {
            return $element->getId();
        }

        return null;
    }

    public function denormalize(mixed $value, array $params = []): mixed
    {
        $element = null;
        if (is_numeric($value)) {
            $element = Service::getElementById('document', $value);
        }

        return $element;
    }

    public function transformGetterData(mixed $data, array $params = []): mixed
    {
        if (is_numeric($data)) {
            return \Pimcore\Model\Document\Service::getElementById('document', (int) $data);
        }

        return $data;
    }

    public function transformSetterData(mixed $data, array $params = []): mixed
    {
        if ($data instanceof \Pimcore\Model\Document) {
            return $data->getId();
        }

        return $data;
    }

    public function getDataFromEditMode(mixed $data, array $params = []): int|string|null
    {
        $element = $data;
        if (is_string($data)) {
            $element = Service::getElementByPath('document', $data);
        }
        if ($element instanceof \Pimcore\Model\Document) {
            return $element->getId();
        }

        return '';
    }

    public function getDataForResource(mixed $data, array $params = []): mixed
    {
        if ($data instanceof \Pimcore\Model\Document) {
            return $data->getId();
        }

        return $data;
    }

    public function getDataForEditMode(mixed $data, array $params = []): mixed
    {
        if (is_numeric($data)) {
            $data = Service::getElementById('document', $data);
        }
        if ($data instanceof \Pimcore\Model\Document) {
            return $data->getRealFullPath();
        } else {
            return '';
        }
    }

    public function getDataForListfolderGrid(mixed $data, array $params = []): mixed
    {
        if (is_numeric($data)) {
            $data = \Pimcore\Model\Document::getById($data);
        }

        if ($data instanceof \Pimcore\Model\Document) {
            return $data->getFullPath();
        }

        return $data;
    }

    public function resolveDependencies(mixed $data, array $params = []): array
    {
        if ($data instanceof \Pimcore\Model\Document && isset($params['type'])) {
            $elementId = $data->getId();
            $elementType = $params['type'];

            $key = $elementType . '_' . $elementId;

            return [
                $key => [
                    'id' => $elementId,
                    'type' => $elementType,
                ], ];
        }

        return [];
    }

    public function getDataFromListfolderGrid(mixed $data, array $params = []): ?int
    {
        $data = \Pimcore\Model\Document::getByPath($data);

        if ($data instanceof \Pimcore\Model\Document) {
            return $data->getId();
        }

        return null;
    }
}
