<?php

namespace App\Export;

use App\Csv\CsvHelper;
use App\Entity\Message;
use App\Entity\Tuition;
use App\Message\PollResultView;
use App\Message\PollResultViewHelper;
use App\Sorting\Sorter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class PollResultCsvExporter {
    private $viewHelper;
    private $csvHelper;
    private $translator;
    private $sorter;


    public function __construct(PollResultViewHelper $resultViewHelper, CsvHelper $csvHelper, TranslatorInterface $translator, Sorter $sorter) {
        $this->viewHelper = $resultViewHelper;
        $this->csvHelper = $csvHelper;
        $this->translator = $translator;
        $this->sorter = $sorter;
    }

    public function getRows(Message $message): array {
        $view = $this->viewHelper->createView($message);

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

        $rows[] = $header;

        foreach($view->getStudents() as $student) {
            $row = [
                $student->getExternalId(),
                $student->getFirstname(),
                $student->getLastname(),
                $student->getGrade() !== null ? $student->getGrade()->getName() : null,
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