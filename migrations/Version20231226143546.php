<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Exam\ExamStudentsResolver;
use App\Migrations\ExamRepositoryDependantMigrationInterface;
use App\Migrations\ExamStudentsResolverDependentMigrationInterface;
use App\Repository\ExamRepositoryInterface;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231226143546 extends AbstractMigration implements ExamStudentsResolverDependentMigrationInterface, ExamRepositoryDependantMigrationInterface
{
    private ExamStudentsResolver $resolver;
    private ExamRepositoryInterface $examRepository;

    public function setExamStudentsResolver(ExamStudentsResolver $resolver): void {
        $this->resolver = $resolver;
    }

    public function setExamRepository(ExamRepositoryInterface $examRepository): void {
        $this->examRepository = $examRepository;
    }

    public function postUp(Schema $schema): void {
        foreach($this->examRepository->findAll() as $exam) {
            $examStudents = $this->resolver->resolveExamStudentsFromGivenStudents($exam, $exam->getStudents()->toArray());
            $this->resolver->setExamStudents($exam, $examStudents);
            $this->examRepository->persist($exam);
        }
    }

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE exam_students_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_5aedb07969302d6b8d03805ec52778e7_idx (type), INDEX object_id_5aedb07969302d6b8d03805ec52778e7_idx (object_id), INDEX discriminator_5aedb07969302d6b8d03805ec52778e7_idx (discriminator), INDEX transaction_hash_5aedb07969302d6b8d03805ec52778e7_idx (transaction_hash), INDEX blame_id_5aedb07969302d6b8d03805ec52778e7_idx (blame_id), INDEX created_at_5aedb07969302d6b8d03805ec52778e7_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE exam_students ADD id INT UNSIGNED AUTO_INCREMENT NOT NULL, ADD tuition_id INT UNSIGNED DEFAULT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE exam_students ADD CONSTRAINT FK_E70DC2817FFA6BA FOREIGN KEY (tuition_id) REFERENCES tuition (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_E70DC2817FFA6BA ON exam_students (tuition_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE exam_students_audit');
        $this->addSql('ALTER TABLE exam_students MODIFY id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE exam_students DROP FOREIGN KEY FK_E70DC2817FFA6BA');
        $this->addSql('DROP INDEX IDX_E70DC2817FFA6BA ON exam_students');
        $this->addSql('DROP INDEX `PRIMARY` ON exam_students');
        $this->addSql('ALTER TABLE exam_students DROP id, DROP tuition_id');
        $this->addSql('ALTER TABLE exam_students ADD PRIMARY KEY (exam_id, student_id)');
    }
}
