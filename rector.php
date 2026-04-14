<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\If_\RemoveAlwaysTrueIfConditionRector;
use Rector\DeadCode\Rector\If_\RemoveDeadInstanceOfRector;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\SafeDeclareStrictTypesRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/Classes',
        __DIR__ . '/Configuration',
        __DIR__ . '/Tests',
    ])
    ->withPhpSets()
    ->withAutoloadPaths([
        __DIR__ . '/.Build/vendor/autoload.php',
    ])
    ->withImportNames(
        importShortClasses: false,
        removeUnusedImports: true,
    )
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        earlyReturn: true,
    )
    ->withSets([
        PHPUnitSetList::PHPUNIT_100,
    ])
    ->withSkip([
        SafeDeclareStrictTypesRector::class => [
            __DIR__ . '/ext_emconf.php',
        ],

        // @todo Remove once compatibility with TYPO3 v13 is dropped
        RemoveAlwaysTrueIfConditionRector::class => [
            __DIR__ . '/Classes/Preview/ContentElementPreviewRenderer.php',
        ],
        RemoveDeadInstanceOfRector::class => [
            __DIR__ . '/Classes/Preview/ContentElementPreviewRenderer.php',
        ],
        RemoveExtraParametersRector::class => [
            __DIR__ . '/Configuration/TCA/Overrides/tt_content.php',
        ],
    ])
    ->withRootFiles();
