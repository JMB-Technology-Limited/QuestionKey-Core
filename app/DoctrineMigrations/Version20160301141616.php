<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160301141616 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE variable (id BIGINT AUTO_INCREMENT NOT NULL, tree_version_id BIGINT NOT NULL, name VARCHAR(250) NOT NULL, type VARCHAR(250) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_CC4D878DB0AFDF04 (tree_version_id), UNIQUE INDEX name (tree_version_id, name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE variable ADD CONSTRAINT FK_CC4D878DB0AFDF04 FOREIGN KEY (tree_version_id) REFERENCES tree_version (id)');
        $this->addSql('ALTER TABLE tree_version ADD feature_variables TINYINT(1) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE variable');
        $this->addSql('ALTER TABLE tree_version DROP feature_variables');
    }
}
