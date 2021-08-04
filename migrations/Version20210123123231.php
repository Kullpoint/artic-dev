<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210123123231 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, topic VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_performer (category_id INT NOT NULL, performer_id INT NOT NULL, INDEX IDX_D041D9DB12469DE2 (category_id), INDEX IDX_D041D9DB6C6B33F3 (performer_id), PRIMARY KEY(category_id, performer_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, avatar VARCHAR(180) DEFAULT NULL, country VARCHAR(180) NOT NULL, city VARCHAR(180) NOT NULL, UNIQUE INDEX UNIQ_C7440455A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE complaint (id INT AUTO_INCREMENT NOT NULL, performer_id INT NOT NULL, client_id INT NOT NULL, text VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_5F2732B56C6B33F3 (performer_id), INDEX IDX_5F2732B519EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, performer_id INT NOT NULL, client_id INT NOT NULL, category_id INT NOT NULL, summery_type VARCHAR(255) NOT NULL, theme VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, requirements LONGTEXT DEFAULT NULL, price VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, doc VARCHAR(255) DEFAULT NULL, deadline DATE NOT NULL, INDEX IDX_F52993986C6B33F3 (performer_id), INDEX IDX_F529939819EB6921 (client_id), INDEX IDX_F529939812469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_comment (id INT AUTO_INCREMENT NOT NULL, order_id INT NOT NULL, comment VARCHAR(255) NOT NULL, INDEX IDX_620EFB278D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partner (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, birthday DATE NOT NULL, referral VARCHAR(180) NOT NULL, UNIQUE INDEX UNIQ_312B3E1673079D00 (referral), UNIQUE INDEX UNIQ_312B3E16A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE performer (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, birthday DATE NOT NULL, avatar VARCHAR(180) DEFAULT NULL, balance VARCHAR(180) DEFAULT NULL, pending_balance VARCHAR(180) DEFAULT NULL, country VARCHAR(180) NOT NULL, city VARCHAR(180) NOT NULL, experience VARCHAR(180) NOT NULL, about LONGTEXT DEFAULT NULL, summery_types VARCHAR(180) NOT NULL, rating VARCHAR(10) DEFAULT NULL, UNIQUE INDEX UNIQ_17210BEBA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, performer_id INT NOT NULL, reviewed_order_id INT NOT NULL, mark VARCHAR(255) NOT NULL, text VARCHAR(255) DEFAULT NULL, date DATE DEFAULT NULL, INDEX IDX_794381C619EB6921 (client_id), INDEX IDX_794381C66C6B33F3 (performer_id), UNIQUE INDEX UNIQ_794381C6C385221F (reviewed_order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, receiver_id INT NOT NULL, sender_id INT NOT NULL, order_id INT NOT NULL, amount VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, paid TINYINT(1) NOT NULL, charge_id VARCHAR(255) NOT NULL, INDEX IDX_723705D1CD53EDB6 (receiver_id), INDEX IDX_723705D1F624B39D (sender_id), UNIQUE INDEX UNIQ_723705D18D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, performer_id INT DEFAULT NULL, partner_id INT DEFAULT NULL, facebook_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, phone VARCHAR(10) NOT NULL, last_name VARCHAR(180) NOT NULL, first_name VARCHAR(180) NOT NULL, roles JSON NOT NULL, is_verified TINYINT(1) NOT NULL, is_blocked TINYINT(1) NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649444F97DD (phone), UNIQUE INDEX UNIQ_8D93D64919EB6921 (client_id), UNIQUE INDEX UNIQ_8D93D6496C6B33F3 (performer_id), UNIQUE INDEX UNIQ_8D93D6499393F8FE (partner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category_performer ADD CONSTRAINT FK_D041D9DB12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_performer ADD CONSTRAINT FK_D041D9DB6C6B33F3 FOREIGN KEY (performer_id) REFERENCES performer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE complaint ADD CONSTRAINT FK_5F2732B56C6B33F3 FOREIGN KEY (performer_id) REFERENCES performer (id)');
        $this->addSql('ALTER TABLE complaint ADD CONSTRAINT FK_5F2732B519EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993986C6B33F3 FOREIGN KEY (performer_id) REFERENCES performer (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939819EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939812469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE order_comment ADD CONSTRAINT FK_620EFB278D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE partner ADD CONSTRAINT FK_312B3E16A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE performer ADD CONSTRAINT FK_17210BEBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C619EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C66C6B33F3 FOREIGN KEY (performer_id) REFERENCES performer (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6C385221F FOREIGN KEY (reviewed_order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES performer (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1F624B39D FOREIGN KEY (sender_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D18D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64919EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6496C6B33F3 FOREIGN KEY (performer_id) REFERENCES performer (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6499393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category_performer DROP FOREIGN KEY FK_D041D9DB12469DE2');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939812469DE2');
        $this->addSql('ALTER TABLE complaint DROP FOREIGN KEY FK_5F2732B519EB6921');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939819EB6921');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C619EB6921');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1F624B39D');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64919EB6921');
        $this->addSql('ALTER TABLE order_comment DROP FOREIGN KEY FK_620EFB278D9F6D38');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6C385221F');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D18D9F6D38');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6499393F8FE');
        $this->addSql('ALTER TABLE category_performer DROP FOREIGN KEY FK_D041D9DB6C6B33F3');
        $this->addSql('ALTER TABLE complaint DROP FOREIGN KEY FK_5F2732B56C6B33F3');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993986C6B33F3');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C66C6B33F3');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1CD53EDB6');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6496C6B33F3');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455A76ED395');
        $this->addSql('ALTER TABLE partner DROP FOREIGN KEY FK_312B3E16A76ED395');
        $this->addSql('ALTER TABLE performer DROP FOREIGN KEY FK_17210BEBA76ED395');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_performer');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE complaint');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_comment');
        $this->addSql('DROP TABLE partner');
        $this->addSql('DROP TABLE performer');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE user');
    }
}
