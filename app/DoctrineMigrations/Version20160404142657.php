<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160404142657 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE node_has_library_content_if_variable (id BIGINT AUTO_INCREMENT NOT NULL, node_id BIGINT NOT NULL, library_content_id BIGINT NOT NULL, variable_id BIGINT NOT NULL, from_old_version_id BIGINT DEFAULT NULL, public_id VARCHAR(250) NOT NULL, action VARCHAR(250) NOT NULL, value LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_F7D45231460D9FD7 (node_id), INDEX IDX_F7D4523160B49091 (library_content_id), INDEX IDX_F7D45231F3037E8E (variable_id), INDEX IDX_F7D4523199BD42BD (from_old_version_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE node_has_library_content_if_variable ADD CONSTRAINT FK_F7D45231460D9FD7 FOREIGN KEY (node_id) REFERENCES node (id)');
        $this->addSql('ALTER TABLE node_has_library_content_if_variable ADD CONSTRAINT FK_F7D4523160B49091 FOREIGN KEY (library_content_id) REFERENCES library_content (id)');
        $this->addSql('ALTER TABLE node_has_library_content_if_variable ADD CONSTRAINT FK_F7D45231F3037E8E FOREIGN KEY (variable_id) REFERENCES variable (id)');
        $this->addSql('ALTER TABLE node_has_library_content_if_variable ADD CONSTRAINT FK_F7D4523199BD42BD FOREIGN KEY (from_old_version_id) REFERENCES node_has_library_content_if_variable (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE node_has_library_content_if_variable DROP FOREIGN KEY FK_F7D4523199BD42BD');
        $this->addSql('DROP TABLE node_has_library_content_if_variable');
    }
}
