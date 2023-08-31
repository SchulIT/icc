<?php

namespace App\Form;

use App\Settings\TimetableSettings;
use App\Entity\DateLesson;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class DateLessonType extends AbstractType implements DataMapperInterface {

    public function __construct(private readonly TimetableSettings $timetableSettings, private readonly TranslatorInterface $translator)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void {
        parent::configureOptions($resolver);

        $resolver->setDefault('compound', true);
        $resolver->setDefault('error_bubbling', false);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'label.date'
            ])
            ->add('lesson', ChoiceType::class, [
                'choices' => $this->getChoices(),
                'label' => 'label.lesson',
                'attr' => [
                    'class' => 'custom-select'
                ]
            ])
            ->setDataMapper($this);
    }

    private function getChoices(): array {
        $choices = [ ];

        for($lesson = 1; $lesson <= $this->timetableSettings->getMaxLessons(); $lesson++) {
            $label = sprintf('%s (%s - %s)',
                $this->translator->trans('label.exam_lessons', [
                    '%start%' => $lesson,
                    '%count%' => 0
                ]),
                $this->timetableSettings->getStart($lesson),
                $this->timetableSettings->getEnd($lesson)
            );

            $choices[$label] = $lesson;
        }

        return $choices;
    }

    /**
     * @inheritDoc
     */
    public function mapDataToForms($viewData, $forms) {
        if($viewData === null) {
            return;
        }

        if(!$viewData instanceof DateLesson) {
            throw new UnexpectedTypeException($viewData, DateLesson::class);
        }

        $forms = iterator_to_array($forms);

        $forms['date']->setData($viewData->getDate());
        $forms['lesson']->setData($viewData->getLesson());
    }

    /**
     * @inheritDoc
     */
    public function mapFormsToData($forms, &$viewData) {
        $forms = iterator_to_array($forms);

        $viewData = new DateLesson();
        $viewData->setDate($forms['date']->getData());
        $viewData->setLesson($forms['lesson']->getData());
    }
}