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
use Pimcore\Model\Tool\SettingsStore;

final class Version20230120111111 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'In case the application logger permissions already exists, mark the ApplicationLoggerBundle as installed';
    }

    public function up(Schema $schema): void
    {
        if (!SettingsStore::get('BUNDLE_INSTALLED__Pimcore\\Bundle\\ApplicationLoggerBundle\\PimcoreApplicationLoggerBundle', 'pimcore')) {
            SettingsStore::set('BUNDLE_INSTALLED__Pimcore\\Bundle\\ApplicationLoggerBundle\\PimcoreApplicationLoggerBundle', true, SettingsStore::TYPE_BOOLEAN, 'pimcore');
        }

        // updating description  of permissions
        $this->addSql("UPDATE `users_permission_definitions` SET `category` = 'Application Logger Bundle' WHERE `key` = 'application_logging'");

        $this->warnIf(
            null !== SettingsStore::get('BUNDLE_INSTALLED__Pimcore\\Bundle\\ApplicationLoggerBundle\\PimcoreApplicationLoggerBundle', 'pimcore'),
            'Please make sure to enable the Pimcore\\Bundle\\ApplicationLoggerBundle\\PimcoreApplicationLoggerBundle manually in config/bundles.php',
        );
    }

    public function down(Schema $schema): void
    {
        if (SettingsStore::get('BUNDLE_INSTALLED__Pimcore\\Bundle\\ApplicationLoggerBundle\\PimcoreApplicationLoggerBundle', 'pimcore')) {
            SettingsStore::delete('BUNDLE_INSTALLED__Pimcore\\Bundle\\ApplicationLoggerBundle\\PimcoreApplicationLoggerBundle', 'pimcore');
        }

        // restoring the permission
        $this->addSql("UPDATE `users_permission_definitions` SET `category` = '' WHERE `key` = 'application_logging'");

        $this->write('Please deactivate the Pimcore\\Bundle\\ApplicationLoggerBundle\\PimcoreApplicationLoggerBundle manually in config/bundles.php');
    }
}
