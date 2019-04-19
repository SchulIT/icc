<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MarkdownType extends TextareaType {

    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator) {
        $this->urlGenerator = $urlGenerator;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver
            ->setDefault('upload_enabled', true)
            ->setDefault('upload_url', $this->urlGenerator->generate('markdown_upload', [], UrlGeneratorInterface::ABSOLUTE_PATH))
            ->setDefault('preview_url', $this->urlGenerator->generate('markdown_preview', [], UrlGeneratorInterface::ABSOLUTE_PATH));
    }

    public function buildView(FormView $view, FormInterface $form, array $options) {
        parent::buildView($view, $form, $options);

        $view->vars['attr'] = [
            'data-editor' => 'markdown',
            'data-language' => 'de',
            'data-upload' => $options['upload_enabled'],
            'data-url' => $options['upload_url'],
            'data-preview' => $options['preview_url']
        ];
    }
}