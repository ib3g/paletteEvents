<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230606084552 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category ADD icon_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C154B9D732 FOREIGN KEY (icon_id) REFERENCES media (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C154B9D732 ON category (icon_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C154B9D732');
        $this->addSql('DROP INDEX UNIQ_64C19C154B9D732 ON category');
        $this->addSql('ALTER TABLE category DROP icon_id');
    }
}
