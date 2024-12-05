<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241204013543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD permission_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649FED90CCA FOREIGN KEY (permission_id) REFERENCES rujukan (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649FED90CCA ON user (permission_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649FED90CCA');
        $this->addSql('DROP INDEX IDX_8D93D649FED90CCA ON user');
        $this->addSql('ALTER TABLE user DROP permission_id');
    }
}
