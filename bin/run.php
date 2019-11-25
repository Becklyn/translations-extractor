<?php declare(strict_types=1);

use Becklyn\TranslationsExtractor\Extractor\TranslationExtractor;
use Becklyn\TranslationsExtractor\Extractor\Twig\MockExtension;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


var_dump(dirname(__DIR__) . "/vendor/autoload.php");
var_dump(dirname(__DIR__, 3) . "/autoload.php");
if (is_file($autoloader = dirname(__DIR__) . "/vendor/autoload.php"))
{
    require_once $autoloader;
}
else if (is_file($autoloader = dirname(__DIR__, 3) . "/autoload.php"))
{
    require_once $autoloader;
}

$commandName = "extract-translations";
$version = "0.0.1";

(new Application($commandName, $version))
    ->register($commandName)
    ->addArgument("directories", InputArgument::IS_ARRAY | InputArgument::REQUIRED, "The directories to extract the translations from")
    ->addOption("mock-functions", null, InputOption::VALUE_IS_ARRAY, "Twig functions to shim", [])
    ->addOption("mock-filters", null, InputOption::IS_ARRAY, "Twig filters to shim", [])
    ->addOption("mock-tests", null, InputOption::IS_ARRAY, "Twig tests to shim", [])
    ->setCode(
        function (InputInterface $input, OutputInterface $output)
        {
            $mockExtension = new MockExtension(
                $input->getOption("mock-functions"),
                $input->getOption("mock-filters"),
                $input->getOption("mock-tests")
            );

            $extractor = new TranslationExtractor($mockExtension);
            $output->writeln(json_encode($extractor->extract($input->getArgument("directory"))));
            return 0;
        }
    )
    ->getApplication()
    ->setDefaultCommand($commandName, true)
    ->run();
