<?php declare(strict_types=1);

use Becklyn\TranslationsExtractor\Extractor\TranslationExtractor;
use Becklyn\TranslationsExtractor\Extractor\Twig\MockExtension;
use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/** @var ClassLoader|null $loader */
$loader = null;

if (is_file($autoloaderPath = dirname(__DIR__) . "/vendor/autoload.php"))
{
    $loader = include $autoloaderPath;
}
else if (is_file($autoloaderPath = dirname(__DIR__, 3) . "/autoload.php"))
{
    $loader = include $autoloaderPath;
}

if (null === $loader)
{
    throw new RuntimeException("Could not determine library composer autoload structure");
}

// include project's autoloader
// this mirrors the structure of projects using the `composer-bin-plugin`
if (is_file($projectAutoloader = dirname(__DIR__, 6) . "/vendor/composer/autoload_psr4.php"))
{
    $projectVendors = include $projectAutoloader;

    foreach ($projectVendors as $prefix => $paths)
    {
        $loader->addPsr4($prefix, $paths);
    }
}

// register autoloader for annotations
AnnotationRegistry::registerLoader([$loader, "loadClass"]);

$commandName = "extract-translations";
$version = "0.0.1";

(new Application($commandName, $version))
    ->register($commandName)
    ->addArgument("directories", InputArgument::IS_ARRAY | InputArgument::REQUIRED, "The directories to extract the translations from")
    ->addOption("mock-functions", null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, "Twig functions to shim", [])
    ->addOption("mock-filters", null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, "Twig filters to shim", [])
    ->addOption("mock-tests", null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, "Twig tests to shim", [])
    ->setCode(
        function (InputInterface $input, OutputInterface $output)
        {
            $mockExtension = new MockExtension(
                $input->getOption("mock-functions"),
                $input->getOption("mock-filters"),
                $input->getOption("mock-tests")
            );

            $extractor = new TranslationExtractor($mockExtension);
            $output->writeln(json_encode($extractor->extract($input->getArgument("directories"))));
            return 0;
        }
    )
    ->getApplication()
    ->setDefaultCommand($commandName, true)
    ->run();
