<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201119142352 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE poste_role');
        $this->addSql('ALTER TABLE poste ADD role_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE poste ADD CONSTRAINT FK_7C890FABD60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('CREATE INDEX IDX_7C890FABD60322AC ON poste (role_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE poste_role (poste_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_38B65255A0905086 (poste_id), INDEX IDX_38B65255D60322AC (role_id), PRIMARY KEY(poste_id, role_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE poste_role ADD CONSTRAINT FK_38B65255A0905086 FOREIGN KEY (poste_id) REFERENCES poste (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE poste_role ADD CONSTRAINT FK_38B65255D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE poste DROP FOREIGN KEY FK_7C890FABD60322AC');
        $this->addSql('DROP INDEX IDX_7C890FABD60322AC ON poste');
        $this->addSql('ALTER TABLE poste DROP role_id');
    }
}
