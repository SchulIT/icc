<?php

namespace App\Appointment\Import\OpenHolidaysApi;

use App\Appointment\Entity\Appointment;
use App\Appointment\External\OpenHolidaysClient\Client;
use App\Appointment\External\OpenHolidaysClient\Model\HolidayResponse;
use App\Appointment\External\OpenHolidaysClient\Model\ProblemDetails;
use App\Appointment\Repository\AppointmentCategoryRepositoryInterface;
use App\Appointment\Repository\AppointmentRepositoryInterface;
use App\Appointment\Settings\AppointmentsSettings;
use App\Common\Entity\Section;
use App\Common\Entity\StudyGroup;
use App\Common\Entity\StudyGroupType;
use App\Common\Repository\SectionRepositoryInterface;
use App\Common\Repository\StudyGroupRepositoryInterface;
use App\Common\Section\SectionResolverInterface;
use App\Framework\Import\Settings\ImportSettings;
use App\Framework\Utils\CollectionUtils;
use DateTime;

readonly class Importer {
    public function __construct(
        private Client $client,
        private AppointmentRepositoryInterface $appointmentRepository,
        private AppointmentCategoryRepositoryInterface $appointmentCategoryRepository,
        private StudyGroupRepositoryInterface $studyGroupRepository,
        private SectionResolverInterface $sectionResolver,
        private SectionRepositoryInterface $sectionRepository,
        private AppointmentsSettings $settings,
        private ImportSettings $importSettings
    ) {

    }

    public function import(ImportRequest $request): ImportResult {
        if($this->settings->getImportAppointmentCategoryId() === null) {
            return ImportResult::fromZero();
        }

        $category = $this->appointmentCategoryRepository->findOneById($this->settings->getImportAppointmentCategoryId());

        if($category === null) {
            return ImportResult::fromZero();
        }

        $publicHolidays = $this->client->publicHolidays(
            $this->settings->getImportCountry(),
            $request->start,
            $request->end,
            'DE',
            $this->settings->getImportSubdivision()
        );

        $schoolHolidays = $this->client->schoolHolidays(
            $this->settings->getImportCountry(),
            $request->start,
            $request->end,
            'DE',
            $this->settings->getImportSubdivision()
        );

        if($publicHolidays instanceof ProblemDetails || $schoolHolidays instanceof ProblemDetails) {
            // TODO: LOG

            return ImportResult::fromZero();
        }

        /** @var HolidayResponse[] $holidays */
        $holidays = array_merge($publicHolidays, $schoolHolidays);

        $added = 0;
        $updated = 0;

        $this->appointmentRepository->beginTransaction();

        foreach($holidays as $holiday) {
            $entity = $this->appointmentRepository->findOneByExternalId($holiday->id);

            if($entity === null) {
                $entity = (new Appointment())->setExternalId($holiday->id);
                $added++;
            } else {
                $updated++;
            }

            $entity->setAllDay(true);
            $entity->setStart($holiday->startDate);
            $entity->setEnd($holiday->endDate);
            $entity->setIsConfirmed(true);
            $entity->setCategory($category);

            $name = array_first($holiday->name);
            $entity->setTitle($name->text);

            $section = $this->getSectionForDate($holiday->startDate);

            if($section !== null) {
                $studyGroups = $this->studyGroupRepository->findAllByType(StudyGroupType::Grade, $section);
                CollectionUtils::synchronize(
                    $entity->getStudyGroups(),
                    $studyGroups,
                    fn(StudyGroup $studyGroup): int => $studyGroup->getId()
                );
            }

            $this->appointmentRepository->persist($entity);
        }

        $this->appointmentRepository->commit();

        return new ImportResult($added, $updated);
    }

    private function getSectionForDate(DateTime $dateTime): Section|null {
        $section = $this->sectionResolver->getSectionForDate($dateTime);

        if($section === null && $this->importSettings->getFallbackSection() !== null) {
            return $this->sectionRepository->findOneById($this->importSettings->getFallbackSection());
        }

        return $section;
    }
}
