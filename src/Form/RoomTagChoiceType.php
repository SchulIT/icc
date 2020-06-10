<?php

namespace App\Form;

use App\Entity\RoomTagInfo;
use App\Repository\RoomTagRepositoryInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

class RoomTagChoiceType extends AbstractType implements DataMapperInterface {

    private $tagRepository;

    public function __construct(RoomTagRepositoryInterface $tagRepository) {
        $this->tagRepository = $tagRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        foreach($this->tagRepository->findAll() as $tag) {
            $builder
                ->add(sprintf('tag-%s', $tag->getId()), CheckboxType::class, [
                    'required' => false,
                    'label' => $tag->getName(),
                    'help' => $tag->getDescription(),
                    'label_attr' => [
                        'class' => 'checkbox-custom'
                    ]
                ]);

            if($tag->hasValue()) {
                $builder
                    ->add(sprintf('tag-%s-value', $tag->getId()), IntegerType::class, [
                        'label' => $tag->getName(),
                        'required' => false
                    ]);
            }
        }

        $builder
            ->setDataMapper($this);
    }

    /**
     * @inheritDoc
     */
    public function mapDataToForms($viewData, $forms) {
        if(!is_iterable($viewData)) {
            throw new UnexpectedTypeException($viewData, 'array');
        }

        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        /** @var RoomTagInfo $tagInfo */
        foreach($viewData as $tagInfo) {
            if(!$tagInfo instanceof RoomTagInfo) {
                throw new UnexpectedTypeException($tagInfo, RoomTagInfo::class);
            }

            $id = sprintf('tag-%s', $tagInfo->getTag()->getId());

            if(isset($forms[$id])) {
                $forms[$id]->setData(true);
            }

            $valueId = sprintf('tag-%s-value', $tagInfo->getTag()->getId());

            if($tagInfo->getTag()->hasValue() && isset($forms[$valueId])) {
                $forms[$valueId]->setData($tagInfo->getValue());
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function mapFormsToData($forms, &$viewData) {
        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        /** @var Collection $viewData */

        $existingTagInfos = [ ];

        /**
         * @var int $idx
         * @var RoomTagInfo $tagInfo
         */
        foreach($viewData as $idx => $tagInfo) {
            $existingTagInfos[$tagInfo->getTag()->getId()] = $tagInfo;
        }

        /** @var RoomTagInfo $toBeRemoved */
        $toBeRemoved = [ ];

        foreach($this->tagRepository->findAll() as $tag) {
            $id = sprintf('tag-%s', $tag->getId());
            $valueId = sprintf('tag-%s-value', $tag->getId());
            $ensureAdded = $forms[$id]->getData();

            if($ensureAdded) { // Case 1: Tag must be added/updated
                $tagInfo = $existingTagInfos[$tag->getId()] ?? null;

                if($tagInfo === null) {
                    $tagInfo = new RoomTagInfo();
                    $tagInfo->setTag($tag);

                    $viewData->add($tagInfo);
                }

                if($tag->hasValue()) {
                    $tagInfo->setValue(0); // In case no value was provided
                    $value = $forms[$valueId]->getData();

                    if($value !== null && is_numeric($value)) {
                        $tagInfo->setValue($value);
                    }
                }
            } else { // Case 2: Tag is removed
                $existingTagInfo = $existingTagInfos[$tag->getId()] ?? null;

                if($existingTagInfo !== null) {
                    $viewData->removeElement($existingTagInfo);
                }
            }
        }
    }
}