<?php

namespace App\Command;

use App\Entity\Appointment;
use App\Entity\AppointmentCategory;
use App\Entity\Exam;
use App\Entity\Grade;
use App\Entity\GradeMembership;
use App\Entity\GradeTeacher;
use App\Entity\PrivacyCategory;
use App\Entity\Room;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupMembership;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\TimetableLesson;
use App\Entity\TimetableSupervision;
use App\Entity\Tuition;
use App\Entity\UserTypeEntity;
use App\Request\Data\AppointmentCategoriesData;
use App\Request\Data\AppointmentCategoryData;
use App\Request\Data\AppointmentData;
use App\Request\Data\AppointmentsData;
use App\Request\Data\ExamData;
use App\Request\Data\ExamsData;
use App\Request\Data\ExamTuition;
use App\Request\Data\GradeData;
use App\Request\Data\GradeMembershipData;
use App\Request\Data\GradeMembershipsData;
use App\Request\Data\GradesData;
use App\Request\Data\GradeTeacherData;
use App\Request\Data\GradeTeachersData;
use App\Request\Data\PrivacyCategoriesData;
use App\Request\Data\PrivacyCategoryData;
use App\Request\Data\RoomData;
use App\Request\Data\RoomsData;
use App\Request\Data\StudentData;
use App\Request\Data\StudentsData;
use App\Request\Data\StudyGroupData;
use App\Request\Data\StudyGroupMembershipData;
use App\Request\Data\StudyGroupMembershipsData;
use App\Request\Data\StudyGroupsData;
use App\Request\Data\SubjectData;
use App\Request\Data\SubjectsData;
use App\Request\Data\TeacherData;
use App\Request\Data\TeachersData;
use App\Request\Data\TimetableLessonData;
use App\Request\Data\TimetableLessonsData;
use App\Request\Data\TimetableSupervisionData;
use App\Request\Data\TimetableSupervisionsData;
use App\Request\Data\TuitionData;
use App\Request\Data\TuitionsData;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand('app:export', description: 'Exportiert alle importierten Daten, sodass diese (z.B. auf einem anderen Server importiert werden können).')]
class ExportDataCommand extends Command {

    public function __construct(private readonly KernelInterface $kernel, private readonly EntityManagerInterface $em, private readonly SerializerInterface $serializer, string $name = null) {
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);

        $this->exportStudents($io);
        $this->exportTeachers($io);
        $this->exportSubjects($io);
        $this->exportRooms($io);
        $this->exportPrivacyCategories($io);
        $this->exportGrades($io);
        $this->exportGradeMemberships($io);
        $this->exportGradeTeachers($io);
        $this->exportStudyGroups($io);
        $this->exportStudyGroupMemberships($io);
        $this->exportTuitions($io);
        $this->exportExams($io);
        $this->exportAppointmentCategories($io);
        $this->exportAppointments($io);
        $this->exportTimetable($io);
        $this->exportSupervisions($io);

        $io->success('Export erfolgreich - bitte das Verzeichnis export/ prüfen');

