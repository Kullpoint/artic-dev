<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210206122545 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP INDEX UNIQ_723705D18D9F6D38, ADD INDEX IDX_723705D18D9F6D38 (order_id)');
        $this->addSql('ALTER TABLE transaction ADD partner_id INT DEFAULT NULL, CHANGE receiver_id receiver_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D19393F8FE FOREIGN KEY (partner_id) REFERENCES performer (id)');
        $this->addSql('CREATE INDEX IDX_723705D19393F8FE ON transaction (partner_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP INDEX IDX_723705D18D9F6D38, ADD UNIQUE INDEX UNIQ_723705D18D9F6D38 (order_id)');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D19393F8FE');
        $this->addSql('DROP INDEX IDX_723705D19393F8FE ON transaction');
        $this->addSql('ALTER TABLE transaction DROP partner_id, CHANGE receiver_id receiver_id INT NOT NULL');
    }
}
