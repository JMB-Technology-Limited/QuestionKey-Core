<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160301150556 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE library_content (id BIGINT AUTO_INCREMENT NOT NULL, tree_version_id BIGINT NOT NULL, public_id VARCHAR(250) NOT NULL, title_admin VARCHAR(250) NOT NULL, body_text LONGTEXT DEFAULT NULL, body_html LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_84048899B0AFDF04 (tree_version_id), UNIQUE INDEX public_id (tree_version_id, public_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE node_has_library_content (node_id BIGINT NOT NULL, library_content_id BIGINT NOT NULL, sort INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_79F190E4460D9FD7 (node_id), INDEX IDX_79F190E460B49091 (library_content_id), PRIMARY KEY(node_id, library_content_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE library_content ADD CONSTRAINT FK_84048899B0AFDF04 FOREIGN KEY (tree_version_id) REFERENCES tree_version (id)');
        $this->addSql('ALTER TABLE node_has_library_content ADD CONSTRAINT FK_79F190E4460D9FD7 FOREIGN KEY (node_id) REFERENCES node (id)');
        $this->addSql('ALTER TABLE node_has_library_content ADD CONSTRAINT FK_79F190E460B49091 FOREIGN KEY (library_content_id) REFERENCES library_content (id)');
        $this->addSql('ALTER TABLE tree_version ADD feature_library_content TINYINT(1) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE node_has_library_content DROP FOREIGN KEY FK_79F190E460B49091');
        $this->addSql('DROP TABLE library_content');
        $this->addSql('DROP TABLE node_has_library_content');
        $this->addSql('ALTER TABLE tree_version DROP feature_library_content');
    }
}
