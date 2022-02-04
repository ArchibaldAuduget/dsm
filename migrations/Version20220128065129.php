<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220128065129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist_request ADD admin_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE artist_request ADD CONSTRAINT FK_8CD8D1A8642B8210 FOREIGN KEY (admin_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8CD8D1A8642B8210 ON artist_request (admin_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist_request DROP FOREIGN KEY FK_8CD8D1A8642B8210');
        $this->addSql('DROP INDEX IDX_8CD8D1A8642B8210 ON artist_request');
        $this->addSql('ALTER TABLE artist_request DROP admin_id');
    }
}
