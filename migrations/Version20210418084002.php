<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210418084002 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fos_user ADD nb_piaf_effectuees INT DEFAULT NULL, ADD nb_piaf_attendues INT DEFAULT NULL, ADD date_debut_piaf DATE DEFAULT NULL, ADD absence_longue_duree_sans_courses TINYINT(1) DEFAULT NULL, ADD absence_longue_duree_courses TINYINT(1) DEFAULT NULL, CHANGE new_roles new_roles LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fos_user DROP nb_piaf_effectuees, DROP nb_piaf_attendues, DROP date_debut_piaf, DROP absence_longue_duree_sans_courses, DROP absence_longue_duree_courses, CHANGE new_roles new_roles LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_bin`');
    }
}
