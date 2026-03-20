# Architecture: twig-extra-bundle

## Purpose

A Symfony bundle that auto-wires the `twig/extra-*` extension packages (markdown, HTML, intl, string, CSS inliner, Inky, cache) as Twig extensions in a Symfony application. Provides friendly error messages when a required dependency is missing.

## Directory Structure

```
TwigExtraBundle.php                           - Bundle class
DependencyInjection/
  TwigExtraExtension.php                      - Loads service configs based on which extra packages are installed
  Configuration.php                           - Bundle configuration (which extras to enable)
  Compiler/MissingExtensionSuggestorPass.php  - DI compiler pass: registers the suggestor if an extension is absent
Extensions.php                                - Map of extra-package IDs to their service class and install hint
MissingExtensionSuggestor.php                 - Twig extension that throws a helpful exception when an extra is used but not installed
LeagueCommonMarkConverterFactory.php          - Factory: creates the League CommonMark converter for the markdown extra
Resources/config/                             - Service definitions for each extra (cache, cssinliner, html, inky, intl, markdown, string)
Tests/                                        - Bundle integration tests
```

## Key Design Decisions

- **Auto-detection**: The bundle checks which `twig/extra-*` packages are installed at container compile time and only registers the corresponding service definitions, avoiding hard dependencies on all extras.
- **Helpful errors**: `MissingExtensionSuggestor` registers as a fallback Twig extension that intercepts calls to unregistered functions/filters and throws an exception explaining which `twig/extra-*` package to install.
- **Compiler pass**: The `MissingExtensionSuggestorPass` injects hints for each missing extra into the suggestor service.

## Extension Points

- Add new `twig/extra-*` packages by adding entries to `Extensions.php` and a corresponding service config file in `Resources/config/`.

## Dependency Flow

```
Symfony DI container build
  └─> TwigExtraExtension::load()
        └─> checks which twig/extra-* are installed
        └─> loads matching Resources/config/*.php service definitions
        └─> MissingExtensionSuggestorPass — registers fallback hints for absent extras
Runtime
  └─> Twig renders {{ "text" | u_truncate(5) }}
        └─> StringExtension (from twig/extra-string) handles the filter
```
