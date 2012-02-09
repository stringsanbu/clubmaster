<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20120209232011 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        
        $this->addSql("CREATE TABLE plan_field (plan_id INT NOT NULL, field_id INT NOT NULL, INDEX IDX_85300A4E899029B (plan_id), INDEX IDX_85300A4443707B0 (field_id), PRIMARY KEY(plan_id, field_id)) ENGINE = InnoDB");
        $this->addSql("ALTER TABLE plan_field ADD CONSTRAINT FK_85300A4E899029B FOREIGN KEY (plan_id) REFERENCES club_booking_plan(id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE plan_field ADD CONSTRAINT FK_85300A4443707B0 FOREIGN KEY (field_id) REFERENCES club_booking_field(id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE club_booking_plan DROP FOREIGN KEY FK_BF7FD1C3443707B0");
        $this->addSql("DROP INDEX IDX_BF7FD1C3443707B0 ON club_booking_plan");
        $this->addSql("ALTER TABLE club_booking_plan DROP field_id");
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        
        $this->addSql("DROP TABLE plan_field");
        $this->addSql("ALTER TABLE club_booking_plan ADD field_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE club_booking_plan ADD CONSTRAINT FK_BF7FD1C3443707B0 FOREIGN KEY (field_id) REFERENCES club_booking_field(id)");
        $this->addSql("CREATE INDEX IDX_BF7FD1C3443707B0 ON club_booking_plan (field_id)");
    }
}
