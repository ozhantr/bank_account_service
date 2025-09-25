<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests')
    ->name('*.php')
    ->ignoreVCS(true)
    ->ignoreDotFiles(true);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setCacheFile(__DIR__.'/.php-cs-fixer.cache')
    ->setRules([
        // Temel setler
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHP80Migration' => true,
        '@PHP81Migration' => true,
        '@PHP82Migration' => true,
        '@PHP83Migration' => true, // 8.3+ projeler için güvenli

        'ordered_imports' => ['sort_algorithm' => 'alpha', 'imports_order' => ['class', 'function', 'const']],
        'global_namespace_import' => ['import_classes' => true, 'import_functions' => false, 'import_constants' => false],        
        'no_superfluous_phpdoc_tags' => ['allow_mixed' => true, 'remove_inheritdoc' => false],
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_to_comment' => false,
        'phpdoc_summary' => false,
        'yoda_style' => false,
        'binary_operator_spaces' => ['default' => 'single_space'],
        'class_attributes_separation' => ['elements' => ['method' => 'one', 'property' => 'one']],
        'single_quote' => true,
        'declare_strict_types' => false,
        'nullable_type_declaration_for_default_null_value' => true,
        'native_type_declaration_casing' => true,

        'strict_param' => true,
        'mb_str_functions' => true,
        'modernize_strpos' => true,             
        'fopen_flag_order' => true,
        'no_alias_functions' => true,

        'no_unused_imports' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'simplified_if_return' => true,
        'combine_consecutive_issets' => false,
        'combine_consecutive_unsets' => false,
        'return_assignment' => false,
    ]);