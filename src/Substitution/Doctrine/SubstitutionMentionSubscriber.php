<?php

namespace App\Substitution\Doctrine;

use App\Substitution\Entity\Substitution;
use App\Common\Entity\Teacher;
use App\Substitution\Event\SubstitutionMentionCreatedEvent;
use App\Infrastructure\EventSubscriber\DoctrineEventsCollector;
use App\Common\Repository\TeacherRepositoryInterface;
use App\Framework\Utils\ArrayUtils;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::prePersist)]
class SubstitutionMentionSubscriber  {

    /**
     * @var array<string, Teacher>
     */
    private array $teachersMap = [ ];
    private ?string $regExp = null;

    public function __construct(private readonly TeacherRepositoryInterface $teacherRepository,
                                private readonly DoctrineEventsCollector $collector) {

    }

    private function initialize(): void {
        if(count($this->teachersMap) > 0) {
            return;
        }

        $this->teachersMap = ArrayUtils::createArrayWithKeys(
            $this->teacherRepository->findAll(),
            fn(Teacher $teacher) => $teacher->getAcronym()
        );

        $this->regExp = '~(?<=\W|^)' . implode('|', array_keys($this->teachersMap)) . '(?=\W|$)~m';
    }

    public function prePersist(PrePersistEventArgs $args): void {
        $substitution = $args->getObject();

        if(!$substitution instanceof Substitution) {
            return;
        }

        $this->initialize();

        if(empty($this->regExp)) {
            return;
        }

        if(empty($substitution->getRemark())) {
            return;
        }

        if(preg_match_all($this->regExp, $substitution->getRemark(), $matches) > 0) {
            $acronyms = array_unique($matches[0]);

            foreach($acronyms as $acronym) {
                $teacher = $this->teachersMap[$acronym];

                $this->collector->collect(new SubstitutionMentionCreatedEvent($substitution, $teacher));
            }
        }
    }
}