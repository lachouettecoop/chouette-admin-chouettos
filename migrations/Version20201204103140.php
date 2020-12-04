<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201204103140 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reserve (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, informations LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_1FE0EA22A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reserve_creneau_generique (reserve_id INT NOT NULL, creneau_generique_id INT NOT NULL, INDEX IDX_26EAC0595913AEBF (reserve_id), INDEX IDX_26EAC0597A650C82 (creneau_generique_id), PRIMARY KEY(reserve_id, creneau_generique_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE statut (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, libelle VARCHAR(255) DEFAULT NULL, actif TINYINT(1) DEFAULT NULL, date_debut DATE DEFAULT NULL, date_fin DATE DEFAULT NULL, INDEX IDX_E564F0BFA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reserve ADD CONSTRAINT FK_1FE0EA22A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE reserve_creneau_generique ADD CONSTRAINT FK_26EAC0595913AEBF FOREIGN KEY (reserve_id) REFERENCES reserve (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reserve_creneau_generique ADD CONSTRAINT FK_26EAC0597A650C82 FOREIGN KEY (creneau_generique_id) REFERENCES creneau_generique (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE statut ADD CONSTRAINT FK_E564F0BFA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reserve_creneau_generique DROP FOREIGN KEY FK_26EAC0595913AEBF');
        $this->addSql('DROP TABLE reserve');
        $this->addSql('DROP TABLE reserve_creneau_generique');
        $this->addSql('DROP TABLE statut');
    }
}
