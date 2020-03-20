<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200320170621 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE request_status (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE admin_sent_mails CHANGE user_id user_id INT DEFAULT NULL, CHANGE admin_mail_id admin_mail_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE asset_images CHANGE assets_id assets_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE assets CHANGE asset_type_id asset_type_id INT DEFAULT NULL, CHANGE asset_name asset_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE asset_types CHANGE asset_categories_id asset_categories_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE chat CHANGE user1_id user1_id INT DEFAULT NULL, CHANGE user2_id user2_id INT DEFAULT NULL, CHANGE timestamp_read timestamp_read DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE loan_images CHANGE loans_id loans_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE loans ADD status_loan_id INT DEFAULT NULL, DROP status_loan, CHANGE users_id users_id INT DEFAULT NULL, CHANGE assets_id assets_id INT DEFAULT NULL, CHANGE comment comment VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE loans ADD CONSTRAINT FK_82C24DBCC6A3F10A FOREIGN KEY (status_loan_id) REFERENCES request_status (id)');
        $this->addSql('CREATE INDEX IDX_82C24DBCC6A3F10A ON loans (status_loan_id)');
        $this->addSql('ALTER TABLE loggin_logs CHANGE users_id users_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rating_loans CHANGE loans_id loans_id INT DEFAULT NULL, CHANGE rating_of_loaner rating_of_loaner INT DEFAULT NULL, CHANGE rating_of_borrower rating_of_borrower INT DEFAULT NULL, CHANGE rating_asset rating_asset INT DEFAULT NULL');
        $this->addSql('ALTER TABLE unwanted_behavior_reports CHANGE reporter_id reporter_id INT DEFAULT NULL, CHANGE reported_id reported_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_connections CHANGE user1_id user1_id INT DEFAULT NULL, CHANGE user2_id user2_id INT DEFAULT NULL, CHANGE timestamp timestamp DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user_has_user_rights CHANGE user_id user_id INT DEFAULT NULL, CHANGE user_rights_id user_rights_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_rights CHANGE user_right user_right VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE zip_code_id zip_code_id INT DEFAULT NULL, CHANGE middle_name middle_name VARCHAR(255) DEFAULT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE address2 address2 VARCHAR(255) DEFAULT NULL, CHANGE nickname nickname VARCHAR(255) DEFAULT NULL, CHANGE phone phone VARCHAR(255) DEFAULT NULL, CHANGE profile_image profile_image VARCHAR(255) DEFAULT NULL, CHANGE password password VARCHAR(255) DEFAULT NULL, CHANGE active active TINYINT(1) DEFAULT NULL, CHANGE news_subscription news_subscription TINYINT(1) DEFAULT NULL, CHANGE birth_date birth_date DATE DEFAULT NULL, CHANGE usertype usertype VARCHAR(255) DEFAULT NULL, CHANGE userterms userterms TINYINT(1) DEFAULT NULL, CHANGE auth_code auth_code VARCHAR(32) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE loans DROP FOREIGN KEY FK_82C24DBCC6A3F10A');
        $this->addSql('DROP TABLE request_status');
        $this->addSql('ALTER TABLE admin_sent_mails CHANGE user_id user_id INT DEFAULT NULL, CHANGE admin_mail_id admin_mail_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE asset_images CHANGE assets_id assets_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE asset_types CHANGE asset_categories_id asset_categories_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE assets CHANGE asset_type_id asset_type_id INT DEFAULT NULL, CHANGE asset_name asset_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE chat CHANGE user1_id user1_id INT DEFAULT NULL, CHANGE user2_id user2_id INT DEFAULT NULL, CHANGE timestamp_read timestamp_read DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE loan_images CHANGE loans_id loans_id INT DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_82C24DBCC6A3F10A ON loans');
        $this->addSql('ALTER TABLE loans ADD status_loan VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP status_loan_id, CHANGE users_id users_id INT DEFAULT NULL, CHANGE assets_id assets_id INT DEFAULT NULL, CHANGE comment comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE loggin_logs CHANGE users_id users_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rating_loans CHANGE loans_id loans_id INT DEFAULT NULL, CHANGE rating_of_loaner rating_of_loaner INT DEFAULT NULL, CHANGE rating_of_borrower rating_of_borrower INT DEFAULT NULL, CHANGE rating_asset rating_asset INT DEFAULT NULL');
        $this->addSql('ALTER TABLE unwanted_behavior_reports CHANGE reporter_id reporter_id INT DEFAULT NULL, CHANGE reported_id reported_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_connections CHANGE user1_id user1_id INT DEFAULT NULL, CHANGE user2_id user2_id INT DEFAULT NULL, CHANGE timestamp timestamp DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user_has_user_rights CHANGE user_id user_id INT DEFAULT NULL, CHANGE user_rights_id user_rights_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_rights CHANGE user_right user_right VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE users CHANGE zip_code_id zip_code_id INT DEFAULT NULL, CHANGE middle_name middle_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE address address VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE address2 address2 VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE phone phone VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE nickname nickname VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE birth_date birth_date DATE DEFAULT \'NULL\', CHANGE profile_image profile_image VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE password password VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE usertype usertype VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE active active TINYINT(1) DEFAULT \'NULL\', CHANGE news_subscription news_subscription TINYINT(1) DEFAULT \'NULL\', CHANGE userterms userterms TINYINT(1) DEFAULT \'NULL\', CHANGE auth_code auth_code VARCHAR(32) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
