<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220821152004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer ADD birth_state_id INT DEFAULT NULL, ADD state_id INT DEFAULT NULL, ADD agent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09BCC5F564 FOREIGN KEY (birth_state_id) REFERENCES us_state (id)');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E095D83CC1 FOREIGN KEY (state_id) REFERENCES us_state (id)');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E093414710B FOREIGN KEY (agent_id) REFERENCES agent (id)');
        $this->addSql('CREATE INDEX IDX_81398E09BCC5F564 ON customer (birth_state_id)');
        $this->addSql('CREATE INDEX IDX_81398E095D83CC1 ON customer (state_id)');
        $this->addSql('CREATE INDEX IDX_81398E093414710B ON customer (agent_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E09BCC5F564');
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E095D83CC1');
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E093414710B');
        $this->addSql('DROP INDEX IDX_81398E09BCC5F564 ON customer');
        $this->addSql('DROP INDEX IDX_81398E095D83CC1 ON customer');
        $this->addSql('DROP INDEX IDX_81398E093414710B ON customer');
        $this->addSql('ALTER TABLE customer DROP birth_state_id, DROP state_id, DROP agent_id');
    }
}
