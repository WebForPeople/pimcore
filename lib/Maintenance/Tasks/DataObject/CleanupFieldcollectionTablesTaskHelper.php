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

namespace Pimcore\Maintenance\Tasks\DataObject;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

/**
 * @internal
 */
class CleanupFieldcollectionTablesTaskHelper implements ConcreteTaskHelperInterface
{
    private const PIMCORE_FIELDCOLLECTION_CLASS_DIRECTORY =
        PIMCORE_CLASS_DEFINITION_DIRECTORY . '/fieldcollections';

    public function __construct(
        private LoggerInterface $logger,
        private DataObjectTaskHelperInterface $helper,
        private Connection $db
    ) {
    }

    public function cleanupCollectionTable(): void
    {
        $collectionNames =
            $this->helper->getCollectionNames(self::PIMCORE_FIELDCOLLECTION_CLASS_DIRECTORY);

        if (empty($collectionNames)) {
            return;
        }

        $tasks = [
            [
                'localized' => false,
                'prefix' => 'object_collection_',
                'pattern' => "object\_collection\_%",
            ],
        ];
        foreach ($tasks as $task) {
            $prefix = $task['prefix'];
            $pattern = $task['pattern'];
            $tableNames = $this->db->fetchAllAssociative("SHOW TABLES LIKE '" . $pattern . "'");

            foreach ($tableNames as $tableName) {
                $tableName = current($tableName);

                $fieldDescriptor = substr($tableName, strlen($prefix));
                $idx = strpos($fieldDescriptor, '_');
                $fcType = substr($fieldDescriptor, 0, $idx);
                $fcType = $collectionNames[strtolower($fcType)] ?? $fcType;

                if (!$this->checkIfFcExists($fcType, $tableName)) {
                    continue;
                }

                $classId = substr($fieldDescriptor, $idx + 1);

                $isLocalized = false;

                if (str_starts_with($classId, 'localized_')) {
                    $isLocalized = true;
                    $classId = substr($classId, strlen('localized_'));
                }

                $this->helper->cleanupTable($tableName, $classId, $isLocalized);
            }
        }
    }

    private function checkIfFcExists(string $fcType, string $tableName): bool
    {
        $fcDef = \Pimcore\Model\DataObject\Fieldcollection\Definition::getByKey($fcType);
        if (!$fcDef) {
            $this->logger->error("Fieldcollection '" . $fcType . "' not found. Please check table " . $tableName);

            return false;
        }

        return true;
    }
}
