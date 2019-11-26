<?php declare(strict_types=1);

namespace Becklyn\TranslationsExtractor\Extractor\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

class MockExtension extends AbstractExtension
{
    /**
     * @var array
     */
    private $functions;


    /**
     * @var array
     */
    private $filters;


    /**
     * @var array
     */
    private $tests;


    /**
     * @param array $functions
     * @param array $filters
     * @param array $tests
     */
    public function __construct (array $functions, array $filters, array $tests)
    {
        // append default functions here
        $this->functions = \array_merge($functions, [
            // Symfony
            "controller",
            "is_granted",
            "path",
            "render",

            // Becklyn tools
            "asset",
            "asset_inline",
            "asset_link",
            "data_container",
            "icon",
            "javascript_context",
            "javascript_routes_init",
            "javascript_translations_init",
            "route_tree_render",

            // mayd functions
            "mayd_config",
            "mayd_crud",
            "mayd_feature",
            "mayd_form_themes",
            "mayd_view_context",
        ]);
        $this->filters = \array_merge($filters, [
            // Becklyn tools
            "appendToKey",
        ]);
        $this->tests = $tests;
    }

    /**
     * @inheritDoc
     */
    public function getFilters () : array
    {
        return \array_map(
            function (string $name)
            {
                return new TwigFilter($name);
            },
            $this->filters
        );
    }

    /**
     * @inheritDoc
     */
    public function getTests () : array
    {
        return \array_map(
            function (string $name)
            {
                return new TwigTest($name);
            },
            $this->tests
        );
    }

    /**
     * @inheritDoc
     */
    public function getFunctions () : array
    {
        return \array_map(
            function (string $name)
            {
                return new TwigFunction($name);
            },
            $this->functions
        );
    }
}
