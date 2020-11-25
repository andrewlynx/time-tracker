<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201125091902 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE task ADD user INT NOT NULL, CHANGE time_spent time_spent NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB258D93D649 FOREIGN KEY (user) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_527EDB258D93D649 ON task (user)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB258D93D649');
        $this->addSql('DROP INDEX IDX_527EDB258D93D649 ON task');
        $this->addSql('ALTER TABLE task DROP user, CHANGE time_spent time_spent INT NOT NULL');
    }
}
