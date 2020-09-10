<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200910083148 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE country (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, alpha2_code VARCHAR(20) DEFAULT NULL, alpha3_code VARCHAR(20) DEFAULT NULL, region VARCHAR(255) DEFAULT NULL, sub_region VARCHAR(255) DEFAULT NULL, native_name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country_article_living_thing (country_id INT NOT NULL, article_living_thing_id INT NOT NULL, INDEX IDX_65596325F92F3E70 (country_id), INDEX IDX_655963258485AF89 (article_living_thing_id), PRIMARY KEY(country_id, article_living_thing_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE country_article_living_thing ADD CONSTRAINT FK_65596325F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE country_article_living_thing ADD CONSTRAINT FK_655963258485AF89 FOREIGN KEY (article_living_thing_id) REFERENCES article_living_thing (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE country_article_living_thing DROP FOREIGN KEY FK_65596325F92F3E70');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE country_article_living_thing');
    }
}
