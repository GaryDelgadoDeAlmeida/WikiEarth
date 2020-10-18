<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201018150414 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE atome (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, scientific_name VARCHAR(255) NOT NULL, radioisotope VARCHAR(255) NOT NULL, atomic_number INT NOT NULL, symbole VARCHAR(255) NOT NULL, atome_group VARCHAR(255) DEFAULT NULL, atome_period VARCHAR(255) NOT NULL, atome_block VARCHAR(255) NOT NULL, volumic_mass VARCHAR(255) DEFAULT NULL, num_cas VARCHAR(255) NOT NULL, num_ce VARCHAR(255) DEFAULT NULL, atomic_mass NUMERIC(20, 10) NOT NULL, atomic_radius VARCHAR(255) DEFAULT NULL, covalent_radius VARCHAR(255) DEFAULT NULL, van_der_waals_radius VARCHAR(255) DEFAULT NULL, electronique_configuration VARCHAR(255) DEFAULT NULL, oxidation_state VARCHAR(255) DEFAULT NULL, electronegativity NUMERIC(10, 5) DEFAULT NULL, fusion_point VARCHAR(255) DEFAULT NULL, boiling_point VARCHAR(255) DEFAULT NULL, radioactivity TINYINT(1) NOT NULL, img_path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country_living_thing (country_id INT NOT NULL, living_thing_id INT NOT NULL, INDEX IDX_91E6A037F92F3E70 (country_id), INDEX IDX_91E6A03767409F17 (living_thing_id), PRIMARY KEY(country_id, living_thing_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mineral (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, rruff_chemistry VARCHAR(255) NOT NULL, ima_chemistry VARCHAR(255) NOT NULL, chemistry_elements VARCHAR(255) NOT NULL, ima_number VARCHAR(255) DEFAULT NULL, ima_status LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', structural_groupname VARCHAR(255) DEFAULT NULL, crystal_system VARCHAR(255) DEFAULT NULL, valence_elements VARCHAR(255) NOT NULL, img_path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mineral_country (mineral_id INT NOT NULL, country_id INT NOT NULL, INDEX IDX_6FA9882921F4A72C (mineral_id), INDEX IDX_6FA98829F92F3E70 (country_id), PRIMARY KEY(mineral_id, country_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reference (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE country_living_thing ADD CONSTRAINT FK_91E6A037F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE country_living_thing ADD CONSTRAINT FK_91E6A03767409F17 FOREIGN KEY (living_thing_id) REFERENCES living_thing (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mineral_country ADD CONSTRAINT FK_6FA9882921F4A72C FOREIGN KEY (mineral_id) REFERENCES mineral (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mineral_country ADD CONSTRAINT FK_6FA98829F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mineral_country DROP FOREIGN KEY FK_6FA9882921F4A72C');
        $this->addSql('DROP TABLE atome');
        $this->addSql('DROP TABLE country_living_thing');
        $this->addSql('DROP TABLE mineral');
        $this->addSql('DROP TABLE mineral_country');
        $this->addSql('DROP TABLE reference');
    }
}
