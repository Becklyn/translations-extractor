<?php declare(strict_types=1);

namespace Tests\Becklyn\TranslationsExtractor\Translations\Visitor;

use Becklyn\TranslationsExtractor\Extractor\Visitor\TranslatorVisitor;
use PHPUnit\Framework\TestCase;

class TranslatorVisitorTest extends TestCase
{
    use PhpExtractorTestTrait;


    /**
     *
     */
    public function testExtract () : void
    {
        $extracted = $this->extractMessagesWithVisitorFrom(new TranslatorVisitor(), ["TranslatorVisitor"]);

        self::assertEquals([
            "messages" => [
                "translator.trans.property",
                "translator.trans.var",
            ],
            "other" => [
                "translator.trans.property",
                "translator.trans.var",
            ],
            "backend" => [
                "backendTranslator.trans.property",
                "backendTranslator.trans.var",
                "backendTranslator.t.property",
                "backendTranslator.t.var",
                "translator.t.property",
                "translator.t.var",
            ],
        ], $extracted);
    }
}
