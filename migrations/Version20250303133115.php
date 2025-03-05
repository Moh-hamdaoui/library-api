<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250303133115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE borrow_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE borrow (id INT NOT NULL, user_id INT NOT NULL, book_id INT NOT NULL, borrowed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, due_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, returned_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_55DBA8B0A76ED395 ON borrow (user_id)');
        $this->addSql('CREATE INDEX IDX_55DBA8B016A2B381 ON borrow (book_id)');
        $this->addSql('ALTER TABLE borrow ADD CONSTRAINT FK_55DBA8B0A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE borrow ADD CONSTRAINT FK_55DBA8B016A2B381 FOREIGN KEY (book_id) REFERENCES book (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE book DROP is_borrowed');
        $this->addSql('ALTER TABLE book DROP borrow_date');
        $this->addSql('ALTER TABLE book DROP return_date');
        $this->addSql('ALTER TABLE "user" DROP borrowed_books');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE borrow_id_seq CASCADE');
        $this->addSql('ALTER TABLE borrow DROP CONSTRAINT FK_55DBA8B0A76ED395');
        $this->addSql('ALTER TABLE borrow DROP CONSTRAINT FK_55DBA8B016A2B381');
        $this->addSql('DROP TABLE borrow');
        $this->addSql('ALTER TABLE book ADD is_borrowed BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE book ADD borrow_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE book ADD return_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD borrowed_books TEXT NOT NULL');
        $this->addSql('COMMENT ON COLUMN "user".borrowed_books IS \'(DC2Type:array)\'');
    }
}
