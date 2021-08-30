<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210830092733 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE creneau ADD hors_mag TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE creneau_generique ADD hors_mag TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE piaf ADD non_pourvu TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE creneau DROP hors_mag');
        $this->addSql('ALTER TABLE creneau_generique DROP hors_mag');
        $this->addSql('ALTER TABLE piaf DROP non_pourvu');
    }
}
