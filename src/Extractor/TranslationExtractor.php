<?php declare(strict_types=1);

namespace Becklyn\TranslationsExtractor\Extractor;

use Becklyn\TranslationsExtractor\Extractor\Integration\NameResolverIntegration;
use Becklyn\TranslationsExtractor\Extractor\Visitor\BackendTranslatorVisitor;
use Becklyn\TranslationsExtractor\Extractor\Visitor\CallbackValidationVisitor;
use Becklyn\TranslationsExtractor\Extractor\Visitor\ClassValidationVisitor;
use Becklyn\TranslationsExtractor\Extractor\Visitor\ConstructorParameterVisitor;
use Becklyn\TranslationsExtractor\Extractor\Visitor\CustomConstraintDefaultMessagesVisitor;
use Becklyn\TranslationsExtractor\Extractor\Visitor\FormOptionLabelsVisitor;
use Becklyn\TranslationsExtractor\Extractor\Visitor\PropertyValidationVisitor;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\Finder\Finder;
use Translation\Extractor\Extractor;
use Translation\Extractor\FileExtractor\FileExtractor;
use Translation\Extractor\FileExtractor\PHPFileExtractor;
use Translation\Extractor\FileExtractor\TwigFileExtractor;
use Translation\Extractor\Model\SourceCollection;
use Translation\Extractor\Model\SourceLocation;
use Translation\Extractor\Visitor\Php\Symfony\ContainerAwareTrans;
use Translation\Extractor\Visitor\Php\Symfony\ContainerAwareTransChoice;
use Translation\Extractor\Visitor\Php\Symfony\FlashMessage;
use Translation\Extractor\Visitor\Php\Symfony\FormTypeChoices;
use Translation\Extractor\Visitor\Twig\TwigVisitorFactory;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Loader\FilesystemLoader;

class TranslationExtractor
{
    /**
     * @var AbstractExtension
     */
    private $mockExtension;


    /**
     */
    public function __construct (AbstractExtension $mockExtension)
    {
        $this->mockExtension = $mockExtension;
    }

    /**
     * @return string
     */
    public function extract (array $dirs) : array
    {
        if (empty($dirs))
        {
            return new SourceCollection();
        }

        $extractor = new Extractor();
        $extractor->addFileExtractor($this->createPhpExtractor());
        $extractor->addFileExtractor($this->createTwigExtractor($dirs));

        $messages = [];
        /** @var SourceLocation $location */
        foreach ($extractor->extract($this->createFinder($dirs)) as $location)
        {
            $domain = $location->getContext()["domain"] ?? "messages";
            $messages[$domain][] = $location->getMessage();
        }

        return $this->dedupeMessages($messages);
    }


    /**
     * Dedupes the messages
     */
    private function dedupeMessages (array $domains) : array
    {
        $result = [];

        foreach ($domains as $group => $messages)
        {
            $filtered = [];

            foreach ($messages as $message)
            {
                // filter null / empty messages
                if (!$message)
                {
                    continue;
                }

                $filtered[$message] = true;
            }

            if (!empty($filtered))
            {
                $result[$group] = \array_keys($filtered);
            }
        }

        return $result;
    }


    /**
     *
     */
    private function createPhpExtractor () : FileExtractor
    {
        $fileExtractor = new PHPFileExtractor();

        // add FQCN first
        $fileExtractor->addVisitor(new NameResolverIntegration());

        // add remaining visitors
        $fileExtractor->addVisitor(new BackendTranslatorVisitor());
        $fileExtractor->addVisitor(new CallbackValidationVisitor());
        $fileExtractor->addVisitor(new ClassValidationVisitor());
        $fileExtractor->addVisitor(new ConstructorParameterVisitor());
        $fileExtractor->addVisitor(new ContainerAwareTrans());
        $fileExtractor->addVisitor(new ContainerAwareTransChoice());
        $fileExtractor->addVisitor(new CustomConstraintDefaultMessagesVisitor());
        $fileExtractor->addVisitor(new FlashMessage());
        $fileExtractor->addVisitor(new FormOptionLabelsVisitor());
        $fileExtractor->addVisitor(new FormTypeChoices());
        $fileExtractor->addVisitor(new PropertyValidationVisitor());

        return $fileExtractor;
    }


    /**
     * @return SourceCollection
     */
    private function createTwigExtractor (array $dirs) : FileExtractor
    {
        $loader = new FilesystemLoader($dirs);
        $twig = new Environment($loader);
        $fileExtractor = new TwigFileExtractor($twig);

        // register extensions
        $twig->addExtension(new FormExtension());
        $twig->addExtension(new TranslationExtension());
        $twig->addExtension($this->mockExtension);

        // add visitors
        $fileExtractor->addVisitor(TwigVisitorFactory::create());

        return $fileExtractor;
    }


    /**
     *
     */
    private function createFinder (array $dirs) : Finder
    {
        $finder = new Finder();
        $finder
            ->name("*.{php,twig}")
            ->in($dirs);

        return $finder;
    }
}
