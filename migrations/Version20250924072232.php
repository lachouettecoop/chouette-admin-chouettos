<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250924072232 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, link LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task_creneau_generique (task_id INT NOT NULL, creneau_generique_id INT NOT NULL, INDEX IDX_D53D86498DB60186 (task_id), INDEX IDX_D53D86497A650C82 (creneau_generique_id), PRIMARY KEY(task_id, creneau_generique_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE task_creneau_generique ADD CONSTRAINT FK_D53D86498DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_creneau_generique ADD CONSTRAINT FK_D53D86497A650C82 FOREIGN KEY (creneau_generique_id) REFERENCES creneau_generique (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE creneau DROP FOREIGN KEY FK_F9668B5F7A650C82');
        $this->addSql('ALTER TABLE creneau ADD CONSTRAINT FK_F9668B5F7A650C82 FOREIGN KEY (creneau_generique_id) REFERENCES creneau_generique (id)');
        $this->addSql('ALTER TABLE poste DROP FOREIGN KEY FK_7C890FAB7A650C82');
        $this->addSql('ALTER TABLE poste ADD CONSTRAINT FK_7C890FAB7A650C82 FOREIGN KEY (creneau_generique_id) REFERENCES creneau_generique (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task_creneau_generique DROP FOREIGN KEY FK_D53D86498DB60186');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE task_creneau_generique');
        $this->addSql('ALTER TABLE creneau DROP FOREIGN KEY FK_F9668B5F7A650C82');
        $this->addSql('ALTER TABLE creneau ADD CONSTRAINT FK_F9668B5F7A650C82 FOREIGN KEY (creneau_generique_id) REFERENCES creneau_generique (id) ON UPDATE CASCADE ON DELETE SET NULL');
        $this->addSql('ALTER TABLE poste DROP FOREIGN KEY FK_7C890FAB7A650C82');
        $this->addSql('ALTER TABLE poste ADD CONSTRAINT FK_7C890FAB7A650C82 FOREIGN KEY (creneau_generique_id) REFERENCES creneau_generique (id) ON UPDATE CASCADE ON DELETE CASCADE');
    }
}
