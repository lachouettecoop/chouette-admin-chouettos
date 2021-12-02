<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211202115350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fos_user ADD affichage_donnees_personnelles TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE piaf ADD description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE role ADD formation_requise TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fos_user DROP affichage_donnees_personnelles');
        $this->addSql('ALTER TABLE piaf DROP description');
        $this->addSql('ALTER TABLE role DROP formation_requise');
    }
}
