<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250117223523 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE deck (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, pokemon_count INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE deck_pokemon (deck_id INT NOT NULL, pokemon_id VARCHAR(255) NOT NULL, PRIMARY KEY(deck_id, pokemon_id))');
        $this->addSql('CREATE INDEX IDX_E652A58F111948DC ON deck_pokemon (deck_id)');
        $this->addSql('CREATE INDEX IDX_E652A58F2FE71C3E ON deck_pokemon (pokemon_id)');
        $this->addSql('CREATE TABLE pokemon (id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, supertype VARCHAR(255) NOT NULL, subtypes JSON NOT NULL, hp VARCHAR(255) DEFAULT NULL, types JSON NOT NULL, evolves_from VARCHAR(255) DEFAULT NULL, abilities JSON NOT NULL, attacks JSON NOT NULL, weaknesses JSON NOT NULL, retreat_cost JSON NOT NULL, converted_retreat_cost INT DEFAULT NULL, set JSON NOT NULL, number VARCHAR(255) NOT NULL, artist VARCHAR(255) NOT NULL, rarity VARCHAR(255) NOT NULL, flavor_text TEXT DEFAULT NULL, national_pokedex_numbers JSON NOT NULL, legalities JSON NOT NULL, images JSON NOT NULL, tcgplayer JSON NOT NULL, cardmarket JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE deck_pokemon ADD CONSTRAINT FK_E652A58F111948DC FOREIGN KEY (deck_id) REFERENCES deck (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE deck_pokemon ADD CONSTRAINT FK_E652A58F2FE71C3E FOREIGN KEY (pokemon_id) REFERENCES pokemon (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE deck_pokemon DROP CONSTRAINT FK_E652A58F111948DC');
        $this->addSql('ALTER TABLE deck_pokemon DROP CONSTRAINT FK_E652A58F2FE71C3E');
        $this->addSql('DROP TABLE deck');
        $this->addSql('DROP TABLE deck_pokemon');
        $this->addSql('DROP TABLE pokemon');
    }
}
