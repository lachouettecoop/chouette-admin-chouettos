<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230624073440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fos_user ADD nb_demi_piaf INT DEFAULT NULL');
        $this->addSql('ALTER TABLE creneau ADD demi_piaf TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE creneau_generique ADD demi_piaf TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fos_user DROP nb_demi_piaf');
        $this->addSql('ALTER TABLE creneau DROP demi_piaf');
        $this->addSql('ALTER TABLE creneau_generique DROP demi_piaf');
    }
}
