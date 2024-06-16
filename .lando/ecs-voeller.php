<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;

return static function (ECSConfig $ecsConfig): void {
    $headerFile = __DIR__ . '/.header';
    if (file_exists($headerFile)) {
        $ecsConfig->ruleWithConfiguration(HeaderCommentFixer::class, [
            'comment_type' => 'comment',
            'header' => file_get_contents($headerFile),
            'location' => 'after_declare_strict',
            'separate' => 'both',
        ]);
    }

    $ecsConfig->rule(DeclareStrictTypesFixer::class);
    $ecsConfig->rule(NoUnusedImportsFixer::class);
    $ecsConfig->rule(OrderedImportsFixer::class);
};