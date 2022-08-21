<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220821151505 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE agent_carrier (agent_id INT NOT NULL, carrier_id INT NOT NULL, INDEX IDX_956A93B63414710B (agent_id), INDEX IDX_956A93B621DFC797 (carrier_id), PRIMARY KEY(agent_id, carrier_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE agent_ancillary (agent_id INT NOT NULL, ancillary_id INT NOT NULL, INDEX IDX_5A761C623414710B (agent_id), INDEX IDX_5A761C6244FDCBB (ancillary_id), PRIMARY KEY(agent_id, ancillary_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE agent_carrier ADD CONSTRAINT FK_956A93B63414710B FOREIGN KEY (agent_id) REFERENCES agent (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE agent_carrier ADD CONSTRAINT FK_956A93B621DFC797 FOREIGN KEY (carrier_id) REFERENCES carrier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE agent_ancillary ADD CONSTRAINT FK_5A761C623414710B FOREIGN KEY (agent_id) REFERENCES agent (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE agent_ancillary ADD CONSTRAINT FK_5A761C6244FDCBB FOREIGN KEY (ancillary_id) REFERENCES ancillary (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agent_carrier DROP FOREIGN KEY FK_956A93B63414710B');
        $this->addSql('ALTER TABLE agent_carrier DROP FOREIGN KEY FK_956A93B621DFC797');
        $this->addSql('ALTER TABLE agent_ancillary DROP FOREIGN KEY FK_5A761C623414710B');
        $this->addSql('ALTER TABLE agent_ancillary DROP FOREIGN KEY FK_5A761C6244FDCBB');
        $this->addSql('DROP TABLE agent_carrier');
        $this->addSql('DROP TABLE agent_ancillary');
    }
}
