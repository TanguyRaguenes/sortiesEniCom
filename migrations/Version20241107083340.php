<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241107083340 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE trip_participant (trip_id INT NOT NULL, participant_id INT NOT NULL, INDEX IDX_23BECC9BA5BC2E0E (trip_id), INDEX IDX_23BECC9B9D1C3019 (participant_id), PRIMARY KEY(trip_id, participant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE trip_participant ADD CONSTRAINT FK_23BECC9BA5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trip_participant ADD CONSTRAINT FK_23BECC9B9D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trip ADD organizer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53B876C4DDA FOREIGN KEY (organizer_id) REFERENCES participant (id)');
        $this->addSql('CREATE INDEX IDX_7656F53B876C4DDA ON trip (organizer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trip_participant DROP FOREIGN KEY FK_23BECC9BA5BC2E0E');
        $this->addSql('ALTER TABLE trip_participant DROP FOREIGN KEY FK_23BECC9B9D1C3019');
        $this->addSql('DROP TABLE trip_participant');
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53B876C4DDA');
        $this->addSql('DROP INDEX IDX_7656F53B876C4DDA ON trip');
        $this->addSql('ALTER TABLE trip DROP organizer_id');
    }
}
