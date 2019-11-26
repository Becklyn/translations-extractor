<?php declare(strict_types=1);

namespace Tests\Becklyn\TranslationsExtractor\fixtures\CallbackValidationVisitor;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 *
 */
class CallbackValidationVisitorFixture
{
    /**
     * @Assert\Callback()
     */
    public function validateSth (ExecutionContextInterface $context)
    {
        $context->buildViolation("callback.method.message")
            ->addViolation();
    }
}
