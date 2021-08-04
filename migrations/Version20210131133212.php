<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210131133212 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_comment ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE order_comment ADD CONSTRAINT FK_620EFB27A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_620EFB27A76ED395 ON order_comment (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_comment DROP FOREIGN KEY FK_620EFB27A76ED395');
        $this->addSql('DROP INDEX IDX_620EFB27A76ED395 ON order_comment');
        $this->addSql('ALTER TABLE order_comment DROP user_id');
    }
}