        return Command::SUCCESS;
    }

    private function writeFile(string $filename, $object): void {
        $json = $this->serializer->serialize($object, 'json');
        $target = sprintf('%s/%s', $this->kernel->getProjectDir(), 'export');
        if(!is_dir($target)) {
            mkdir($target);
            $handle = fopen(sprintf('%s/.gitignore', $target), 'w');
            fwrite($handle, '*');
            fclose($handle);
        }

        $handle = fopen(sprintf('%s/%s', $target, $filename), 'w');
        fwrite($handle, $json);
        fclose($handle);
    }

    private function getSectionKey(Section $section): string {
        return sprintf('%d_%d', $section->getYear(), $section->getNumber());
    }

    private function exportStudents(SymfonyStyle $io): void {
        $io->section('Exportiere Lernende');

        $students = [ ];

        /** @var Section[] $sections */
        $sections = $this->em->getRepository(Section::class)->findAll();

        /** @var Student $student */
        foreach($this->em->getRepository(Student::class)->findAll() as $student) {
            foreach($student->getSections() as $section) {
                $key = $this->getSectionKey($section);

                if(!isset($students[$key])) {
                    $students[$key] = [ ];
                }

                $students[$key][] = (new StudentData())
                    ->setId($student->getId())
                    ->setFirstname($student->getFirstname())
                    ->setLastname($student->getLastname())
                    ->setEmail($student->getEmail())
                    ->setBirthday($student->getBirthday())
                    ->setGender($student->getGender()->value)
                    ->setStatus($student->getStatus());
            }
        }

        foreach($sections as $section) {
            $data = $students[$this->getSectionKey($section)] ?? [ ];

            $this->writeFile(
                sprintf('students_%s.json', $this->getSectionKey($section)),
                (new StudentsData())->setSection($section->getNumber())->setYear($section->getYear())->setStudents($data)
            );

            $io->success(sprintf('%d Lernende exportiert (%s)', count($data), $section->getDisplayName()));
        }
    }

    private function exportPrivacyCategories(SymfonyStyle $io): void {
        $io->section('Exportiere Datenschutzkategorien');

        $categories = [ ];
        /** @var PrivacyCategory $category */
        foreach($this->em->getRepository(PrivacyCategory::class)->findAll() as $category) {
            $categories[] = (new PrivacyCategoryData())
                ->setId($category->getExternalId())
                ->setLabel($category->getLabel())
                ->setDescription($category->getDescription());
        }

        $data = new PrivacyCategoriesData();
        $data->setCategories($categories);

        $this->writeFile('privacy.json', $data);
        $io->success(sprintf('%d Datenschutzkategorie(n) exportiert', count($categories)));
    }

    private function exportTeachers(SymfonyStyle $io): void {
        $io->section('Exportiere Lehrkräfte');

        $teachers = [ ];

        /** @var Section[] $sections */
        $sections = $this->em->getRepository(Section::class)->findAll();

        /** @var Teacher $teacher */
        foreach($this->em->getRepository(Teacher::class)->findAll() as $teacher) {
            foreach($teacher->getSections() as $section) {
                $key = $this->getSectionKey($section);

                if (!isset($teachers[$key])) {
                    $teachers[$key] = [];
                }

                $teachers[$key][] = (new TeacherData())
                    ->setId($teacher->getExternalId())
                    ->setAcronym($teacher->getAcronym())
                    ->setFirstname($teacher->getFirstname())
                    ->setLastname($teacher->getLastname())
                    ->setEmail($teacher->getEmail())
                    ->setGender($teacher->getGender()->value)
                    ->setTitle($teacher->getTitle())
                    ->setSubjects($teacher->getSubjects()->map(fn(Subject $subject) => $subject->getExternalId())->toArray());
            }
        }

        foreach($sections as $section) {
            $data = $teachers[$this->getSectionKey($section)] ?? [ ];

            $this->writeFile(
                sprintf('teachers_%s.json', $this->getSectionKey($section)),
                (new TeachersData())->setSection($section->getNumber())->setYear($section->getYear())->setTeachers($data)
            );

            $io->success(sprintf('%d Lehrkräfte exportiert (%s)', count($data), $section->getDisplayName()));
        }
    }

    private function exportSubjects(SymfonyStyle $io): void {
        $io->section('Exportiere Fächer');
        $subjects = [ ];

        /** @var Subject $subject */
        foreach($this->em->getRepository(Subject::class)->findAll() as $subject) {
            if($subject->getExternalId() === null) {
                continue;
            }

            $subjects[] = (new SubjectData())
                ->setId($subject->getExternalId())
                ->setAbbreviation($subject->getAbbreviation())
                ->setName($subject->getName());
        }

        $data = (new SubjectsData())->setSubjects($subjects);
        $this->writeFile('subjects.json', $data);

        $io->success(sprintf('%d Fächer exportiert', count($subjects)));
    }

    private function exportRooms(SymfonyStyle $io): void {
        $io->section('Exportiere Räume');
        $rooms = [ ];

        /** @var Room $room */
        foreach($this->em->getRepository(Room::class)->findAll() as $room) {
            $rooms[] = (new RoomData())
                ->setId($room->getExternalId())
                ->setName($room->getName())
                ->setCapacity($room->getCapacity())
                ->setDescription($room->getDescription());
        }

        $data = (new RoomsData())->setRooms($rooms);
        $this->writeFile('rooms.json', $data);

        $io->success(sprintf('%d Räume exportiert', count($rooms)));
    }

    private function exportGrades(SymfonyStyle $io): void {
        $io->section('Exportiere Klassen');

        $grades = [ ];

        /** @var Grade $grade */
        foreach($this->em->getRepository(Grade::class)->findAll() as $grade) {
            $grades[] = (new GradeData())
                ->setId($grade->getExternalId())
                ->setName($grade->getName());
        }

        $data = (new GradesData())->setGrades($grades);
        $this->writeFile('grades.json', $data);

        $io->success(sprintf('%d Klassen exportiert', count($grades)));
    }

    private function exportGradeMemberships(SymfonyStyle $io): void {
        $io->section('Exportiere Klassenmitgliedschaften');

        $memberships = [ ];

        /** @var Section[] $sections */
        $sections = $this->em->getRepository(Section::class)->findAll();

        /** @var GradeMembership $membership */
        foreach($this->em->getRepository(GradeMembership::class)->findAll() as $membership) {
            $key = $this->getSectionKey($membership->getSection());

            if(!isset($memberships[$key])) {
                $memberships[$key] = [ ];
            }

            $memberships[$key][] = (new GradeMembershipData())
                ->setGrade($membership->getGrade()->getExternalId())
                ->setStudent($membership->getStudent()->getExternalId());
        }

        foreach($sections as $section) {
            $data = $memberships[$this->getSectionKey($section)] ?? [ ];

            $this->writeFile(
                sprintf('grade_memberships_%s.json', $this->getSectionKey($section)),
                (new GradeMembershipsData())->setSection($section->getNumber())->setYear($section->getYear())->setMemberships($data)
            );

            $io->success(sprintf('%d Klassenmitgliedschaften exportiert (%s)', count($data), $section->getDisplayName()));
        }
    }

    private function exportGradeTeachers(SymfonyStyle $io): void {
        $io->section('Exportiere Klassenleitungen');

        $gradeTeachers = [ ];

        /** @var Section[] $sections */
        $sections = $this->em->getRepository(Section::class)->findAll();

        /** @var GradeTeacher $gradeTeacher */
        foreach($this->em->getRepository(GradeTeacher::class)->findAll() as $gradeTeacher) {
            $key = $this->getSectionKey($gradeTeacher->getSection());

            if(!isset($gradeTeachers[$key])) {
                $gradeTeachers[$key] = [ ];
            }

            $gradeTeachers[$key][] = (new GradeTeacherData())
                ->setGrade($gradeTeacher->getGrade()->getExternalId())
                ->setTeacher($gradeTeacher->getTeacher()->getExternalId())
                ->setType($gradeTeacher->getType()->value);
        }

        foreach($sections as $section) {
            $data = $gradeTeachers[$this->getSectionKey($section)] ?? [ ];

            $this->writeFile(
                sprintf('grade_teachers%s.json', $this->getSectionKey($section)),
                (new GradeTeachersData())->setSection($section->getNumber())->setYear($section->getYear())->setGradeTeachers($data)
            );

            $io->success(sprintf('%d Klassenleitungen exportiert (%s)', count($data), $section->getDisplayName()));
        }
    }

    private function exportStudyGroups(SymfonyStyle $io): void {
        $io->section('Exportiere Lerngruppen');

        $studyGroups = [ ];

        /** @var Section[] $sections */
        $sections = $this->em->getRepository(Section::class)->findAll();

        /** @var StudyGroup $studyGroup */
        foreach($this->em->getRepository(StudyGroup::class)->findAll() as $studyGroup) {
            $key = $this->getSectionKey($studyGroup->getSection());

            if(!isset($studyGroups[$key])) {
                $studyGroups[$key] = [ ];
            }

            $studyGroups[$key][] = (new StudyGroupData())
                ->setId($studyGroup->getExternalId())
                ->setName($studyGroup->getName())
                ->setType($studyGroup->getType()->value)
                ->setGrades($studyGroup->getGrades()->map(fn(Grade $grade) => $grade->getExternalId())->toArray());
        }

        foreach($sections as $section) {
            $data = $studyGroups[$this->getSectionKey($section)] ?? [ ];

            $this->writeFile(
                sprintf('studygroups_%s.json', $this->getSectionKey($section)),
                (new StudyGroupsData())->setSection($section->getNumber())->setYear($section->getYear())->setStudyGroups($data)
            );

            $io->success(sprintf('%d Lerngruppen exportiert (%s)', count($data), $section->getDisplayName()));
        }
    }

    private function exportStudyGroupMemberships(SymfonyStyle $io): void {
        $io->section('Exportiere Lerngruppenmitgliedschaften');

        $memberships = [ ];

        /** @var Section[] $sections */
        $sections = $this->em->getRepository(Section::class)->findAll();

        /** @var StudyGroupMembership $membership */
        foreach($this->em->getRepository(StudyGroupMembership::class)->findAll() as $membership) {
            $key = $this->getSectionKey($membership->getStudyGroup()->getSection());

            if(!isset($memberships[$key])) {
                $memberships[$key] = [ ];
            }

            $memberships[$key][] = (new StudyGroupMembershipData())
                ->setStudent($membership->getStudent()->getExternalId())
                ->setStudyGroup($membership->getStudyGroup()->getExternalId())
                ->setType($membership->getType());
        }

        foreach($sections as $section) {
            $data = $memberships[$this->getSectionKey($section)] ?? [ ];

            $this->writeFile(
                sprintf('studygroup_memberships_%s.json', $this->getSectionKey($section)),
                (new StudyGroupMembershipsData())->setSection($section->getNumber())->setYear($section->getYear())->setMemberships($data)
            );

            $io->success(sprintf('%d Lerngruppenmitgliedschaften exportiert (%s)', count($data), $section->getDisplayName()));
        }
    }

    private function exportTuitions(SymfonyStyle $io): void {
        $io->section('Exportiere Unterrichte');

        $tuitions = [ ];

        /** @var Section[] $sections */
        $sections = $this->em->getRepository(Section::class)->findAll();

        /** @var Tuition $tuition */
        foreach($this->em->getRepository(Tuition::class)->findAll() as $tuition) {
            $key = $this->getSectionKey($tuition->getSection());

            if(!isset($tuitions[$key])) {
                $tuitions[$key] = [ ];
            }

            $tuitions[$key][] = (new TuitionData())
                ->setId($tuition->getExternalId())
                ->setName($tuition->getName())
                ->setDisplayName($tuition->getDisplayName())
                ->setSubject($tuition->getSubject()->getExternalId())
                ->setStudyGroup($tuition->getStudyGroup()->getExternalId())
                ->setTeachers($tuition->getTeachers()->map(fn(Teacher $teacher) => $teacher->getExternalId())->toArray());
        }

        foreach($sections as $section) {
            $data = $tuitions[$this->getSectionKey($section)] ?? [ ];

            $this->writeFile(
                sprintf('tuitions_%s.json', $this->getSectionKey($section)),
                (new TuitionsData())->setSection($section->getNumber())->setYear($section->getYear())->setTuitions($data)
            );

            $io->success(sprintf('%d Unterrichte exportiert (%s)', count($data), $section->getDisplayName()));
        }
    }

    private function exportExams(SymfonyStyle $io): void {
        $io->section('Exportiere Klausurplan');
        $exams = [ ];

        $startDate = null;
        $endDate = null;

        /** @var Exam $exam */
        foreach($this->em->getRepository(Exam::class)->findAll() as $exam) {
            if($exam->getExternalId() === null) {
                continue;
            }

            if($startDate === null || $startDate > $exam->getDate()) {
                $startDate = clone $exam->getDate();
            }

            if($endDate === null || $endDate < $exam->getDate()) {
                $endDate = clone $exam->getDate();
            }

            $supervisions = [ ];
            foreach($exam->getSupervisions() as $supervision) {
                $supervisions[$supervision->getLesson()] = $supervision->getTeacher()->getExternalId();
            }

            $exams[] = (new ExamData())
                ->setId($exam->getExternalId())
                ->setDate($exam->getDate())
                ->setLessonStart($exam->getLessonStart())
                ->setLessonEnd($exam->getLessonEnd())
                ->setRooms(array_values(array_fill($exam->getLessonStart(), $exam->getLessonEnd(), $exam->getRoom())))
                ->setStudents($exam->getStudents()->map(fn(Student $student) => $student->getExternalId())->toArray())
                ->setTuitions($exam->getTuitions()->map(function(Tuition $tuition) use ($exam) {
                    return (new ExamTuition())
                        ->setTeachers($tuition->getTeachers()->map(fn(Teacher $teacher) => $teacher->getExternalId())->toArray())
                        ->setGrades($tuition->getStudyGroup()->getGrades()->map(fn(Grade $grade) => $grade->getExternalId())->toArray())
                        ->setSubjectOrCourse($tuition->getName());
                })->toArray())
                ->setSupervisions(array_values($supervisions));
        }

        $data = (new ExamsData())->setStartDate($startDate)->setEndDate($endDate)->setExams($exams);
        $this->writeFile('exams.json', $data);

        $io->success(sprintf('%d Klausur(en) exportiert', count($exams)));
    }

    private function exportAppointmentCategories(SymfonyStyle $io): void {
        $io->section('Exportiere Terminkategorien');

        $categories = [ ];

        /** @var AppointmentCategory $category */
        foreach($this->em->getRepository(AppointmentCategory::class)->findAll() as $category) {
            $categories[] = (new AppointmentCategoryData())
                ->setId($category->getExternalId())
                ->setColor($category->getColor())
                ->setName($category->getName());
        }

        $data = (new AppointmentCategoriesData())->setCategories($categories);
        $this->writeFile('appointment_categories.json', $data);

        $io->success(sprintf('%d Terminkategorien exportiert', count($categories)));
    }

    private function exportAppointments(SymfonyStyle $io): void {
        $io->section('Exportiere Termine');
        $appointments = [ ];

        /** @var Appointment $appointment */
        foreach($this->em->getRepository(Appointment::class)->findAll() as $appointment) {
            if($appointment->getExternalId() === null) {
                continue;
            }

            $appointments[] = (new AppointmentData())
                ->setId($appointment->getExternalId())
                ->setStart($appointment->getStart())
                ->setEnd($appointment->getEnd())
                ->setContent($appointment->getContent())
                ->setIsAllDay($appointment->isAllDay())
                ->setLocation($appointment->getLocation())
                ->setSubject($appointment->getTitle())
                ->setCategory($appointment->getCategory()->getExternalId())
                ->setExternalOrganizers($appointment->getExternalOrganizers())
                ->setOrganizers($appointment->getOrganizers()->map(fn(Teacher $teacher) => $teacher->getExternalId())->toArray())
                ->setStudyGroups($appointment->getStudyGroups()->map(fn(StudyGroup $studyGroup) => $studyGroup->getExternalId())->toArray())
                ->setVisibilities($appointment->getVisibilities()->map(fn(UserTypeEntity $type) => $type->getUserType()->value)->toArray());
        }

        $data = (new AppointmentsData())->setAppointments($appointments);
        $this->writeFile('appointment.json', $data);

        $io->success(sprintf('%d Termine exportiert', count($appointments)));
    }

    private function exportTimetable(SymfonyStyle $io): void {
        $io->section('Exportiere Stundenplan');

        $timetable = [ ];

        /** @var TimetableLesson $lesson */
        foreach($this->em->getRepository(TimetableLesson::class)->findAll() as $lesson) {
            $timetable[] = (new TimetableLessonData())
                ->setId($lesson->getId())
                ->setDate($lesson->getDate())
                ->setLessonStart($lesson->getLessonStart())
                ->setLessonEnd($lesson->getLessonEnd())
                ->setRoom($lesson->getRoom() !== null ? $lesson->getRoom()->getExternalId() : $lesson->getLocation())
                ->setSubject($lesson->getSubjectName())
                ->setTeachers($lesson->getTeachers()->map(fn(Teacher $teacher) => $teacher->getExternalId())->toArray());
        }

        /** @var Section $section */
        foreach($this->em->getRepository(Section::class)->findAll() as $section) {
            $data = array_filter($timetable, fn(TimetableLessonData $lesson) => $lesson->getDate() >= $section->getStart() && $lesson->getDate() <= $section->getEnd());

            $this->writeFile(
                sprintf('timetable_lessons_%s.json', $this->getSectionKey($section)),
                (new TimetableLessonsData())->setStartDate($section->getStart())->setEndDate($section->getEnd())->setLessons($data)
            );

            $io->success(sprintf('%d Stundenplanstunden exportiert (%s)', count($data), $section->getDisplayName()));
        }
    }

    private function exportSupervisions(SymfonyStyle $io): void {
        $io->section('Exportiere Aufsichten');

        $supervisions = [ ];

        /** @var TimetableSupervision $supervision */
        foreach($this->em->getRepository(TimetableSupervision::class)->findAll() as $supervision) {
            $supervisions[] = (new TimetableSupervisionData())
                ->setId($supervision->getExternalId())
                ->setIsBefore($supervision->isBefore())
                ->setDate($supervision->getDate())
                ->setLesson($supervision->getLesson())
                ->setLocation($supervision->getLocation())
                ->setTeacher($supervision->getTeacher()->getExternalId());
        }

        /** @var Section $section */
        foreach($this->em->getRepository(Section::class)->findAll() as $section) {
            $data = array_filter($supervisions, fn(TimetableSupervisionData $supervision) => $supervision->getDate() >= $section->getStart() && $supervision->getDate() <= $section->getEnd());

            $this->writeFile(
                sprintf('timetable_supervisions_%s.json', $this->getSectionKey($section)),
                (new TimetableSupervisionsData())->setStartDate($section->getStart())->setEndDate($section->getEnd())->setSupervisions($supervisions)
            );

            $io->success(sprintf('%d Aufsichten exportiert (%s)', count($data), $section->getDisplayName()));
        }
    }
}