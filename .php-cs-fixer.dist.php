<?php

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__ . '/src',
//        __DIR__ . '/tests',
        __DIR__ . '/config',
    ])
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'array_syntax' => ['syntax' => 'short'],
        'modernize_types_casting' => true,
        'declare_strict_types' => true,
        'no_unused_imports' => true,
        'single_quote' => true,
        'concat_space' => ['spacing' => 'one'],
        'trim_array_spaces' => true,
        'no_trailing_comma_in_singleline' => true,
        'no_whitespace_in_blank_line' => true,
        'whitespace_after_comma_in_array' => true,
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
            'imports_order' => ['class', 'function', 'const'],
        ],
        'global_namespace_import' => [
            'import_classes' => true,
            'import_functions' => false,
            'import_constants' => false,
        ],
        'class_attributes_separation' => [
            'elements' => [
                'method' => 'one',
                'property' => 'one',
                'trait_import' => 'none',
            ],
        ],
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
        ],
        'strict_param' => true,
        'strict_comparison' => true,
    ])
    ->setFinder($finder)
;
