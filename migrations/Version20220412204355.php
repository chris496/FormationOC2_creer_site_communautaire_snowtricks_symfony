<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220412204355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment ADD tricks_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C3B153154 FOREIGN KEY (tricks_id) REFERENCES tricks (id)');
        $this->addSql('CREATE INDEX IDX_9474526C3B153154 ON comment (tricks_id)');
        $this->addSql('ALTER TABLE media ADD tricks_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C3B153154 FOREIGN KEY (tricks_id) REFERENCES tricks (id)');
        $this->addSql('CREATE INDEX IDX_6A2CA10C3B153154 ON media (tricks_id)');
        $this->addSql('ALTER TABLE tricks ADD user_id INT NOT NULL, ADD category_id INT NOT NULL');
        $this->addSql('ALTER TABLE tricks ADD CONSTRAINT FK_E1D902C1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tricks ADD CONSTRAINT FK_E1D902C112469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_E1D902C1A76ED395 ON tricks (user_id)');
        $this->addSql('CREATE INDEX IDX_E1D902C112469DE2 ON tricks (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C3B153154');
        $this->addSql('DROP INDEX IDX_9474526C3B153154 ON comment');
        $this->addSql('ALTER TABLE comment DROP tricks_id');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C3B153154');
        $this->addSql('DROP INDEX IDX_6A2CA10C3B153154 ON media');
        $this->addSql('ALTER TABLE media DROP tricks_id');
        $this->addSql('ALTER TABLE tricks DROP FOREIGN KEY FK_E1D902C1A76ED395');
        $this->addSql('ALTER TABLE tricks DROP FOREIGN KEY FK_E1D902C112469DE2');
        $this->addSql('DROP INDEX IDX_E1D902C1A76ED395 ON tricks');
        $this->addSql('DROP INDEX IDX_E1D902C112469DE2 ON tricks');
        $this->addSql('ALTER TABLE tricks DROP user_id, DROP category_id');
    }
}
