<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241105111227 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE trip (id INT AUTO_INCREMENT NOT NULL, campus_id INT DEFAULT NULL, state_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, date_and_time DATETIME NOT NULL, duration DOUBLE PRECISION NOT NULL, seats INT NOT NULL, text_note LONGTEXT DEFAULT NULL, registration_deadline DATETIME NOT NULL, INDEX IDX_7656F53BAF5D55E1 (campus_id), INDEX IDX_7656F53B5D83CC1 (state_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53BAF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id)');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53B5D83CC1 FOREIGN KEY (state_id) REFERENCES state (id)');
        $this->addSql('ALTER TABLE place ADD trip_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE place ADD CONSTRAINT FK_741D53CDA5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id)');
        $this->addSql('CREATE INDEX IDX_741D53CDA5BC2E0E ON place (trip_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE place DROP FOREIGN KEY FK_741D53CDA5BC2E0E');
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53BAF5D55E1');
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53B5D83CC1');
        $this->addSql('DROP TABLE trip');
        $this->addSql('DROP INDEX IDX_741D53CDA5BC2E0E ON place');
        $this->addSql('ALTER TABLE place DROP trip_id');
    }
}
