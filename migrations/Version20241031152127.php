<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241031152127 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'dangerous migration to delete creneau generique pattern B, C, D';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE poste DROP FOREIGN KEY FK_7C890FAB7A650C82');
        $this->addSql('ALTER TABLE poste ADD CONSTRAINT FK_7C890FAB7A650C82 FOREIGN KEY (creneau_generique_id)  REFERENCES creneau_generique(id) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->addSql('ALTER TABLE creneau DROP FOREIGN KEY FK_F9668B5F7A650C82');
        $this->addSql('ALTER TABLE creneau ADD CONSTRAINT FK_F9668B5F7A650C82 FOREIGN KEY (creneau_generique_id)  REFERENCES creneau_generique(id) ON DELETE SET NULL ON UPDATE CASCADE');
        $this->addSql('delete from creneau_generique where frequence <> 1 AND id NOT IN (272, 248, 246)');
        $this->addSql('UPDATE creneau_generique SET frequence = 3 WHERE id IN (272, 248, 246)');

    }

    public function down(Schema $schema): void
    {

    }
}
