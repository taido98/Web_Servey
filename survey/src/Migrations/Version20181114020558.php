<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181114020558 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE survey_form (id INT AUTO_INCREMENT NOT NULL, content JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey_form_class_subject (survey_form_id INT NOT NULL, class_subject_id INT NOT NULL, INDEX IDX_8CBFE20D1BDAB8C6 (survey_form_id), INDEX IDX_8CBFE20DB4332E25 (class_subject_id), PRIMARY KEY(survey_form_id, class_subject_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey_form_student (survey_form_id INT NOT NULL, student_id INT NOT NULL, INDEX IDX_F69DC3581BDAB8C6 (survey_form_id), INDEX IDX_F69DC358CB944F1A (student_id), PRIMARY KEY(survey_form_id, student_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE survey_form_class_subject ADD CONSTRAINT FK_8CBFE20D1BDAB8C6 FOREIGN KEY (survey_form_id) REFERENCES survey_form (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE survey_form_class_subject ADD CONSTRAINT FK_8CBFE20DB4332E25 FOREIGN KEY (class_subject_id) REFERENCES class_subject (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE survey_form_student ADD CONSTRAINT FK_F69DC3581BDAB8C6 FOREIGN KEY (survey_form_id) REFERENCES survey_form (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE survey_form_student ADD CONSTRAINT FK_F69DC358CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE survey_form_class_subject DROP FOREIGN KEY FK_8CBFE20D1BDAB8C6');
        $this->addSql('ALTER TABLE survey_form_student DROP FOREIGN KEY FK_F69DC3581BDAB8C6');
        $this->addSql('DROP TABLE survey_form');
        $this->addSql('DROP TABLE survey_form_class_subject');
        $this->addSql('DROP TABLE survey_form_student');
    }
}
