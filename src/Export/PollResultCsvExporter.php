<?php

namespace App\Export;

use App\Csv\CsvHelper;
use App\Entity\Message;
use App\Entity\Tuition;
use App\Entity\UserType;
use App\Entity\UserTypeEntity;
use App\Message\PollResultView;
use App\Message\PollResultViewHelper;
use App\Section\SectionResolverInterface;
use App\Sorting\Sorter;
use App\Utils\ArrayUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class PollResultCsvExporter {
    public function __construct(private PollResultViewHelper $viewHelper, private CsvHelper $csvHelper, private TranslatorInterface $translator, private SectionResolverInterface $sectionResolver)
    {
    }

    public function getRows(Message $message): array {
        $rows = [];
        $view = $this->viewHelper->createView($message);

        /** @var UserType[] $visibilities */
        $visibilities = $message->getPollUserTypes()->map(fn(UserTypeEntity $visibility) => $visibility->getUserType());
        $isParentsPollEnabled = ArrayUtils::inArray(UserType::Parent, $visibilities);

        $header = [
            $this->translator->trans('label.external_id'),
            $this->translator->trans('label.firstname'),
            $this->translator->trans('label.lastname'),
            $this->translator->trans('label.grade'),
            $this->translator->trans('label.email')
        ];

        for($i = 1; $i <= $message->getPollNumChoices(); $i++) {
            $header[] = $this->translator->trans('messages.poll.choice', ['%rank%' => $i ]);
        }

        if($isParentsPollEnabled) {
            for($i = 1; $i <= $message->getPollNumChoices(); $i++) {
                $header[] = $this->translator->trans('messages.poll.choice', ['%rank%' => $i ]) . ' [' . $this->translator->trans('user_type.parent', [], 'enums') . ']';
            }
        }

        $rows[] = $header;

        $section = $this->sectionResolver->getCurrentSection();
        foreach($view->getStudents() as $student) {
            $grade = $student->getGrade($section);

            $row = [
                $student->getExternalId(),
                $student->getFirstname(),
                $student->getLastname(),
                $grade !== null ? $grade->getName() : null,
                $student->getEmail()
            ];

            for($i = 1; $i <= $message->getPollNumChoices(); $i++) {
                $vote = $view->getStudentVote($student);

                if($vote !== null) {
                    for($i = 1; $i <= $message->getPollNumChoices(); $i++) {
                        $choice = $vote->getChoice($i - 1);
                        $row[] = $choice !== null ? $choice->getChoice()->getLabel() : null;
                    }
                } else {
                    for($i = 1; $i <= $message->getPollNumChoices(); $i++) {
                        $row[] = null;
                    }
                }
            }

            if($isParentsPollEnabled) {
                for($i = 1; $i <= $message->getPollNumChoices(); $i++) {
                    $vote = $view->getParentVote($student);

                    if($vote !== null) {
                        for($i = 1; $i <= $message->getPollNumChoices(); $i++) {
                            $choice = $vote->getChoice($i - 1);
                            $row[] = $choice !== null ? $choice->getChoice()->getLabel() : null;
                        }
                    } else {
                        for($i = 1; $i <= $message->getPollNumChoices(); $i++) {
                            $row[] = null;
                        }
                    }
                }
            }

            $rows[] = $row;
        }

        foreach($view->getTeachers() as $teacher) {
            $row = [
                $teacher->getExternalId(),
                $teacher->getFirstname(),
                $teacher->getLastname(),
                null,
                $teacher->getEmail()
            ];

            for($i = 1; $i <= $message->getPollNumChoices(); $i++) {
                $vote = $view->getTeacherVote($teacher);

                if($vote !== null) {
                    for($i = 1; $i <= $message->getPollNumChoices(); $i++) {
                        $choice = $vote->getChoice($i - 1);
                        $row[] = $choice !== null ? $choice->getChoice()->getLabel() : null;
                    }
                } else {
                    for($i = 1; $i <= $message->getPollNumChoices(); $i++) {
                        $row[] = null;
                    }
                }
            }

            $rows[] = $row;
        }

        return $rows;
    }

    public function getCsvResponse(Message $message): Response {
        return $this->csvHelper->getCsvResponse(
            'poll.csv',
            $this->getRows($message)
        );
    }
}