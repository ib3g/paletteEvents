<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230407154255 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande ADD text VARCHAR(255) NOT NULL, ADD created_at DATETIME NOT NULL, ADD status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE event ADD date_event DATETIME NOT NULL, ADD lieu VARCHAR(255) NOT NULL, ADD sponsors VARCHAR(255) DEFAULT NULL, ADD status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE facture ADD status VARCHAR(255) NOT NULL, ADD created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE media ADD real_name VARCHAR(255) NOT NULL, ADD extension VARCHAR(10) DEFAULT NULL, ADD mime_type VARCHAR(150) DEFAULT NULL, ADD size NUMERIC(20, 2) DEFAULT NULL, ADD path VARCHAR(300) DEFAULT NULL, DROP name, DROP url, DROP type');
        $this->addSql('ALTER TABLE prix ADD place_max INT NOT NULL, ADD place_restantes INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket ADD place INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande DROP text, DROP created_at, DROP status');
        $this->addSql('ALTER TABLE event DROP date_event, DROP lieu, DROP sponsors, DROP status');
        $this->addSql('ALTER TABLE facture DROP status, DROP created_at');
        $this->addSql('ALTER TABLE media ADD url VARCHAR(255) NOT NULL, ADD type VARCHAR(255) NOT NULL, DROP extension, DROP mime_type, DROP size, DROP path, CHANGE real_name name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE prix DROP place_max, DROP place_restantes');
        $this->addSql('ALTER TABLE ticket DROP place');
    }
}
