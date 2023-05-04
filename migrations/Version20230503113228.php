<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230503113228 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD stripe_event_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE prix ADD stripe_price_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD stripe_customer_id VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP stripe_event_id');
        $this->addSql('ALTER TABLE prix DROP stripe_price_id');
        $this->addSql('ALTER TABLE user DROP stripe_customer_id');
    }
}
