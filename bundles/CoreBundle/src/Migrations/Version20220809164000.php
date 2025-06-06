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

namespace Pimcore\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Pimcore\Db;

final class Version20220809164000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migrate staticGeneratorEnabled attributes of doc types in settings_store to boolean';
    }

    public function up(Schema $schema): void
    {
        $db = Db::get();

        $docTypeList = $db->fetchAllAssociative("SELECT id, data FROM `settings_store` WHERE scope = 'pimcore_document_types'");
        foreach ($docTypeList as $docType) {
            $dataArray = json_decode($docType['data'], true);
            $dataArray['staticGeneratorEnabled'] = (bool) $dataArray['staticGeneratorEnabled'];
            $docType['data'] = json_encode($dataArray);
            $this->addSql("UPDATE `settings_store` SET data = :data WHERE id = :id AND scope = 'pimcore_document_types'", $docType);
        }
    }

    public function down(Schema $schema): void
    {
        $db = Db::get();

        $docTypeList = $db->fetchAllAssociative("SELECT id, data FROM `settings_store` WHERE scope = 'pimcore_document_types'");
        foreach ($docTypeList as $docType) {
            $dataArray = json_decode($docType['data'], true);
            $dataArray['staticGeneratorEnabled'] = (int) $dataArray['staticGeneratorEnabled'];
            $docType['data'] = json_encode($dataArray);
            $this->addSql("UPDATE `settings_store` SET data = :data WHERE id = :id AND scope = 'pimcore_document_types'", $docType);
        }
    }
}
