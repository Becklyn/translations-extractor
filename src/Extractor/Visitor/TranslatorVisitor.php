<?php declare(strict_types=1);

namespace Becklyn\TranslationsExtractor\Extractor\Visitor;

use PhpParser\Node;

/**
 * Searches for calls using the backend translator and regular translator calls.
 */
class TranslatorVisitor extends AbstractVisitor
{
    /**
     * @inheritDoc
     *
     * @return Node[]|void|null Array of nodes
     */
    public function enterNode (Node $node)
    {
        if (!$node instanceof Node\Expr\MethodCall)
        {
            return null;
        }

        if (!$node->name instanceof Node\Identifier)
        {
            return null;
        }

        if (null !== $label = $this->getStringArgument($node, 0))
        {
            if (
                $this->isNamedCall($node, "backendTranslator", "trans")
                || $this->isNamedCall($node, "backendTranslator", "t")
                || $this->isNamedCall($node, "translator", "t")
            )
            {
                $this->addLocation(
                    $label,
                    $node->getAttribute("startLine"),
                    $node,
                    ["domain" => "backend"]
                );
            }
            elseif ($this->isNamedCall($node, "translator", "trans"))
            {
                $this->addLocation(
                    $label,
                    $node->getAttribute("startLine"),
                    $node,
                    ["domain" => $this->getStringArgument($node, 2) ?? "messages"]
                );
            }
        }
    }


    /**
     *
     */
    private function isNamedCall (Node\Expr\MethodCall $node, string $caller, string $method) : bool
    {
        $callerNode = $node->var;
        $callerName = \property_exists($callerNode, "name") ? (string) $callerNode->name : '';

        return
            $method === (string) $node->name
            && $callerName === $caller
            && ($callerNode instanceof Node\Expr\Variable || $callerNode instanceof Node\Expr\PropertyFetch);
    }
}
