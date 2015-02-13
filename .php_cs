<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->exclude('vendor')
    ->exclude('web/TMX')
    ->in(__DIR__)
;

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->fixers([
        'psr0', 'encoding', 'short_tag', 'duplicate_semicolon', 'empty_return',
        'extra_empty_lines', 'include', 'join_function', 'multiline_array_trailing_comma',
        'namespace_no_leading_whitespace', 'no_blank_lines_after_class_opening',
        'no_empty_lines_after_phpdocs', 'object_operator', 'operators_spaces',
        'phpdoc_indent', 'phpdoc_params', 'remove_leading_slash_use', 'remove_lines_between_uses',
        'return', 'single_array_no_trailing_comma', 'spaces_before_semicolon',
        'spaces_cast', 'standardize_not_equal', 'ternary_spaces', 'unused_use',
        'whitespacy_lines', 'align_double_arrow', 'concat_with_spaces',
        'no_blank_lines_before_namespace', 'ordered_use', 'short_array_syntax',
    ])
    ->finder($finder)
;
