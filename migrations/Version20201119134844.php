<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201119134844 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE creneau (id INT AUTO_INCREMENT NOT NULL, creneau_generique_id INT DEFAULT NULL, date DATE DEFAULT NULL, heure_debut TIME DEFAULT NULL, heure_fin TIME DEFAULT NULL, informations LONGTEXT DEFAULT NULL, INDEX IDX_F9668B5F7A650C82 (creneau_generique_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE creneau_generique (id INT AUTO_INCREMENT NOT NULL, frequence VARCHAR(255) DEFAULT NULL, jour VARCHAR(255) DEFAULT NULL, heure_debut TIME DEFAULT NULL, heure_fin TIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE piaf (id INT AUTO_INCREMENT NOT NULL, role_id INT DEFAULT NULL, piaffeur_id INT DEFAULT NULL, creneau_id INT NOT NULL, visible TINYINT(1) DEFAULT NULL, remplacement TINYINT(1) DEFAULT NULL, INDEX IDX_D54A11A4D60322AC (role_id), INDEX IDX_D54A11A43C900CC2 (piaffeur_id), INDEX IDX_D54A11A47D0729A9 (creneau_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE poste (id INT AUTO_INCREMENT NOT NULL, reservation_chouettos_id INT DEFAULT NULL, creneau_generique_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_7C890FABDDB04173 (reservation_chouettos_id), INDEX IDX_7C890FAB7A650C82 (creneau_generique_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE poste_role (poste_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_38B65255A0905086 (poste_id), INDEX IDX_38B65255D60322AC (role_id), PRIMARY KEY(poste_id, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE creneau ADD CONSTRAINT FK_F9668B5F7A650C82 FOREIGN KEY (creneau_generique_id) REFERENCES creneau_generique (id)');
        $this->addSql('ALTER TABLE piaf ADD CONSTRAINT FK_D54A11A4D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('ALTER TABLE piaf ADD CONSTRAINT FK_D54A11A43C900CC2 FOREIGN KEY (piaffeur_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE piaf ADD CONSTRAINT FK_D54A11A47D0729A9 FOREIGN KEY (creneau_id) REFERENCES creneau (id)');
        $this->addSql('ALTER TABLE poste ADD CONSTRAINT FK_7C890FABDDB04173 FOREIGN KEY (reservation_chouettos_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE poste ADD CONSTRAINT FK_7C890FAB7A650C82 FOREIGN KEY (creneau_generique_id) REFERENCES creneau_generique (id)');
        $this->addSql('ALTER TABLE poste_role ADD CONSTRAINT FK_38B65255A0905086 FOREIGN KEY (poste_id) REFERENCES poste (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE poste_role ADD CONSTRAINT FK_38B65255D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE piaf DROP FOREIGN KEY FK_D54A11A47D0729A9');
        $this->addSql('ALTER TABLE creneau DROP FOREIGN KEY FK_F9668B5F7A650C82');
        $this->addSql('ALTER TABLE poste DROP FOREIGN KEY FK_7C890FAB7A650C82');
        $this->addSql('ALTER TABLE poste_role DROP FOREIGN KEY FK_38B65255A0905086');
        $this->addSql('ALTER TABLE piaf DROP FOREIGN KEY FK_D54A11A4D60322AC');
        $this->addSql('ALTER TABLE poste_role DROP FOREIGN KEY FK_38B65255D60322AC');
        $this->addSql('DROP TABLE creneau');
        $this->addSql('DROP TABLE creneau_generique');
        $this->addSql('DROP TABLE piaf');
        $this->addSql('DROP TABLE poste');
        $this->addSql('DROP TABLE poste_role');
        $this->addSql('DROP TABLE role');
    }
}
