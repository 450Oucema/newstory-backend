<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200607103615 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE friend_request ADD response_date DATETIME DEFAULT NULL, CHANGE accepted accepted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE piece CHANGE user_id user_id INT DEFAULT NULL, CHANGE image_url image_url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE produit CHANGE piece_id piece_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL, CHANGE prix prix DOUBLE PRECISION DEFAULT NULL, CHANGE liens liens LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', CHANGE date_achat date_achat DATETIME DEFAULT NULL, CHANGE image_url image_url VARCHAR(255) DEFAULT NULL, CHANGE commentaire commentaire VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE profile_picture_url profile_picture_url VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE friend_request DROP response_date, CHANGE accepted accepted TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE piece CHANGE user_id user_id INT DEFAULT NULL, CHANGE image_url image_url VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE produit CHANGE piece_id piece_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL, CHANGE prix prix DOUBLE PRECISION DEFAULT \'NULL\', CHANGE liens liens LONGTEXT CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\', CHANGE date_achat date_achat DATETIME DEFAULT \'NULL\', CHANGE image_url image_url VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE commentaire commentaire VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE profile_picture_url profile_picture_url VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
