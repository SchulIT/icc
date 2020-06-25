<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_CRON")
 */
class CronjobController extends AbstractController {

    /**
     * @Route("/cron", methods={"GET"})
     */
    public function run(KernelInterface $kernel) {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new StringInput('shapecode:cron:run');

        $output = new BufferedOutput(
            OutputInterface::VERBOSITY_NORMAL,
            true
        );
        $application->run($input, $output);

        $content = $output->fetch();
        $converter = new AnsiToHtmlConverter();

        return $this->render('cron/output.html.twig', [
            'output' => $converter->convert($content)
        ]);
    }
}