<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\ClassMethod\InlineArrayReturnAssignRector;
use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\CodeQuality\Rector\Empty_\SimplifyEmptyCheckOnEmptyArrayRector;
use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\CodeQuality\Rector\Isset_\IssetOnPropertyObjectToPropertyExistsRector;
use Rector\Config\RectorConfig;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php81\Rector\Array_\ArrayToFirstClassCallableRector;
use Rector\Php84\Rector\Class_\DeprecatedAnnotationToDeprecatedAttributeRector;
use Rector\Php84\Rector\MethodCall\NewMethodCallWithoutParenthesesRector;
use Rector\Php84\Rector\Param\ExplicitNullableParamTypeRector;
use Rector\TypeDeclaration\Rector\Class_\ObjectTypedPropertyFromJMSSerializerAttributeTypeRector;
use Rector\TypeDeclaration\Rector\Class_\ScalarTypedPropertyFromJMSSerializerAttributeTypeRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignsRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\SafeDeclareStrictTypesRector;

return RectorConfig::configure()
    ->withPaths(
        [
            __DIR__ . '/src',
        ]
    )
    ->withSkip(
        [
            ArrayToFirstClassCallableRector::class,
            FlipTypeControlToUseExclusiveTypeRector::class,
            IssetOnPropertyObjectToPropertyExistsRector::class,
            InlineArrayReturnAssignRector::class,
            ClosureToArrowFunctionRector::class,
            SimplifyEmptyCheckOnEmptyArrayRector::class,
            SafeDeclareStrictTypesRector::class,
            DeprecatedAnnotationToDeprecatedAttributeRector::class,
            ScalarTypedPropertyFromJMSSerializerAttributeTypeRector::class,
            ObjectTypedPropertyFromJMSSerializerAttributeTypeRector::class,
            NewMethodCallWithoutParenthesesRector::class,
        ]
    )
    ->withImportNames(
        importShortClasses: false,
        removeUnusedImports: false,
    )
    ->withPhpSets(php82: true)
    ->withAttributesSets()
    ->withPreparedSets(
        // deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        typeDeclarationDocblocks: true,
    )
    ->withRules(
        [
            InlineConstructorDefaultToPropertyRector::class,
            ExplicitNullableParamTypeRector::class,
        ]
    )
    ->withConfiguredRule(
        TypedPropertyFromAssignsRector::class,
        [
            'inlinePublic' => true,
        ]
    );
