<?php

namespace Pimcore\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;

class Version20200508074340 extends AbstractPimcoreMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE `classificationstore_collectionrelations`
            DROP FOREIGN KEY IF EXISTS `FK_classificationstore_collectionrelations_groups`');

        $this->addSql('ALTER TABLE `classificationstore_relations`
            DROP FOREIGN KEY IF EXISTS `FK_classificationstore_relations_classificationstore_groups`');
        $this->addSql('ALTER TABLE `classificationstore_relations`
            DROP FOREIGN KEY IF EXISTS `FK_classificationstore_relations_classificationstore_keys`');

        $this->addSql('ALTER TABLE `classificationstore_collections`
            CHANGE `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            CHANGE `storeId` `storeId` INT(11) UNSIGNED NULL,
            CHANGE `creationDate` `creationDate` INT(11) UNSIGNED NOT NULL DEFAULT 0,
            CHANGE `modificationDate` `modificationDate` INT(11) UNSIGNED NOT NULL DEFAULT 0');

        $this->addSql('ALTER TABLE `classificationstore_groups`
            CHANGE `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            CHANGE `storeId` `storeId` INT(11) UNSIGNED NULL,
            CHANGE `parentId` `parentId` INT(11) UNSIGNED NOT NULL DEFAULT 0,
            CHANGE `creationDate` `creationDate` INT(11) UNSIGNED NOT NULL DEFAULT 0,
            CHANGE `modificationDate` `modificationDate` INT(11) UNSIGNED NOT NULL DEFAULT 0');

        $this->addSql('ALTER TABLE `classificationstore_keys`
            CHANGE `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            CHANGE `storeId` `storeId` INT(11) UNSIGNED NULL,
            CHANGE `creationDate` `creationDate` INT(11) UNSIGNED NOT NULL DEFAULT 0,
            CHANGE `modificationDate` `modificationDate` INT(11) UNSIGNED NOT NULL DEFAULT 0');

        $this->addSql('ALTER TABLE `classificationstore_relations`
            CHANGE `groupId` `groupId` INT(11) UNSIGNED,
            CHANGE `keyId` `keyId` INT(11) UNSIGNED');

        $this->addSql('ALTER TABLE `classificationstore_collectionrelations`
            CHANGE `groupId` `groupId` INT(11) UNSIGNED,
            CHANGE `colId` `colId` INT(11) UNSIGNED');

        $this->addSql('ALTER TABLE `classificationstore_collectionrelations`
            ADD CONSTRAINT `FK_classificationstore_collectionrelations_groups` foreign key (groupId) references classificationstore_groups (id)
            on delete cascade');

        $this->addSql('ALTER TABLE `classificationstore_relations`
            ADD CONSTRAINT `FK_classificationstore_relations_classificationstore_groups` foreign key (groupId) references classificationstore_groups (id)
            on delete cascade');
        $this->addSql('ALTER TABLE `classificationstore_relations`
            ADD CONSTRAINT `FK_classificationstore_relations_classificationstore_keys` foreign key (keyId) references classificationstore_keys (id)
            on delete cascade');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
