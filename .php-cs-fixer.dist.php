<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('var')
    ->exclude('vendor')
    ->exclude('migrations')
    ->exclude('bin')
;

return (new PhpCsFixer\Config())->setRules([
    '@Symfony' => true,             // https://cs.symfony.com/doc/ruleSets/Symfony.html
    'declare_strict_types' => true, // https://cs.symfony.com/doc/rules/strict/declare_strict_types.html
    'yoda_style' => false,          // https://cs.symfony.com/doc/rules/control_structure/yoda_style.html
    'phpdoc_to_comment' => false,   // https://cs.symfony.com/doc/rules/phpdoc/phpdoc_to_comment.html
])->setFinder($finder);