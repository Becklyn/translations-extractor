Translations Extractor
======================

CLI command, that helps extracting translations in Symfony projects.


Installation
------------

It's best to install this command using the `composer-bin-plugin`:

```bash
composer bin test req becklyn/translations-extractor 
```


Usage
-----

Call the command using the CLI:

```bash
./vendor/bin/extract-translations src/ templates/
```

As arguments, pass the list of directories to search in.


### Options

You can pass in extension names for twig, so that Twig doesn't complain about missing functions (or filters, etc.).

*   `--mock-functions` the names of the functions to mock
*   `--mock-filters` the names of the filters to mock
*   `--mock-tests` the names of the tests to mock

```bash
./vendor/bin/extract-translations src/ --mock-filter normalize
```

There is a list of predefined mocks, take a look in `MockExtension`.
