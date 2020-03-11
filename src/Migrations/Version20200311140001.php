<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200311140001 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE asset_images (id INT AUTO_INCREMENT NOT NULL, assets_id INT DEFAULT NULL, image_url VARCHAR(255) NOT NULL, main_image TINYINT(1) NOT NULL, INDEX IDX_8323177E6AF163A (assets_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chat (id INT AUTO_INCREMENT NOT NULL, user1_id INT DEFAULT NULL, user2_id INT DEFAULT NULL, timestamp_sent DATETIME NOT NULL, message LONGTEXT NOT NULL, timestamp_read DATETIME DEFAULT NULL, INDEX IDX_659DF2AA56AE248B (user1_id), INDEX IDX_659DF2AA441B8B65 (user2_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE loan_images (id INT AUTO_INCREMENT NOT NULL, loans_id INT DEFAULT NULL, image_url VARCHAR(255) NOT NULL, comment LONGTEXT DEFAULT NULL, INDEX IDX_1B9C4B179AB85012 (loans_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE loans (id INT AUTO_INCREMENT NOT NULL, users_id INT DEFAULT NULL, assets_id INT DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, date_start DATETIME NOT NULL, date_end DATETIME NOT NULL, status_loan VARCHAR(255) NOT NULL, INDEX IDX_82C24DBC67B3B43D (users_id), INDEX IDX_82C24DBCE6AF163A (assets_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rating_loans (id INT AUTO_INCREMENT NOT NULL, loans_id INT DEFAULT NULL, comment_loaner LONGTEXT DEFAULT NULL, comment_borrower LONGTEXT DEFAULT NULL, rating_of_loaner INT DEFAULT NULL, rating_of_borrower INT DEFAULT NULL, rating_asset INT DEFAULT NULL, UNIQUE INDEX UNIQ_1FD442809AB85012 (loans_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE asset_images ADD CONSTRAINT FK_8323177E6AF163A FOREIGN KEY (assets_id) REFERENCES assets (id)');
        $this->addSql('ALTER TABLE chat ADD CONSTRAINT FK_659DF2AA56AE248B FOREIGN KEY (user1_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE chat ADD CONSTRAINT FK_659DF2AA441B8B65 FOREIGN KEY (user2_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE loan_images ADD CONSTRAINT FK_1B9C4B179AB85012 FOREIGN KEY (loans_id) REFERENCES loans (id)');
        $this->addSql('ALTER TABLE loans ADD CONSTRAINT FK_82C24DBC67B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE loans ADD CONSTRAINT FK_82C24DBCE6AF163A FOREIGN KEY (assets_id) REFERENCES assets (id)');
        $this->addSql('ALTER TABLE rating_loans ADD CONSTRAINT FK_1FD442809AB85012 FOREIGN KEY (loans_id) REFERENCES loans (id)');
        $this->addSql('ALTER TABLE assets CHANGE asset_type_id asset_type_id INT DEFAULT NULL, CHANGE asset_name asset_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE asset_types CHANGE asset_categories_id asset_categories_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_connections CHANGE user1_id user1_id INT DEFAULT NULL, CHANGE user2_id user2_id INT DEFAULT NULL, CHANGE timestamp timestamp DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD auth_code VARCHAR(32) DEFAULT NULL, CHANGE zip_code_id zip_code_id INT DEFAULT NULL, CHANGE middle_name middle_name VARCHAR(255) DEFAULT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE address2 address2 VARCHAR(255) DEFAULT NULL, CHANGE nickname nickname VARCHAR(255) DEFAULT NULL, CHANGE phone phone VARCHAR(255) DEFAULT NULL, CHANGE profile_image profile_image VARCHAR(255) DEFAULT NULL, CHANGE password password VARCHAR(255) DEFAULT NULL, CHANGE active active TINYINT(1) DEFAULT NULL, CHANGE news_subscription news_subscription TINYINT(1) DEFAULT NULL, CHANGE birth_date birth_date DATE DEFAULT NULL, CHANGE usertype usertype VARCHAR(255) DEFAULT NULL, CHANGE userterms userterms TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE loan_images DROP FOREIGN KEY FK_1B9C4B179AB85012');
        $this->addSql('ALTER TABLE rating_loans DROP FOREIGN KEY FK_1FD442809AB85012');
        $this->addSql('DROP TABLE asset_images');
        $this->addSql('DROP TABLE chat');
        $this->addSql('DROP TABLE loan_images');
        $this->addSql('DROP TABLE loans');
        $this->addSql('DROP TABLE rating_loans');
        $this->addSql('ALTER TABLE asset_types CHANGE asset_categories_id asset_categories_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE assets CHANGE asset_type_id asset_type_id INT DEFAULT NULL, CHANGE asset_name asset_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user_connections CHANGE user1_id user1_id INT DEFAULT NULL, CHANGE user2_id user2_id INT DEFAULT NULL, CHANGE timestamp timestamp DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE users DROP auth_code, CHANGE zip_code_id zip_code_id INT DEFAULT NULL, CHANGE middle_name middle_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE address address VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE address2 address2 VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE phone phone VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE nickname nickname VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE birth_date birth_date DATE DEFAULT \'NULL\', CHANGE profile_image profile_image VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE password password VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE usertype usertype VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE active active TINYINT(1) DEFAULT \'NULL\', CHANGE news_subscription news_subscription TINYINT(1) DEFAULT \'NULL\', CHANGE userterms userterms TINYINT(1) DEFAULT \'NULL\'');
    }
}
