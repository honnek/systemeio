<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240229095435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Изменим `type` `code` `value` на NOT NULL';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coupon ALTER type SET NOT NULL');
        $this->addSql('ALTER TABLE coupon ALTER code SET NOT NULL');
        $this->addSql('ALTER TABLE coupon ALTER value SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE coupon ALTER type DROP NOT NULL');
        $this->addSql('ALTER TABLE coupon ALTER code DROP NOT NULL');
        $this->addSql('ALTER TABLE coupon ALTER value DROP NOT NULL');
    }
}
