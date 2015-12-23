<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151223141032 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tree_version_preview_code (id BIGINT AUTO_INCREMENT NOT NULL, tree_version_id BIGINT NOT NULL, created_by_id INT NOT NULL, code VARCHAR(250) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_FD5D02DFB0AFDF04 (tree_version_id), INDEX IDX_FD5D02DFB03A8386 (created_by_id), UNIQUE INDEX public_id (tree_version_id, code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tree_version_preview_code ADD CONSTRAINT FK_FD5D02DFB0AFDF04 FOREIGN KEY (tree_version_id) REFERENCES tree_version (id)');
        $this->addSql('ALTER TABLE tree_version_preview_code ADD CONSTRAINT FK_FD5D02DFB03A8386 FOREIGN KEY (created_by_id) REFERENCES fos_user (id)');
        
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE tree_version_preview_code');
    }
}
