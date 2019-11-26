<?php declare(strict_types=1);

namespace Tests\Becklyn\TranslationsExtractor\fixtures\PropertyValidationVisitor;

use Symfony\Component\Validator\Constraints as Assert;

class PropertyValidationVisitorFixture
{
    /**
     * @Assert\NotBlank(message="property.not_blank.message")
     */
    private $test;

    /**
     * Default validation messages should not be added.
     *
     * @Assert\NotBlank()
     */
    private $withDefaultMessage;
}
