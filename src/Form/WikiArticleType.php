<?php

namespace App\Form;

use App\Converter\EnumStringConverter;
use App\Entity\WikiArticle;
use App\Repository\WikiArticleRepositoryInterface;
use App\Wiki\TreeHelper;
use SchulIT\CommonBundle\Form\FontAwesomeIconPicker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class WikiArticleType extends AbstractType {

    public function __construct(private readonly TreeHelper $treeHelper, private readonly WikiArticleRepositoryInterface $wikiRepository, private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $articles = $this->wikiRepository->findAll();

        $builder
            ->add('title', TextType::class, [
                'label' => 'label.title'
            ])
            ->add('icon', FontAwesomeIconPicker::class, [
                'label' => 'label.icon.label',
                'help' => 'label.icon.help',
                'required' => false
            ])
            ->add('parent', ChoiceType::class, [
                'label' => 'label.parent',
                'choices' => $this->treeHelper->flattenTree($articles),
                'placeholder'=> 'label.none',
                'required' => false,
                'attr' => [
                    'data-choice' => 'true'
                ]
            ])
            ->add('isOnline', ChoiceType::class, [
                'label' => 'label.status',
                'choices' => [
                    'label.online' => true,
                    'label.offline' => false
                ],
                'expanded' => true,
                'label_attr' => [
                    'class' => 'radio-custom'
                ]
            ])
            ->add('visibilities', UserTypeEntityType::class, [
                'label' => 'label.visibility',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('content', MarkdownType::class, [
                'label' => 'label.content',
                'upload_enabled' => true,
                'upload_url' => $this->urlGenerator->generate('wiki_upload')
            ]);

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use($articles) {
            /** @var WikiArticle|null $article */
            $article = $event->getData();

            $form = $event->getForm();

            if($article !== null && $article->getId() !== null) {
                $form->add('parent', ChoiceType::class, [
                    'label' => 'label.parent',
                    'choices' => $this->treeHelper->flattenTree($articles, true, $article),
                    'placeholder'=> 'label.none',
                    'required' => false,
                    'attr' => [
                        'data-choice' => 'true'
                    ]
                ]);
            }
        });
    }
}