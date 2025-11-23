<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251121213234 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, sku VARCHAR(10) NOT NULL, name VARCHAR(255) NOT NULL, unit_price INT NOT NULL, UNIQUE INDEX UNIQ_D34A04ADF9038C4 (sku), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE promotion (id INT AUTO_INCREMENT NOT NULL, quantity INT NOT NULL, special_price INT NOT NULL, product_id INT NOT NULL, INDEX IDX_C11D7DD14584665A (product_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE sale (id INT AUTO_INCREMENT NOT NULL, total_price INT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE sale_item (id INT AUTO_INCREMENT NOT NULL, quantity INT NOT NULL, line_price INT NOT NULL, sale_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_A35551FB4A7E4868 (sale_id), INDEX IDX_A35551FB4584665A (product_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE promotion ADD CONSTRAINT FK_C11D7DD14584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE sale_item ADD CONSTRAINT FK_A35551FB4A7E4868 FOREIGN KEY (sale_id) REFERENCES sale (id)');
        $this->addSql('ALTER TABLE sale_item ADD CONSTRAINT FK_A35551FB4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE promotion DROP FOREIGN KEY FK_C11D7DD14584665A');
        $this->addSql('ALTER TABLE sale_item DROP FOREIGN KEY FK_A35551FB4A7E4868');
        $this->addSql('ALTER TABLE sale_item DROP FOREIGN KEY FK_A35551FB4584665A');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE promotion');
        $this->addSql('DROP TABLE sale');
        $this->addSql('DROP TABLE sale_item');
    }
}
