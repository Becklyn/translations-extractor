<?php declare(strict_types=1);

namespace Tests\Becklyn\TranslationsExtractor\Translations\Visitor;

use Becklyn\TranslationsExtractor\Extractor\Visitor\PropertyValidationVisitor;
use PHPUnit\Framework\TestCase;

class PropertyValidationVisitorTest extends TestCase
{
    use PhpExtractorTestTrait;

    /**
     *
     */
    public function testExtract () : void
    {
        $extracted = $this->extractMessagesWithVisitorFrom(new PropertyValidationVisitor(), ["PropertyValidationVisitor"]);

        self::assertEquals([
            "validators" => [
                "property.not_blank.message",
            ],
        ], $extracted);
    }
}
