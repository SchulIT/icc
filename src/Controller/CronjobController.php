<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;
use Shapecode\Bundle\CronBundle\Entity\CronJob;
use Shapecode\Bundle\CronBundle\Entity\CronJobResult;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class CronjobController extends AbstractController {

    /**
     * @IsGranted("ROLE_CRON")
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

        return $this->render('admin/cron/output.html.twig', [
            'output' => $converter->convert($content)
        ]);
    }

    /**
     * @Route("/admin/cron", name="admin_cronjobs")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(EntityManagerInterface $manager) {
        $jobs = $manager->getRepository(CronJob::class)
            ->findAll();

        /** @var CronJobResult[] $results */
        $results = [ ];

        foreach($jobs as $job) {
            $results[$job->getCommand()] = $manager->getRepository(CronJobResult::class)
                ->findMostRecent($job);
        }

        return $this->render('admin/cron/index.html.twig', [
            'jobs' => $jobs,
            'results' => $results
        ]);
    }

    /**
     * @Route("/admin/cron/{id}", name="show_cronjob")
     * @IsGranted("ROLE_ADMIN")
     */
    public function showJob(CronJob $job, EntityManagerInterface $manager) {
        $results = $manager->getRepository(CronJobResult::class)
            ->createQueryBuilder('r')
            ->leftJoin('r.cronJob', 'c')
            ->where('c.id = :job')
            ->orderBy('r.createdAt', 'desc')
            ->setParameter('job', $job->getId())
            ->getQuery()
            ->getResult();

        return $this->render('admin/cron/show.html.twig', [
            'job' => $job,
            'results' => $results
        ]);
    }

    /**
     * @Route("/admin/cron/{job}/result/{id}", name="show_cronresult")
     * @IsGranted("ROLE_ADMIN")
     */
    public function showResult(CronJobResult $result) {
        return $this->render('admin/cron/result.html.twig', [
            'result' => $result
        ]);
    }
}