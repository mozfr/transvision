<?php

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules(
        [
            'array_syntax' => [
                'syntax' => 'short',
            ],
            'binary_operator_spaces' => [
                'align_double_arrow' => true,
            ],
            'blank_line_before_return' => true,
            'cast_spaces'              => true,
            'concat_space'             => [
                'spacing' => 'one',
            ],
            'encoding'                           => true,
            'full_opening_tag'                   => true,
            'no_alias_functions'                 => true,
            'no_blank_lines_after_class_opening' => true,
            'no_blank_lines_after_phpdoc'        => true,
            'no_empty_statement'                 => true,
            'no_extra_consecutive_blank_lines'   => [
                'break', 'continue', 'extra', 'return', 'throw', 'use',
                'parenthesis_brace_block', 'square_brace_block',
                'curly_brace_block',
            ],
            'no_leading_import_slash'                    => true,
            'no_leading_namespace_whitespace'            => true,
            'no_singleline_whitespace_before_semicolons' => true,
            'no_trailing_comma_in_singleline_array'      => true,
            'no_unused_imports'                          => true,
            'no_whitespace_in_blank_line'                => true,
            'object_operator_without_whitespace'         => true,
            'ordered_imports'                            => true,
            'phpdoc_align'                               => true,
            'phpdoc_indent'                              => true,
            'phpdoc_separation'                          => true,
            'phpdoc_types'                               => true,
            'psr0'                                       => true,
            'simplified_null_return'                     => true,
            'single_quote'                               => true,
            'standardize_not_equals'                     => true,
            'ternary_operator_spaces'                    => true,
            'trailing_comma_in_multiline_array'          => true,
            'trim_array_spaces'                          => true,
        ]
    )
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude('vendor')
            ->exclude('web/TMX')
            ->in(__DIR__)
    )
;
