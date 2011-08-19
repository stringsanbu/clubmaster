<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20110726132029 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        
        $this->addSql("CREATE TABLE club_message_message_location (message_id INT NOT NULL, location_id INT NOT NULL, INDEX IDX_EEBD94BC537A1329 (message_id), INDEX IDX_EEBD94BC64D218E (location_id), PRIMARY KEY(message_id, location_id)) ENGINE = InnoDB");
        $this->addSql("CREATE TABLE club_message_message_group (message_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_4A1687E1537A1329 (message_id), INDEX IDX_4A1687E1FE54D947 (group_id), PRIMARY KEY(message_id, group_id)) ENGINE = InnoDB");
        $this->addSql("CREATE TABLE club_message_message_user (message_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_59115838537A1329 (message_id), INDEX IDX_59115838A76ED395 (user_id), PRIMARY KEY(message_id, user_id)) ENGINE = InnoDB");
        $this->addSql("ALTER TABLE club_message_message_location ADD FOREIGN KEY (message_id) REFERENCES club_message_message(id)");
        $this->addSql("ALTER TABLE club_message_message_location ADD FOREIGN KEY (location_id) REFERENCES club_user_location(id)");
        $this->addSql("ALTER TABLE club_message_message_group ADD FOREIGN KEY (message_id) REFERENCES club_message_message(id)");
        $this->addSql("ALTER TABLE club_message_message_group ADD FOREIGN KEY (group_id) REFERENCES club_user_group(id)");
        $this->addSql("ALTER TABLE club_message_message_user ADD FOREIGN KEY (message_id) REFERENCES club_message_message(id)");
        $this->addSql("ALTER TABLE club_message_message_user ADD FOREIGN KEY (user_id) REFERENCES club_user_user(id)");
        $this->addSql("DROP TABLE club_mail_mail_group");
        $this->addSql("DROP TABLE club_mail_mail_location");
        $this->addSql("DROP TABLE club_mail_mail_user");
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        
        $this->addSql("CREATE TABLE club_mail_mail_group (mail_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_92D63E88C8776F01 (mail_id), INDEX IDX_92D63E88FE54D947 (group_id), PRIMARY KEY(mail_id, group_id)) ENGINE = InnoDB");
        $this->addSql("CREATE TABLE club_mail_mail_location (mail_id INT NOT NULL, location_id INT NOT NULL, INDEX IDX_7D8B38AC8776F01 (mail_id), INDEX IDX_7D8B38A64D218E (location_id), PRIMARY KEY(mail_id, location_id)) ENGINE = InnoDB");
        $this->addSql("CREATE TABLE club_mail_mail_user (mail_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_9483EB90C8776F01 (mail_id), INDEX IDX_9483EB90A76ED395 (user_id), PRIMARY KEY(mail_id, user_id)) ENGINE = InnoDB");
        $this->addSql("ALTER TABLE club_mail_mail_group ADD CONSTRAINT club_mail_mail_group_ibfk_3 FOREIGN KEY (mail_id) REFERENCES club_message_message(id)");
        $this->addSql("ALTER TABLE club_mail_mail_group ADD CONSTRAINT club_mail_mail_group_ibfk_2 FOREIGN KEY (group_id) REFERENCES club_user_group(id)");
        $this->addSql("ALTER TABLE club_mail_mail_location ADD CONSTRAINT club_mail_mail_location_ibfk_3 FOREIGN KEY (mail_id) REFERENCES club_message_message(id)");
        $this->addSql("ALTER TABLE club_mail_mail_location ADD CONSTRAINT club_mail_mail_location_ibfk_2 FOREIGN KEY (location_id) REFERENCES club_user_location(id)");
        $this->addSql("ALTER TABLE club_mail_mail_user ADD CONSTRAINT club_mail_mail_user_ibfk_3 FOREIGN KEY (mail_id) REFERENCES club_message_message(id)");
        $this->addSql("ALTER TABLE club_mail_mail_user ADD CONSTRAINT club_mail_mail_user_ibfk_2 FOREIGN KEY (user_id) REFERENCES club_user_user(id)");
        $this->addSql("DROP TABLE club_message_message_location");
        $this->addSql("DROP TABLE club_message_message_group");
        $this->addSql("DROP TABLE club_message_message_user");
    }
}