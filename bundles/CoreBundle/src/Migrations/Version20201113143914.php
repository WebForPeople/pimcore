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

/**
 * @internal
 */
final class Version20201113143914 extends AbstractMigration
{
    private array $tables = ['documents_email', 'documents_newsletter', 'documents_page',
        'documents_snippet', 'documents_printpage', ];

    public function up(Schema $schema): void
    {
        foreach ($this->tables as $table) {
            if ($schema->getTable($table)->hasColumn('action')) {
                $this->addSql(sprintf('ALTER TABLE `%s` DROP COLUMN `action`;', $table));
            }

            if ($schema->getTable($table)->hasColumn('module')) {
                $this->addSql(sprintf('ALTER TABLE `%s` DROP COLUMN `module`;', $table));
            }
        }
    }

    public function down(Schema $schema): void
    {
        $this->write(sprintf('Unable to rollback %s as the data was already deleted.', self::class));
        $this->write(sprintf('Please restore the data from tables %s manually from backup.', implode(',', $this->tables)));
    }
}
