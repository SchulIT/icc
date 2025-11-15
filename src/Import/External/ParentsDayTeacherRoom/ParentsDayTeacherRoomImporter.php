<?php

namespace App\Import\External\ParentsDayTeacherRoom;

use App\Entity\ParentsDayTeacherRoom;
use App\Entity\Room;
use App\Entity\Teacher;
use App\Repository\ParentsDayTeacherRoomRepositoryInterface;
use App\Repository\RoomRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Utils\ArrayUtils;
use League\Csv\Reader;

readonly class ParentsDayTeacherRoomImporter {
    public function __construct(
        private ParentsDayTeacherRoomRepositoryInterface $repository,
        private TeacherRepositoryInterface $teacherRepository,
        private RoomRepositoryInterface $roomRepository
    ) { }

    public function import(ImportRequest $request): ImportResult {
        $this->repository->beginTransaction();
        $this->repository->removeByParentsDay($request->parentsDay);

        $reader = Reader::fromString($request->csv->getContent());
        $reader->setHeaderOffset(0);
        $reader->setDelimiter($request->delimiter);
        $reader->setEscape('');

        $teachers = ArrayUtils::createArrayWithKeys(
            $this->teacherRepository->findAll(),
            fn(Teacher $teacher): string => $teacher->getAcronym()
        );

        $rooms = ArrayUtils::createArrayWithKeys(
            $this->roomRepository->findAll(),
            fn(Room $room): string => $room->getExternalId()
        );

        $importCount = 0;
        $ignoredTeachers = [ ];
        $ignoredRooms = [ ];
        $alreadyAddedTeachers = [ ]; // prevent duplicates

        foreach($reader->getRecords() as $record) {
            $teacherAcronym = $record[$request->teacherHeader];
            $roomId = $record[$request->roomHeader];

            $teacher = null;
            $room = null;

            if(in_array($teacherAcronym, $alreadyAddedTeachers)) {
                continue;
            }

            if(!empty($teacherAcronym)) {
                $teacher = $teachers[$teacherAcronym] ?? null;
            }

            if(!empty($roomId)) {
                $room = $rooms[$roomId] ?? null;
            }

            if($room === null || $teacher === null) {
                $ignoredRooms[] = $roomId;
                $ignoredTeachers[] = $teacherAcronym;

                continue;
            }

            $teacherRoom = (new ParentsDayTeacherRoom())
                ->setParentsDay($request->parentsDay)
                ->setTeacher($teacher)
                ->setRoom($room);

            $alreadyAddedTeachers[] = $teacherAcronym;

            $this->repository->persist($teacherRoom);
            $importCount++;
        }

        $this->repository->commit();

        return new ImportResult($ignoredTeachers, $ignoredRooms, $importCount);
    }
}