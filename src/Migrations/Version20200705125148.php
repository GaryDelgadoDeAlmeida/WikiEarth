<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200705125148 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article_living_thing (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, id_living_thing_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, geography LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', ecology LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', bahaviour LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', way_of_life LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', description LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', other_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', approved TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_4E095BB4A76ED395 (user_id), UNIQUE INDEX UNIQ_4E095BB48E1E5C9F (id_living_thing_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE living_thing (id INT AUTO_INCREMENT NOT NULL, common_name VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, kingdom VARCHAR(255) NOT NULL, sub_kingdom VARCHAR(255) DEFAULT NULL, domain VARCHAR(255) DEFAULT NULL, branch VARCHAR(255) DEFAULT NULL, sub_branch VARCHAR(255) DEFAULT NULL, infra_branch VARCHAR(255) DEFAULT NULL, division VARCHAR(255) DEFAULT NULL, super_class VARCHAR(255) DEFAULT NULL, class VARCHAR(255) DEFAULT NULL, sub_class VARCHAR(255) DEFAULT NULL, infra_class VARCHAR(255) DEFAULT NULL, super_order VARCHAR(255) DEFAULT NULL, normal_order VARCHAR(255) DEFAULT NULL, sub_order VARCHAR(255) DEFAULT NULL, infra_order VARCHAR(255) DEFAULT NULL, micro_order VARCHAR(255) DEFAULT NULL, super_family VARCHAR(255) DEFAULT NULL, family VARCHAR(255) DEFAULT NULL, sub_family VARCHAR(255) DEFAULT NULL, genus VARCHAR(255) DEFAULT NULL, sub_genus VARCHAR(255) DEFAULT NULL, species VARCHAR(255) DEFAULT NULL, sub_species VARCHAR(255) DEFAULT NULL, img_path VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media_gallery (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, media_type VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE source_link (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, login VARCHAR(255) NOT NULL, password LONGTEXT NOT NULL, img_path VARCHAR(255) DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article_living_thing ADD CONSTRAINT FK_4E095BB4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE article_living_thing ADD CONSTRAINT FK_4E095BB48E1E5C9F FOREIGN KEY (id_living_thing_id) REFERENCES living_thing (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article_living_thing DROP FOREIGN KEY FK_4E095BB48E1E5C9F');
        $this->addSql('ALTER TABLE article_living_thing DROP FOREIGN KEY FK_4E095BB4A76ED395');
        $this->addSql('DROP TABLE article_living_thing');
        $this->addSql('DROP TABLE living_thing');
        $this->addSql('DROP TABLE media_gallery');
        $this->addSql('DROP TABLE source_link');
        $this->addSql('DROP TABLE user');
    }
}
