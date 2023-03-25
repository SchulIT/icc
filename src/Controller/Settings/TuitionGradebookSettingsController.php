<?php

namespace App\Controller\Settings;

use App\Controller\AbstractController;
use App\Settings\TuitionGradebookSettings;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/settings')]
#[Security("is_granted('ROLE_ADMIN')")]
class TuitionGradebookSettingsController extends AbstractController {

    #[Route('/gradebook', name: 'admin_settings_gradebook')]
    public function gradebook(Request $request, TuitionGradebookSettings $settings): Response {
        $builder = $this->createFormBuilder();
        $builder
            ->add('confirm', CheckboxType::class, [
                'required' => true,
                'label' => 'admin.settings.tuition_grades.confirm.label',
                'help' => 'admin.settings.tuition_grades.confirm.help',
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('masterKey', TextType::class, [
                'required' => false,
                'data' => $settings->getEncryptedMasterKey(),
                'label' => 'admin.settings.tuition_grades.masterkey.label',
                'help' => 'admin.settings.tuition_grades.masterkey.help'
            ]);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $settings->setEncryptedMasterKey($form->get('masterKey')->getData());
            $this->addFlash('success', 'admin.settings.success');

            return $this->redirectToRoute('admin_settings_gradebook');
        }

        return $this->render('admin/settings/tuition_grades.html.twig', [
            'form' => $form->createView()
        ]);
    }
}