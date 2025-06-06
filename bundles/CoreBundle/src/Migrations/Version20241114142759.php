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
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241114142759 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Introducing an index for user workspaces table';
    }

    public function up(Schema $schema): void
    {
        $this->createIndex($schema, 'users_workspaces_asset');
        $this->createIndex($schema, 'users_workspaces_document');
        $this->createIndex($schema, 'users_workspaces_object');
    }

    public function down(Schema $schema): void
    {
        $this->removeIndex($schema, 'users_workspaces_asset');
        $this->removeIndex($schema, 'users_workspaces_document');
        $this->removeIndex($schema, 'users_workspaces_object');
    }

    /**
     * @throws SchemaException
     */
    private function createIndex(Schema $schema, string $tableName): void
    {
        if (
            $schema->hasTable($tableName) &&
            !$schema->getTable($tableName)->hasIndex('idx_users_workspaces_list_permission')) {
            $schema->getTable($tableName)->addIndex(
                ['userId', 'cpath', 'list'],
                'idx_users_workspaces_list_permission'
            );
        }
    }

    /**
     * @throws SchemaException
     */
    private function removeIndex(Schema $schema, string $tableName): void
    {
        if (
            $schema->hasTable($tableName) &&
            $schema->getTable($tableName)->hasIndex('idx_users_workspaces_list_permission')) {
            $schema->getTable($tableName)->dropIndex('idx_users_workspaces_list_permission');
        }
    }
}
