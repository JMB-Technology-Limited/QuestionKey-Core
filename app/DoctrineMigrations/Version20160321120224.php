<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160321120224 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE node_option_variable_action (id BIGINT AUTO_INCREMENT NOT NULL, node_option_id BIGINT NOT NULL, variable_id BIGINT NOT NULL, from_old_version_id BIGINT DEFAULT NULL, public_id VARCHAR(250) NOT NULL, action VARCHAR(250) NOT NULL, value LONGTEXT NOT NULL, sort INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_F62183C9BC7B150C (node_option_id), INDEX IDX_F62183C9F3037E8E (variable_id), INDEX IDX_F62183C999BD42BD (from_old_version_id), UNIQUE INDEX public_id (node_option_id, public_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE node_option_variable_action ADD CONSTRAINT FK_F62183C9BC7B150C FOREIGN KEY (node_option_id) REFERENCES node_option (id)');
        $this->addSql('ALTER TABLE node_option_variable_action ADD CONSTRAINT FK_F62183C9F3037E8E FOREIGN KEY (variable_id) REFERENCES variable (id)');
        $this->addSql('ALTER TABLE node_option_variable_action ADD CONSTRAINT FK_F62183C999BD42BD FOREIGN KEY (from_old_version_id) REFERENCES node_option_variable_action (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE node_option_variable_action DROP FOREIGN KEY FK_F62183C999BD42BD');
        $this->addSql('DROP TABLE node_option_variable_action');
    }
}
