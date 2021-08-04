<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210419124522 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE complaint ADD review_id INT NOT NULL');
        $this->addSql('ALTER TABLE complaint ADD CONSTRAINT FK_5F2732B53E2E969B FOREIGN KEY (review_id) REFERENCES review (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5F2732B53E2E969B ON complaint (review_id)');
        $this->addSql('ALTER TABLE review ADD complaint_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6EDAE188E FOREIGN KEY (complaint_id) REFERENCES complaint (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_794381C6EDAE188E ON review (complaint_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE complaint DROP FOREIGN KEY FK_5F2732B53E2E969B');
        $this->addSql('DROP INDEX UNIQ_5F2732B53E2E969B ON complaint');
        $this->addSql('ALTER TABLE complaint DROP review_id');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6EDAE188E');
        $this->addSql('DROP INDEX UNIQ_794381C6EDAE188E ON review');
        $this->addSql('ALTER TABLE review DROP complaint_id');
    }
}
