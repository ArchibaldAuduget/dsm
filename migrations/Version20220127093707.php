<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220127093707 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist_request DROP FOREIGN KEY FK_8CD8D1A89D86650F');
        $this->addSql('DROP INDEX UNIQ_8CD8D1A89D86650F ON artist_request');
        $this->addSql('ALTER TABLE artist_request CHANGE user_id_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE artist_request ADD CONSTRAINT FK_8CD8D1A8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8CD8D1A8A76ED395 ON artist_request (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist_request DROP FOREIGN KEY FK_8CD8D1A8A76ED395');
        $this->addSql('DROP INDEX UNIQ_8CD8D1A8A76ED395 ON artist_request');
        $this->addSql('ALTER TABLE artist_request CHANGE user_id user_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE artist_request ADD CONSTRAINT FK_8CD8D1A89D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8CD8D1A89D86650F ON artist_request (user_id_id)');
    }
}
