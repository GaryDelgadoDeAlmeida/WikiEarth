<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200929190439 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article_living_thing (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, id_living_thing_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, geography LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', ecology LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', behaviour LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', way_of_life LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', description LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', other_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', approved TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_4E095BB4A76ED395 (user_id), UNIQUE INDEX UNIQ_4E095BB48E1E5C9F (id_living_thing_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country_article_living_thing (country_id INT NOT NULL, article_living_thing_id INT NOT NULL, INDEX IDX_65596325F92F3E70 (country_id), INDEX IDX_655963258485AF89 (article_living_thing_id), PRIMARY KEY(country_id, article_living_thing_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE living_thing (id INT AUTO_INCREMENT NOT NULL, common_name VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, kingdom VARCHAR(255) NOT NULL, sub_kingdom VARCHAR(255) DEFAULT NULL, infra_kingdom VARCHAR(255) DEFAULT NULL, domain VARCHAR(255) DEFAULT NULL, super_branch VARCHAR(255) DEFAULT NULL, branch VARCHAR(255) DEFAULT NULL, sub_branch VARCHAR(255) DEFAULT NULL, infra_branch VARCHAR(255) DEFAULT NULL, division VARCHAR(255) DEFAULT NULL, super_class VARCHAR(255) DEFAULT NULL, class VARCHAR(255) DEFAULT NULL, sub_class VARCHAR(255) DEFAULT NULL, infra_class VARCHAR(255) DEFAULT NULL, super_order VARCHAR(255) DEFAULT NULL, normal_order VARCHAR(255) DEFAULT NULL, sub_order VARCHAR(255) DEFAULT NULL, infra_order VARCHAR(255) DEFAULT NULL, micro_order VARCHAR(255) DEFAULT NULL, super_family VARCHAR(255) DEFAULT NULL, family VARCHAR(255) DEFAULT NULL, sub_family VARCHAR(255) DEFAULT NULL, genus VARCHAR(255) DEFAULT NULL, sub_genus VARCHAR(255) DEFAULT NULL, species VARCHAR(255) DEFAULT NULL, sub_species VARCHAR(255) DEFAULT NULL, img_path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media_gallery (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, media_type VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE source_link (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article_living_thing ADD CONSTRAINT FK_4E095BB4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE article_living_thing ADD CONSTRAINT FK_4E095BB48E1E5C9F FOREIGN KEY (id_living_thing_id) REFERENCES living_thing (id)');
        $this->addSql('ALTER TABLE country_article_living_thing ADD CONSTRAINT FK_65596325F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE country_article_living_thing ADD CONSTRAINT FK_655963258485AF89 FOREIGN KEY (article_living_thing_id) REFERENCES article_living_thing (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE country_article_living_thing DROP FOREIGN KEY FK_655963258485AF89');
        $this->addSql('ALTER TABLE article_living_thing DROP FOREIGN KEY FK_4E095BB48E1E5C9F');
        $this->addSql('DROP TABLE article_living_thing');
        $this->addSql('DROP TABLE country_article_living_thing');
        $this->addSql('DROP TABLE living_thing');
        $this->addSql('DROP TABLE media_gallery');
        $this->addSql('DROP TABLE source_link');
    }
}
