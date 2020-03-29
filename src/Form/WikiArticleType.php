<?php

namespace App\Form;

use App\Converter\EnumStringConverter;
use App\Entity\WikiArticle;
use App\Repository\WikiArticleRepositoryInterface;
use App\Wiki\TreeHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class WikiArticleType extends AbstractType {

    private $treeHelper;
    private $wikiRepository;
    private $urlGenerator;

    public function __construct(TreeHelper $treeHelper, WikiArticleRepositoryInterface $wikiRepository, UrlGeneratorInterface $urlGenerator) {
        $this->treeHelper = $treeHelper;
        $this->wikiRepository = $wikiRepository;
        $this->urlGenerator = $urlGenerator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $articles = $this->wikiRepository->findAll();

        $builder
            ->add('title', TextType::class, [
                'label' => 'label.title'
            ])
            ->add('parent', ChoiceType::class, [
                'label' => 'label.parent',
                'choices' => $this->treeHelper->flattenTree($articles),
                'placeholder'=> 'label.none',
                'required' => false
            ])
            ->add('isOnline', ChoiceType::class, [
                'label' => 'label.status',
                'choices' => [
                    'label.online' => true,
                    'label.offline' => false
                ],
                'expanded' => true
            ])
            ->add('visibilities', UserTypeEntityType::class, [
                'label' => 'label.visibility',
                'multiple' => true,
                'expanded' => true,
                'required' => false
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
                    'required' => false
                ]);
            }
        });
    }
}