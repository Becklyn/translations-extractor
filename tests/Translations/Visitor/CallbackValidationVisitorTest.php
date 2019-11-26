<?php declare(strict_types=1);

namespace Tests\Becklyn\TranslationsExtractor\Translations\Visitor;

use Becklyn\TranslationsExtractor\Extractor\Visitor\CallbackValidationVisitor;
use PHPUnit\Framework\TestCase;

class CallbackValidationVisitorTest extends TestCase
{
    use PhpExtractorTestTrait;


    /**
     *
     */
    public function testExtract () : void
    {
        $extracted = $this->extractMessagesWithVisitorFrom(new CallbackValidationVisitor(), ["CallbackValidationVisitor"]);

        self::assertEquals([
            "validators" => [
                "callback.method.message",
            ],
        ], $extracted);
    }
}
