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

final class Version20230322114936 extends AbstractMigration
{
    private const CONTENT_MASTER_DOC_ID = 'contentMasterDocumentId';

    private const CONTENT_MAIN_DOC_ID = 'contentMainDocumentId';

    private const TABLES = ['documents_page', 'documents_snippet', 'documents_printpage'];

    public function getDescription(): string
    {
        return 'rename master to main';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE email_blacklist RENAME email_blocklist;');

        foreach (self::TABLES as $tableName) {
            if ($schema->getTable($tableName)->hasColumn(self::CONTENT_MASTER_DOC_ID)) {
                $this->addSql(sprintf('ALTER TABLE %s CHANGE COLUMN %s %s int(11) DEFAULT NULL NULL;', $tableName, self::CONTENT_MASTER_DOC_ID, self::CONTENT_MAIN_DOC_ID));
            }
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE email_blocklist RENAME email_blacklist;');

        foreach (self::TABLES as $tableName) {
            if ($schema->getTable($tableName)->hasColumn(self::CONTENT_MAIN_DOC_ID)) {
                $this->addSql(sprintf('ALTER TABLE %s CHANGE COLUMN %s %s int(11) DEFAULT NULL NULL;', $tableName, self::CONTENT_MAIN_DOC_ID, self::CONTENT_MASTER_DOC_ID));
            }
        }
    }
}
