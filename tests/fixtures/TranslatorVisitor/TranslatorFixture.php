<?php declare(strict_types=1);

namespace Tests\Becklyn\TranslationsExtractor\fixtures\TranslatorVisitor;

class TranslatorFixture
{
    public function someMethod ()
    {
        //region Symfony Translator
        // $translator->trans()
        $this->translator->trans("translator.trans.property");
        $translator->trans("translator.trans.var");

        // $translator->trans() with custom domain
        $this->translator->trans("translator.trans.property", [], "other");
        $translator->trans("translator.trans.var", [], "other");
        //endregion

        //region Backend Translator
        // $backendTranslator->trans()
        $this->get("")->t("getter");
        $this->backendTranslator->trans("backendTranslator.trans.property");
        $backendTranslator->trans("backendTranslator.trans.var");

        // $backendTranslator->t()
        $this->backendTranslator->t("backendTranslator.t.property");
        $backendTranslator->t("backendTranslator.t.var");

        // $translator->t()
        $this->translator->t("translator.t.property");
        $translator->t("translator.t.var");
        //endregion
    }
}
