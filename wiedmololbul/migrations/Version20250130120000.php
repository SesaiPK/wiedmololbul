<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250130120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migracja tworząca tabele, funkcje, triggery oraz indeksy';
    }

    public function up(Schema $schema): void
    {
        // Tabela article
        $this->addSql('CREATE TABLE article (id SERIAL PRIMARY KEY, name VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, content TEXT NOT NULL)');
        $this->addSql('ALTER TABLE article OWNER TO symfony');

        // Tabela messenger_messages
        $this->addSql('CREATE TABLE messenger_messages (
            id BIGSERIAL PRIMARY KEY, 
            body TEXT NOT NULL, 
            headers TEXT NOT NULL, 
            queue_name VARCHAR(190) NOT NULL, 
            created_at TIMESTAMP NOT NULL, 
            available_at TIMESTAMP NOT NULL, 
            delivered_at TIMESTAMP DEFAULT NULL
        )');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE messenger_messages OWNER TO symfony');
        $this->addSql('CREATE INDEX idx_75ea56e0fb7336f0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX idx_75ea56e0e3bd61ce ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX idx_75ea56e016ba31db ON messenger_messages (delivered_at)');

        // Tabela user
        $this->addSql('CREATE TABLE "user" (id SERIAL PRIMARY KEY, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL)');
        $this->addSql('ALTER TABLE "user" OWNER TO symfony');
        $this->addSql('CREATE UNIQUE INDEX uniq_identifier_email ON "user" (email)');

        // Tabela post
        $this->addSql('CREATE TABLE post (id SERIAL PRIMARY KEY, name VARCHAR(255) NOT NULL, content VARCHAR(1000) NOT NULL, author VARCHAR(255) NOT NULL)');
        $this->addSql('ALTER TABLE post OWNER TO symfony');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT fk_author FOREIGN KEY (author) REFERENCES "user" (email)');

        // Funkcja i trigger: notify_messenger_messages
        $this->addSql('
            CREATE FUNCTION notify_messenger_messages() RETURNS trigger
            LANGUAGE plpgsql AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
            $$');
        $this->addSql('ALTER FUNCTION notify_messenger_messages() OWNER TO symfony');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE FUNCTION notify_messenger_messages()');

        // Funkcja i trigger: delete_user_posts
        $this->addSql('
            CREATE FUNCTION delete_user_posts() RETURNS trigger
            LANGUAGE plpgsql AS $$
            BEGIN
                DELETE FROM post WHERE author = OLD.email;
                RETURN OLD;
            END;
            $$');
        $this->addSql('ALTER FUNCTION delete_user_posts() OWNER TO symfony');
        $this->addSql('CREATE TRIGGER delete_user_posts_trigger BEFORE DELETE ON "user" FOR EACH ROW EXECUTE FUNCTION delete_user_posts()');
    }

    public function down(Schema $schema): void
    {
        // Usuwanie triggerów i funkcji
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages');
        $this->addSql('DROP FUNCTION IF EXISTS notify_messenger_messages');

        $this->addSql('DROP TRIGGER IF EXISTS delete_user_posts_trigger ON "user"');
        $this->addSql('DROP FUNCTION IF EXISTS delete_user_posts');

        // Usuwanie tabel
        $this->addSql('DROP TABLE IF EXISTS post');
        $this->addSql('DROP TABLE IF EXISTS "user"');
        $this->addSql('DROP TABLE IF EXISTS messenger_messages');
        $this->addSql('DROP TABLE IF EXISTS article');
        $this->addSql('DROP TABLE IF EXISTS doctrine_migration_versions');
    }
}
