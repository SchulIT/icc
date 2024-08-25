<?php

namespace App\Controller\Settings;

use App\Converter\EnumStringConverter;
use App\Entity\UserType;
use App\Settings\ChatSettings;
use App\Utils\ArrayUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/settings')]
#[Security("is_granted('ROLE_ADMIN')")]
class ChatSettingsController extends AbstractController {

    #[Route(path: '/chat', name: 'admin_settings_chat')]
    public function chat(Request $request, ChatSettings $chatSettings, EnumStringConverter $enumStringConverter): Response {
        $userTypeChoices = ArrayUtils::createArray(array_map(fn(UserType $case) => $case->name, UserType::cases()), UserType::cases());
        $userTypeChoiceLabel = fn(UserType $userType) => $enumStringConverter->convert($userType);
        $userTypeChoiceValue = fn(UserType $userType) => $userType->value;

        $builder = $this->createFormBuilder();
        $builder
            ->add('chat_enabled', ChoiceType::class, [
                'choices' => $userTypeChoices,
                'choice_label' => $userTypeChoiceLabel,
                'choice_value' => $userTypeChoiceValue,
                'expanded' => true,
                'multiple' => true,
                'label' => 'admin.settings.chat.enabled.label',
                'help' => 'admin.settings.chat.enabled.help',
                'data' => $chatSettings->getEnabledUserTypes()
            ]);

        foreach(UserType::cases() as $userType) {
            $builder
                ->add(sprintf('%s_recipients', $userType->value), ChoiceType::class, [
                    'choices' => $userTypeChoices,
                    'choice_label' => $userTypeChoiceLabel,
                    'choice_value' => $userTypeChoiceValue,
                    'expanded' => true,
                    'multiple' => true,
                    'label' => 'admin.settings.chat.recipients.label',
                    'label_translation_parameters' => [
                        '%type%' => $enumStringConverter->convert($userType)
                    ],
                    'help' => 'admin.settings.chat.recipients.help',
                    'help_translation_parameters' => [
                        '%type%' => $enumStringConverter->convert($userType)
                    ],
                    'data' => $chatSettings->getAllowedRecipients($userType)
                ]);
        }

        $builder
            ->add('view_read_confirmations', ChoiceType::class, [
                'choices' => $userTypeChoices,
                'choice_label' => $userTypeChoiceLabel,
                'choice_value' => $userTypeChoiceValue,
                'expanded' => true,
                'multiple' => true,
                'label' => 'admin.settings.chat.view_read_confirmations.label',
                'help' => 'admin.settings.chat.view_read_confirmations.help',
                'data' => $chatSettings->getUserTypesAllowedToSeeReadConfirmations()
            ]);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $chatSettings->setEnabledUserTypes($form->get('chat_enabled')->getData());
            $chatSettings->setUserTypesAllowedToSeeReadConfirmations($form->get('view_read_confirmations')->getData());

            foreach(UserType::cases() as $userType) {
                $allowed = $form->get(sprintf('%s_recipients', $userType->value))->getData();
                $chatSettings->setAllowedRecipients($userType, $allowed);
            }

            $this->addFlash('success', 'admin.settings.success');

            return $this->redirectToRoute('admin_settings_chat');
        }

        return $this->render('admin/settings/chat.html.twig', [
            'form' => $form->createView()
        ]);
    }
}