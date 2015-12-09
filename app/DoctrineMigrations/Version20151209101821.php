<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151209101821 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX public_id ON node_option');
        $this->addSql('ALTER TABLE node_option ADD tree_version_id BIGINT NULL');
        $this->addSql('ALTER TABLE node_option ADD CONSTRAINT FK_EE941515B0AFDF04 FOREIGN KEY (tree_version_id) REFERENCES tree_version (id)');

        $this->addSql('UPDATE node_option, node SET node_option.tree_version_id = node.tree_version_id WHERE node_option.node_id = node.id ');

        $this->addSql('ALTER TABLE node_option MODIFY tree_version_id BIGINT NOT NULL');

        $this->addSql('CREATE INDEX IDX_EE941515B0AFDF04 ON node_option (tree_version_id)');
        $this->addSql('CREATE UNIQUE INDEX public_id ON node_option (tree_version_id, public_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE node_option DROP FOREIGN KEY FK_EE941515B0AFDF04');
        $this->addSql('DROP INDEX IDX_EE941515B0AFDF04 ON node_option');
        $this->addSql('DROP INDEX public_id ON node_option');
        $this->addSql('ALTER TABLE node_option DROP tree_version_id');
        $this->addSql('CREATE UNIQUE INDEX public_id ON node_option (node_id, public_id)');
    }
}
