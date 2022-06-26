<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220602162850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media ADD tricks_for_video_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CB6014822 FOREIGN KEY (tricks_for_video_id) REFERENCES tricks (id)');
        $this->addSql('CREATE INDEX IDX_6A2CA10CB6014822 ON media (tricks_for_video_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10CB6014822');
        $this->addSql('DROP INDEX IDX_6A2CA10CB6014822 ON media');
        $this->addSql('ALTER TABLE media DROP tricks_for_video_id');
    }
}
