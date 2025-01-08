<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250108135243 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pokemon (id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, supertype VARCHAR(255) NOT NULL, subtypes JSON NOT NULL, hp VARCHAR(255) DEFAULT NULL, types JSON NOT NULL, evolves_from VARCHAR(255) DEFAULT NULL, abilities JSON NOT NULL, attacks JSON NOT NULL, weaknesses JSON NOT NULL, retreat_cost JSON NOT NULL, converted_retreat_cost INT DEFAULT NULL, set JSON NOT NULL, number VARCHAR(255) NOT NULL, artist VARCHAR(255) NOT NULL, rarity VARCHAR(255) NOT NULL, flavor_text TEXT DEFAULT NULL, national_pokedex_numbers JSON NOT NULL, legalities JSON NOT NULL, images JSON NOT NULL, tcgplayer JSON NOT NULL, cardmarket JSON NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE pokemon');
    }
}
