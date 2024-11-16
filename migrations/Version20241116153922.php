<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241116153922 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sensor_data (id INT AUTO_INCREMENT NOT NULL, sensor_id INT DEFAULT NULL, timestamp INT DEFAULT NULL, client_name VARCHAR(255) DEFAULT NULL, message_counter INT DEFAULT NULL, payload_type VARCHAR(255) DEFAULT NULL, frame_type VARCHAR(255) DEFAULT NULL, temp VARCHAR(255) DEFAULT NULL, battery DOUBLE PRECISION DEFAULT NULL, sensor_position VARCHAR(255) DEFAULT NULL, time_since_last_change INT DEFAULT NULL, flapping VARCHAR(255) DEFAULT NULL, acceleratometer1 INT DEFAULT NULL, acceleratometer2 INT DEFAULT NULL, led_status INT DEFAULT NULL, temp_led_blink INT DEFAULT NULL, keepalive_interval INT DEFAULT NULL, low_temp_thresold INT DEFAULT NULL, high_temp_thresold INT DEFAULT NULL, INDEX IDX_801762CCA247991F (sensor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sensor_data ADD CONSTRAINT FK_801762CCA247991F FOREIGN KEY (sensor_id) REFERENCES sensor (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sensor_data DROP FOREIGN KEY FK_801762CCA247991F');
        $this->addSql('DROP TABLE sensor_data');
    }
}
