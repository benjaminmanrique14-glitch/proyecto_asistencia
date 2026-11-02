<?php

use App\Kernel;
use App\Entity\Administrador;
use App\Entity\Profesor;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();
$container = $kernel->getContainer();
$em = $container->get('doctrine')->getManager();
$hasher = $container->get('security.user_password_hasher');

$adminPass = 'EpetAdmin2026';
$profPass = 'EpetProf2026';

// Admin principal
$admin = $em->getRepository(Administrador::class)->findOneBy(['username' => 'admin']);
if (!$admin) {
    $admin = new Administrador();
    $admin->setUsername('admin');
    $em->persist($admin);
}
$admin->setPassword($hasher->hashPassword($admin, $adminPass));

// Segundo admin
$director = $em->getRepository(Administrador::class)->findOneBy(['username' => 'director']);
if (!$director) {
    $director = new Administrador();
    $director->setUsername('director');
    $em->persist($director);
}
$director->setPassword($hasher->hashPassword($director, $adminPass));

// Profesor Laura Benítez
$profesor = $em->getRepository(Profesor::class)->findOneBy(['dni' => '28000001']);
if (!$profesor) {
    fwrite(STDERR, "No se encontró el profesor 28000001\n");
    exit(1);
}
$profesor->setPassword($hasher->hashPassword($profesor, $profPass));
$profesor->setIsActive(true);

// Profesor Demo
$demo = $em->getRepository(Profesor::class)->findOneBy(['dni' => '30111222']);
if ($demo) {
    $demo->setPassword($hasher->hashPassword($demo, $profPass));
    $demo->setIsActive(true);
}

$em->flush();

echo "OK\n";
echo "Admin: admin / {$adminPass}\n";
echo "Admin: director / {$adminPass}\n";
echo "Ingreso: 28000001 / {$profPass} (Laura Benítez)\n";
echo "Ingreso: 30111222 / {$profPass} (Demo Docente)\n";
