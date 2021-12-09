<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\ClassMethod\DateTimeToDateTimeInterfaceRector;
use Rector\Core\Configuration\Option;
use Rector\Core\ValueObject\PhpVersion;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPromotedPropertyRector;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddArrayParamDocTypeRector;
use Rector\TypeDeclaration\Rector\FunctionLike\ReturnTypeDeclarationRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(LevelSetList::UP_TO_PHP_73);
    $containerConfigurator->import(SetList::CODE_QUALITY);
    $containerConfigurator->import(SetList::DEAD_CODE);
    $containerConfigurator->import(SetList::EARLY_RETURN);
    $containerConfigurator->import(SetList::TYPE_DECLARATION);
    $containerConfigurator->import(PHPUnitSetList::PHPUNIT_CODE_QUALITY);
    $containerConfigurator->import(PHPUnitSetList::PHPUNIT_EXCEPTION);
    $containerConfigurator->import(PHPUnitSetList::PHPUNIT_MOCK);
    $containerConfigurator->import(PHPUnitSetList::PHPUNIT_SPECIFIC_METHOD);
    $containerConfigurator->import(PHPUnitSetList::PHPUNIT_YIELD_DATA_PROVIDER);

    $services = $containerConfigurator->services();

    $services->set(StringClassNameToClassConstantRector::class)
        ->configure(['']);

    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::PATHS, [
        __DIR__ . '/Classes',
        __DIR__ . '/Configuration',
        __DIR__ . '/Tests',
    ]);

    $parameters->set(Option::AUTOLOAD_PATHS, [__DIR__ . '/.Build/vendor/autoload.php']);

    $parameters->set(Option::PHP_VERSION_FEATURES, PhpVersion::PHP_73);

    $parameters->set(Option::SKIP, [
        __DIR__ . '/Tests/Acceptance/*',
        DateTimeToDateTimeInterfaceRector::class => [
            __DIR__ . '/Classes/Domain/Model/Table.php',
            __DIR__ . '/Classes/Domain/Model/Transfer.php',
        ],
        RemoveUnusedPromotedPropertyRector::class, // Skip until compatibility with PHP >= 8.0
        ReturnTypeDeclarationRector::class => [
            __DIR__ . '/Classes/Domain/Repository/TableRepository.php',
            __DIR__ . '/Classes/Domain/Repository/TransferRepository.php',
        ],
    ]);
};
