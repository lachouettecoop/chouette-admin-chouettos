<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210223184241 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE creneau ADD debut DATETIME DEFAULT NULL, ADD fin DATETIME DEFAULT NULL, DROP date, DROP heure_debut, DROP heure_fin');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE creneau ADD date DATE DEFAULT NULL, ADD heure_debut TIME DEFAULT NULL, ADD heure_fin TIME DEFAULT NULL, DROP debut, DROP fin');
    }
}
