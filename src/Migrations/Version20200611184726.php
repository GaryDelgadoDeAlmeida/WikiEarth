<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200611184726 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE animal (id INT AUTO_INCREMENT NOT NULL, common_name VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, kingdom VARCHAR(255) NOT NULL, sub_kingdom VARCHAR(255) DEFAULT NULL, domain VARCHAR(255) DEFAULT NULL, branch VARCHAR(255) DEFAULT NULL, sub_branch VARCHAR(255) DEFAULT NULL, infra_branch VARCHAR(255) DEFAULT NULL, division VARCHAR(255) DEFAULT NULL, super_class VARCHAR(255) DEFAULT NULL, class VARCHAR(255) DEFAULT NULL, sub_class VARCHAR(255) DEFAULT NULL, infra_class VARCHAR(255) DEFAULT NULL, super_order VARCHAR(255) DEFAULT NULL, normal_order VARCHAR(255) DEFAULT NULL, sub_order VARCHAR(255) DEFAULT NULL, infra_order VARCHAR(255) DEFAULT NULL, micro_order VARCHAR(255) DEFAULT NULL, super_family VARCHAR(255) DEFAULT NULL, family VARCHAR(255) DEFAULT NULL, sub_family VARCHAR(255) DEFAULT NULL, genus VARCHAR(255) DEFAULT NULL, sub_genus VARCHAR(255) DEFAULT NULL, species VARCHAR(255) DEFAULT NULL, sub_species VARCHAR(255) DEFAULT NULL, animal_type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE archive (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, id_template INT NOT NULL, concerned_table VARCHAR(255) NOT NULL, INDEX IDX_D5FC5D9C7294869C (article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, id_user_id INT DEFAULT NULL, INDEX IDX_23A0E6679F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE element (id INT AUTO_INCREMENT NOT NULL, symbole VARCHAR(5) NOT NULL, name VARCHAR(255) NOT NULL, family VARCHAR(255) NOT NULL, electronic_configuration VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media_gallery (id INT AUTO_INCREMENT NOT NULL, archive_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, media_type VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_26FCFE732956195F (archive_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE archive ADD CONSTRAINT FK_D5FC5D9C7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E6679F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE media_gallery ADD CONSTRAINT FK_26FCFE732956195F FOREIGN KEY (archive_id) REFERENCES archive (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE media_gallery DROP FOREIGN KEY FK_26FCFE732956195F');
        $this->addSql('ALTER TABLE archive DROP FOREIGN KEY FK_D5FC5D9C7294869C');
        $this->addSql('DROP TABLE animal');
        $this->addSql('DROP TABLE archive');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE element');
        $this->addSql('DROP TABLE media_gallery');
    }
}
