<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250925111138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE accounts (id UUID NOT NULL, user_id UUID NOT NULL, balance_cents INT DEFAULT 0 NOT NULL, version INT DEFAULT 1 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CAC89EACA76ED395 ON accounts (user_id)');
        $this->addSql('COMMENT ON COLUMN accounts.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN accounts.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN accounts.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN accounts.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE transaction (id UUID NOT NULL, account_id UUID NOT NULL, type VARCHAR(16) NOT NULL, amount_cents INT NOT NULL, balance_after_cents INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_723705D19B6B5FBA ON transaction (account_id)');
        $this->addSql('CREATE UNIQUE INDEX idx_tx_account_created ON transaction (account_id, created_at)');
        $this->addSql('COMMENT ON COLUMN transaction.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN transaction.account_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN transaction.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE users (id UUID NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('COMMENT ON COLUMN users.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN users.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN users.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE accounts ADD CONSTRAINT FK_CAC89EACA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D19B6B5FBA FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE accounts DROP CONSTRAINT FK_CAC89EACA76ED395');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D19B6B5FBA');
        $this->addSql('DROP TABLE accounts');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE users');
    }
}
