<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200521005449 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE produit ADD pieces_id INT DEFAULT NULL, ADD created_at DATETIME NOT NULL, CHANGE prix prix DOUBLE PRECISION DEFAULT NULL, CHANGE liens liens LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC273FB89930 FOREIGN KEY (pieces_id) REFERENCES piece (id)');
        $this->addSql('CREATE INDEX IDX_29A5EC273FB89930 ON produit (pieces_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC273FB89930');
        $this->addSql('DROP INDEX IDX_29A5EC273FB89930 ON produit');
        $this->addSql('ALTER TABLE produit DROP pieces_id, DROP created_at, CHANGE prix prix DOUBLE PRECISION DEFAULT \'NULL\', CHANGE liens liens LONGTEXT CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\'');
    }
}
