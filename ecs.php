<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ArraySyntaxFixer::class)
        ->call('configure', [[
            'syntax' => 'short',
        ]]);


    $services->set(BinaryOperatorSpacesFixer::class)
        ->call('configure', [[
            // align key value pairs (mostly), if this is a problem change 'default' => 'single' and uncomment this line:
            // Likely problems are pipe operators, if so try: operators => ['|' => 'none']
            // 'operators' => ['=>' => 'align_single_space_minimal']
            'default' =>'align_single_space_minimal',
        ]]);

    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [
        __DIR__ . '/app',
        __DIR__ . '/bootstrap',
        __DIR__ . '/database',
        __DIR__ . '/public',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ]);

    $parameters->set(Option::SETS, [
        // run and fix, one by one
        SetList::SPACES,
        SetList::ARRAY,
        SetList::DOCBLOCK,
        SetList::NAMESPACES,
//        SetList::CONTROL_STRUCTURES,
//        SetList::CLEAN_CODE,
//        SetList::STRICT,
//        SetList::PSR_12,
//        SetList::PHPUNIT,
    ]);
};
