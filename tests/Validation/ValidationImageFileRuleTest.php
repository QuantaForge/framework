<?php

namespace QuantaQuirk\Tests\Validation;

use QuantaQuirk\Container\Container;
use QuantaQuirk\Http\UploadedFile;
use QuantaQuirk\Support\Arr;
use QuantaQuirk\Support\Facades\Facade;
use QuantaQuirk\Translation\ArrayLoader;
use QuantaQuirk\Translation\Translator;
use QuantaQuirk\Validation\Rule;
use QuantaQuirk\Validation\Rules\File;
use QuantaQuirk\Validation\ValidationServiceProvider;
use QuantaQuirk\Validation\Validator;
use PHPUnit\Framework\TestCase;

class ValidationImageFileRuleTest extends TestCase
{
    public function testDimensions()
    {
        $this->fails(
            File::image()->dimensions(Rule::dimensions()->width(100)->height(100)),
            UploadedFile::fake()->image('foo.png', 101, 101),
            ['validation.dimensions'],
        );

        $this->passes(
            File::image()->dimensions(Rule::dimensions()->width(100)->height(100)),
            UploadedFile::fake()->image('foo.png', 100, 100),
        );
    }

    public function testDimensionsWithCustomImageSizeMethod()
    {
        $this->fails(
            File::image()->dimensions(Rule::dimensions()->width(100)->height(100)),
            new UploadedFileWithCustomImageSizeMethod(stream_get_meta_data($tmpFile = tmpfile())['uri'], 'foo.png'),
            ['validation.dimensions'],
        );

        $this->passes(
            File::image()->dimensions(Rule::dimensions()->width(200)->height(200)),
            new UploadedFileWithCustomImageSizeMethod(stream_get_meta_data($tmpFile = tmpfile())['uri'], 'foo.png'),
        );
    }

    protected function fails($rule, $values, $messages)
    {
        $this->assertValidationRules($rule, $values, false, $messages);
    }

    protected function assertValidationRules($rule, $values, $result, $messages)
    {
        $values = Arr::wrap($values);

        foreach ($values as $value) {
            $v = new Validator(
                resolve('translator'),
                ['my_file' => $value],
                ['my_file' => is_object($rule) ? clone $rule : $rule]
            );

            $this->assertSame($result, $v->passes());

            $this->assertSame(
                $result ? [] : ['my_file' => $messages],
                $v->messages()->toArray()
            );
        }
    }

    protected function passes($rule, $values)
    {
        $this->assertValidationRules($rule, $values, true, []);
    }

    protected function setUp(): void
    {
        $container = Container::getInstance();

        $container->bind('translator', function () {
            return new Translator(
                new ArrayLoader, 'en'
            );
        });

        Facade::setFacadeApplication($container);

        (new ValidationServiceProvider($container))->register();
    }

    protected function tearDown(): void
    {
        Container::setInstance(null);

        Facade::clearResolvedInstances();

        Facade::setFacadeApplication(null);
    }
}

class UploadedFileWithCustomImageSizeMethod extends UploadedFile
{
    public function isValid(): bool
    {
        return true;
    }

    public function guessExtension(): string
    {
        return 'png';
    }

    public function dimensions()
    {
        return [200, 200];
    }
}
