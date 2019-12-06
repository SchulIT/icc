<?php

require_once __DIR__ . '/../config/bootstrap.php';

$kernel = new \App\Kernel($_SERVER['APP_ENV'], $_SERVER['APP_DEBUG']);
$kernel->boot();

$application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
$application->setAutoExit(false);

$application->run(new \Symfony\Component\Console\Input\ArrayInput([
    'command' => 'doctrine:database:drop',
    '--if-exists' => '1',
    '--force' => '1',
]));
$application->run(new \Symfony\Component\Console\Input\ArrayInput([
    'command' => 'doctrine:database:create'
]));
$application->run(new \Symfony\Component\Console\Input\ArrayInput([
    'command' => 'doctrine:schema:create'
]));

$kernel->shutdown();