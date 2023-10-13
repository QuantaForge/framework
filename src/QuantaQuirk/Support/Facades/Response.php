<?php

namespace QuantaQuirk\Support\Facades;

use QuantaQuirk\Contracts\Routing\ResponseFactory as ResponseFactoryContract;

/**
 * @method static \QuantaQuirk\Http\Response make(mixed $content = '', int $status = 200, array $headers = [])
 * @method static \QuantaQuirk\Http\Response noContent(int $status = 204, array $headers = [])
 * @method static \QuantaQuirk\Http\Response view(string|array $view, array $data = [], int $status = 200, array $headers = [])
 * @method static \QuantaQuirk\Http\JsonResponse json(mixed $data = [], int $status = 200, array $headers = [], int $options = 0)
 * @method static \QuantaQuirk\Http\JsonResponse jsonp(string $callback, mixed $data = [], int $status = 200, array $headers = [], int $options = 0)
 * @method static \Symfony\Component\HttpFoundation\StreamedResponse stream(callable $callback, int $status = 200, array $headers = [])
 * @method static \Symfony\Component\HttpFoundation\StreamedResponse streamDownload(callable $callback, string|null $name = null, array $headers = [], string|null $disposition = 'attachment')
 * @method static \Symfony\Component\HttpFoundation\BinaryFileResponse download(\SplFileInfo|string $file, string|null $name = null, array $headers = [], string|null $disposition = 'attachment')
 * @method static \Symfony\Component\HttpFoundation\BinaryFileResponse file(\SplFileInfo|string $file, array $headers = [])
 * @method static \QuantaQuirk\Http\RedirectResponse redirectTo(string $path, int $status = 302, array $headers = [], bool|null $secure = null)
 * @method static \QuantaQuirk\Http\RedirectResponse redirectToRoute(string $route, mixed $parameters = [], int $status = 302, array $headers = [])
 * @method static \QuantaQuirk\Http\RedirectResponse redirectToAction(array|string $action, mixed $parameters = [], int $status = 302, array $headers = [])
 * @method static \QuantaQuirk\Http\RedirectResponse redirectGuest(string $path, int $status = 302, array $headers = [], bool|null $secure = null)
 * @method static \QuantaQuirk\Http\RedirectResponse redirectToIntended(string $default = '/', int $status = 302, array $headers = [], bool|null $secure = null)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 *
 * @see \QuantaQuirk\Routing\ResponseFactory
 */
class Response extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ResponseFactoryContract::class;
    }
}
