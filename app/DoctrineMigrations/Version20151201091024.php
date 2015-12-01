<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151201091024 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE node (id BIGINT AUTO_INCREMENT NOT NULL, tree_version_id BIGINT NOT NULL, from_old_version_id BIGINT DEFAULT NULL, public_id VARCHAR(250) NOT NULL, title LONGTEXT DEFAULT NULL, body_text LONGTEXT DEFAULT NULL, body_html LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_857FE845B0AFDF04 (tree_version_id), INDEX IDX_857FE84599BD42BD (from_old_version_id), UNIQUE INDEX public_id (tree_version_id, public_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE visitor_session (id BIGINT AUTO_INCREMENT NOT NULL, public_id VARCHAR(250) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_1257451CB5B48B91 (public_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE visitor_session_on_node (id BIGINT AUTO_INCREMENT NOT NULL, node_id BIGINT NOT NULL, session_ran_tree_version_id BIGINT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_8ED08171460D9FD7 (node_id), INDEX IDX_8ED08171FAD45745 (session_ran_tree_version_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tree_version_published (id BIGINT AUTO_INCREMENT NOT NULL, tree_version_id BIGINT NOT NULL, published_by_id INT DEFAULT NULL, published_at DATETIME NOT NULL, comment_published_admin LONGTEXT DEFAULT NULL, INDEX IDX_4F1AFE4DB0AFDF04 (tree_version_id), INDEX IDX_4F1AFE4D5B075477 (published_by_id), UNIQUE INDEX version_published_at (tree_version_id, published_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE node_option (id BIGINT AUTO_INCREMENT NOT NULL, node_id BIGINT NOT NULL, destination_node_id BIGINT DEFAULT NULL, from_old_version_id BIGINT DEFAULT NULL, public_id VARCHAR(250) NOT NULL, title LONGTEXT DEFAULT NULL, body_text LONGTEXT DEFAULT NULL, body_html LONGTEXT DEFAULT NULL, sort INT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_EE941515460D9FD7 (node_id), INDEX IDX_EE9415152834A0AF (destination_node_id), INDEX IDX_EE94151599BD42BD (from_old_version_id), UNIQUE INDEX public_id (node_id, public_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fos_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_957A647992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_957A6479A0D96FBF (email_canonical), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tree (id BIGINT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, public_id VARCHAR(250) NOT NULL, title_admin VARCHAR(250) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_B73E5EDCB5B48B91 (public_id), INDEX IDX_B73E5EDC7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tree_version (id BIGINT AUTO_INCREMENT NOT NULL, tree_id BIGINT NOT NULL, from_old_version_id BIGINT DEFAULT NULL, public_id VARCHAR(250) NOT NULL, title_admin VARCHAR(250) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_759B71DE78B64A2 (tree_id), INDEX IDX_759B71DE99BD42BD (from_old_version_id), UNIQUE INDEX public_id (tree_id, public_id), UNIQUE INDEX title_admin (tree_id, title_admin), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE visitor_session_ran_tree_version (id BIGINT AUTO_INCREMENT NOT NULL, visitor_session_id BIGINT NOT NULL, tree_version_id BIGINT NOT NULL, public_id VARCHAR(250) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_C5A170E5B7CE021 (visitor_session_id), INDEX IDX_C5A170E5B0AFDF04 (tree_version_id), UNIQUE INDEX public_id (visitor_session_id, public_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tree_version_starting_node (tree_version_id BIGINT NOT NULL, node_id BIGINT NOT NULL, INDEX IDX_CBE2F23D460D9FD7 (node_id), PRIMARY KEY(tree_version_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE node ADD CONSTRAINT FK_857FE845B0AFDF04 FOREIGN KEY (tree_version_id) REFERENCES tree_version (id)');
        $this->addSql('ALTER TABLE node ADD CONSTRAINT FK_857FE84599BD42BD FOREIGN KEY (from_old_version_id) REFERENCES node (id)');
        $this->addSql('ALTER TABLE visitor_session_on_node ADD CONSTRAINT FK_8ED08171460D9FD7 FOREIGN KEY (node_id) REFERENCES node (id)');
        $this->addSql('ALTER TABLE visitor_session_on_node ADD CONSTRAINT FK_8ED08171FAD45745 FOREIGN KEY (session_ran_tree_version_id) REFERENCES visitor_session_ran_tree_version (id)');
        $this->addSql('ALTER TABLE tree_version_published ADD CONSTRAINT FK_4F1AFE4DB0AFDF04 FOREIGN KEY (tree_version_id) REFERENCES tree_version (id)');
        $this->addSql('ALTER TABLE tree_version_published ADD CONSTRAINT FK_4F1AFE4D5B075477 FOREIGN KEY (published_by_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE node_option ADD CONSTRAINT FK_EE941515460D9FD7 FOREIGN KEY (node_id) REFERENCES node (id)');
        $this->addSql('ALTER TABLE node_option ADD CONSTRAINT FK_EE9415152834A0AF FOREIGN KEY (destination_node_id) REFERENCES node (id)');
        $this->addSql('ALTER TABLE node_option ADD CONSTRAINT FK_EE94151599BD42BD FOREIGN KEY (from_old_version_id) REFERENCES node_option (id)');
        $this->addSql('ALTER TABLE tree ADD CONSTRAINT FK_B73E5EDC7E3C61F9 FOREIGN KEY (owner_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE tree_version ADD CONSTRAINT FK_759B71DE78B64A2 FOREIGN KEY (tree_id) REFERENCES tree (id)');
        $this->addSql('ALTER TABLE tree_version ADD CONSTRAINT FK_759B71DE99BD42BD FOREIGN KEY (from_old_version_id) REFERENCES tree_version (id)');
        $this->addSql('ALTER TABLE visitor_session_ran_tree_version ADD CONSTRAINT FK_C5A170E5B7CE021 FOREIGN KEY (visitor_session_id) REFERENCES visitor_session (id)');
        $this->addSql('ALTER TABLE visitor_session_ran_tree_version ADD CONSTRAINT FK_C5A170E5B0AFDF04 FOREIGN KEY (tree_version_id) REFERENCES tree_version (id)');
        $this->addSql('ALTER TABLE tree_version_starting_node ADD CONSTRAINT FK_CBE2F23DB0AFDF04 FOREIGN KEY (tree_version_id) REFERENCES tree_version (id)');
        $this->addSql('ALTER TABLE tree_version_starting_node ADD CONSTRAINT FK_CBE2F23D460D9FD7 FOREIGN KEY (node_id) REFERENCES node (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE node DROP FOREIGN KEY FK_857FE84599BD42BD');
        $this->addSql('ALTER TABLE visitor_session_on_node DROP FOREIGN KEY FK_8ED08171460D9FD7');
        $this->addSql('ALTER TABLE node_option DROP FOREIGN KEY FK_EE941515460D9FD7');
        $this->addSql('ALTER TABLE node_option DROP FOREIGN KEY FK_EE9415152834A0AF');
        $this->addSql('ALTER TABLE tree_version_starting_node DROP FOREIGN KEY FK_CBE2F23D460D9FD7');
        $this->addSql('ALTER TABLE visitor_session_ran_tree_version DROP FOREIGN KEY FK_C5A170E5B7CE021');
        $this->addSql('ALTER TABLE node_option DROP FOREIGN KEY FK_EE94151599BD42BD');
        $this->addSql('ALTER TABLE tree_version_published DROP FOREIGN KEY FK_4F1AFE4D5B075477');
        $this->addSql('ALTER TABLE tree DROP FOREIGN KEY FK_B73E5EDC7E3C61F9');
        $this->addSql('ALTER TABLE tree_version DROP FOREIGN KEY FK_759B71DE78B64A2');
        $this->addSql('ALTER TABLE node DROP FOREIGN KEY FK_857FE845B0AFDF04');
        $this->addSql('ALTER TABLE tree_version_published DROP FOREIGN KEY FK_4F1AFE4DB0AFDF04');
        $this->addSql('ALTER TABLE tree_version DROP FOREIGN KEY FK_759B71DE99BD42BD');
        $this->addSql('ALTER TABLE visitor_session_ran_tree_version DROP FOREIGN KEY FK_C5A170E5B0AFDF04');
        $this->addSql('ALTER TABLE tree_version_starting_node DROP FOREIGN KEY FK_CBE2F23DB0AFDF04');
        $this->addSql('ALTER TABLE visitor_session_on_node DROP FOREIGN KEY FK_8ED08171FAD45745');
        $this->addSql('DROP TABLE node');
        $this->addSql('DROP TABLE visitor_session');
        $this->addSql('DROP TABLE visitor_session_on_node');
        $this->addSql('DROP TABLE tree_version_published');
        $this->addSql('DROP TABLE node_option');
        $this->addSql('DROP TABLE fos_user');
        $this->addSql('DROP TABLE tree');
        $this->addSql('DROP TABLE tree_version');
        $this->addSql('DROP TABLE visitor_session_ran_tree_version');
        $this->addSql('DROP TABLE tree_version_starting_node');
    }
}
