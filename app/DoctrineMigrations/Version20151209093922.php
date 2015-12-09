<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151209093922 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE visitor_session_on_node ADD node_option_id BIGINT DEFAULT NULL, ADD gone_back_to TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE visitor_session_on_node ADD CONSTRAINT FK_8ED08171BC7B150C FOREIGN KEY (node_option_id) REFERENCES node_option (id)');
        $this->addSql('CREATE INDEX IDX_8ED08171BC7B150C ON visitor_session_on_node (node_option_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE visitor_session_on_node DROP FOREIGN KEY FK_8ED08171BC7B150C');
        $this->addSql('DROP INDEX IDX_8ED08171BC7B150C ON visitor_session_on_node');
        $this->addSql('ALTER TABLE visitor_session_on_node DROP node_option_id, DROP gone_back_to');
    }
}
