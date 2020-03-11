<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200311165111 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE admin_mails (id INT AUTO_INCREMENT NOT NULL, mailcode VARCHAR(255) NOT NULL, header VARCHAR(255) NOT NULL, body LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE admin_sent_mails (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, admin_mail_id INT DEFAULT NULL, timestamp DATETIME NOT NULL, mail_status VARCHAR(255) NOT NULL, INDEX IDX_A5DA1F08A76ED395 (user_id), INDEX IDX_A5DA1F08C4BED88 (admin_mail_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE illegal_words (id INT AUTO_INCREMENT NOT NULL, word VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE loggin_logs (id INT AUTO_INCREMENT NOT NULL, users_id INT DEFAULT NULL, timestamp DATETIME NOT NULL, ip_address VARCHAR(45) NOT NULL, device_type VARCHAR(255) NOT NULL, INDEX IDX_B4D1896767B3B43D (users_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unwanted_behavior_reports (id INT AUTO_INCREMENT NOT NULL, reporter_id INT DEFAULT NULL, reported_id INT DEFAULT NULL, timestamp DATETIME NOT NULL, comment LONGTEXT NOT NULL, subject VARCHAR(255) NOT NULL, INDEX IDX_2268E1F4E1CFE6F5 (reporter_id), INDEX IDX_2268E1F494BDEEB6 (reported_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_has_user_rights (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, user_rights_id INT DEFAULT NULL, INDEX IDX_1F0E26B6A76ED395 (user_id), INDEX IDX_1F0E26B6B176638A (user_rights_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_rights (id INT AUTO_INCREMENT NOT NULL, user_right VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE admin_sent_mails ADD CONSTRAINT FK_A5DA1F08A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE admin_sent_mails ADD CONSTRAINT FK_A5DA1F08C4BED88 FOREIGN KEY (admin_mail_id) REFERENCES admin_mails (id)');
        $this->addSql('ALTER TABLE loggin_logs ADD CONSTRAINT FK_B4D1896767B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE unwanted_behavior_reports ADD CONSTRAINT FK_2268E1F4E1CFE6F5 FOREIGN KEY (reporter_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE unwanted_behavior_reports ADD CONSTRAINT FK_2268E1F494BDEEB6 FOREIGN KEY (reported_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_has_user_rights ADD CONSTRAINT FK_1F0E26B6A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_has_user_rights ADD CONSTRAINT FK_1F0E26B6B176638A FOREIGN KEY (user_rights_id) REFERENCES user_rights (id)');
        $this->addSql('ALTER TABLE asset_images CHANGE assets_id assets_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE assets CHANGE asset_type_id asset_type_id INT DEFAULT NULL, CHANGE asset_name asset_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE asset_types CHANGE asset_categories_id asset_categories_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE chat CHANGE user1_id user1_id INT DEFAULT NULL, CHANGE user2_id user2_id INT DEFAULT NULL, CHANGE timestamp_read timestamp_read DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE loan_images CHANGE loans_id loans_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE loans CHANGE users_id users_id INT DEFAULT NULL, CHANGE assets_id assets_id INT DEFAULT NULL, CHANGE comment comment VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE rating_loans CHANGE loans_id loans_id INT DEFAULT NULL, CHANGE rating_of_loaner rating_of_loaner INT DEFAULT NULL, CHANGE rating_of_borrower rating_of_borrower INT DEFAULT NULL, CHANGE rating_asset rating_asset INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_connections CHANGE user1_id user1_id INT DEFAULT NULL, CHANGE user2_id user2_id INT DEFAULT NULL, CHANGE timestamp timestamp DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE zip_code_id zip_code_id INT DEFAULT NULL, CHANGE middle_name middle_name VARCHAR(255) DEFAULT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE address2 address2 VARCHAR(255) DEFAULT NULL, CHANGE nickname nickname VARCHAR(255) DEFAULT NULL, CHANGE phone phone VARCHAR(255) DEFAULT NULL, CHANGE profile_image profile_image VARCHAR(255) DEFAULT NULL, CHANGE password password VARCHAR(255) DEFAULT NULL, CHANGE active active TINYINT(1) DEFAULT NULL, CHANGE news_subscription news_subscription TINYINT(1) DEFAULT NULL, CHANGE birth_date birth_date DATE DEFAULT NULL, CHANGE usertype usertype VARCHAR(255) DEFAULT NULL, CHANGE userterms userterms TINYINT(1) DEFAULT NULL, CHANGE auth_code auth_code VARCHAR(32) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE admin_sent_mails DROP FOREIGN KEY FK_A5DA1F08C4BED88');
        $this->addSql('ALTER TABLE user_has_user_rights DROP FOREIGN KEY FK_1F0E26B6B176638A');
        $this->addSql('DROP TABLE admin_mails');
        $this->addSql('DROP TABLE admin_sent_mails');
        $this->addSql('DROP TABLE illegal_words');
        $this->addSql('DROP TABLE loggin_logs');
        $this->addSql('DROP TABLE unwanted_behavior_reports');
        $this->addSql('DROP TABLE user_has_user_rights');
        $this->addSql('DROP TABLE user_rights');
        $this->addSql('ALTER TABLE asset_images CHANGE assets_id assets_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE asset_types CHANGE asset_categories_id asset_categories_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE assets CHANGE asset_type_id asset_type_id INT DEFAULT NULL, CHANGE asset_name asset_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE chat CHANGE user1_id user1_id INT DEFAULT NULL, CHANGE user2_id user2_id INT DEFAULT NULL, CHANGE timestamp_read timestamp_read DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE loan_images CHANGE loans_id loans_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE loans CHANGE users_id users_id INT DEFAULT NULL, CHANGE assets_id assets_id INT DEFAULT NULL, CHANGE comment comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE rating_loans CHANGE loans_id loans_id INT DEFAULT NULL, CHANGE rating_of_loaner rating_of_loaner INT DEFAULT NULL, CHANGE rating_of_borrower rating_of_borrower INT DEFAULT NULL, CHANGE rating_asset rating_asset INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_connections CHANGE user1_id user1_id INT DEFAULT NULL, CHANGE user2_id user2_id INT DEFAULT NULL, CHANGE timestamp timestamp DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE users CHANGE zip_code_id zip_code_id INT DEFAULT NULL, CHANGE middle_name middle_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE address address VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE address2 address2 VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE phone phone VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE nickname nickname VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE birth_date birth_date DATE DEFAULT \'NULL\', CHANGE profile_image profile_image VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE password password VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE usertype usertype VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE active active TINYINT(1) DEFAULT \'NULL\', CHANGE news_subscription news_subscription TINYINT(1) DEFAULT \'NULL\', CHANGE userterms userterms TINYINT(1) DEFAULT \'NULL\', CHANGE auth_code auth_code VARCHAR(32) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
