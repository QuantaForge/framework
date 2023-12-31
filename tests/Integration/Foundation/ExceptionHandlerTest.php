<?php

namespace QuantaForge\Tests\Integration\Foundation;

use QuantaForge\Auth\Access\AuthorizationException;
use QuantaForge\Auth\Access\Response;
use QuantaForge\Support\Facades\Route;
use Orchestra\Testbench\TestCase;
use Symfony\Component\Process\PhpProcess;

class ExceptionHandlerTest extends TestCase
{
    /**
     * Resolve application HTTP exception handler.
     *
     * @param  \QuantaForge\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationExceptionHandler($app)
    {
        $app->singleton('QuantaForge\Contracts\Debug\ExceptionHandler', 'QuantaForge\Foundation\Exceptions\Handler');
    }

    public function testItRendersAuthorizationExceptions()
    {
        Route::get('test-route', fn () => Response::deny('expected message', 321)->authorize());

        // HTTP request...
        $this->get('test-route')
            ->assertStatus(403)
            ->assertSeeText('expected message');

        // JSON request...
        $this->getJson('test-route')
            ->assertStatus(403)
            ->assertExactJson([
                'message' => 'expected message',
            ]);
    }

    public function testItRendersAuthorizationExceptionsWithCustomStatusCode()
    {
        Route::get('test-route', fn () => Response::deny('expected message', 321)->withStatus(404)->authorize());

        // HTTP request...
        $this->get('test-route')
            ->assertStatus(404)
            ->assertSeeText('Not Found');

        // JSON request...
        $this->getJson('test-route')
            ->assertStatus(404)
            ->assertExactJson([
                'message' => 'expected message',
            ]);
    }

    public function testItRendersAuthorizationExceptionsWithStatusCodeTextWhenNoMessageIsSet()
    {
        Route::get('test-route', fn () => Response::denyWithStatus(404)->authorize());

        // HTTP request...
        $this->get('test-route')
            ->assertStatus(404)
            ->assertSeeText('Not Found');

        // JSON request...
        $this->getJson('test-route')
            ->assertStatus(404)
            ->assertExactJson([
                'message' => 'Not Found',
            ]);

        Route::get('test-route', fn () => Response::denyWithStatus(418)->authorize());

        // HTTP request...
        $this->get('test-route')
            ->assertStatus(418)
            ->assertSeeText("I'm a teapot", false);

        // JSON request...
        $this->getJson('test-route')
            ->assertStatus(418)
            ->assertExactJson([
                'message' => "I'm a teapot",
            ]);
    }

    public function testItRendersAuthorizationExceptionsWithStatusButWithoutResponse()
    {
        Route::get('test-route', fn () => throw (new AuthorizationException())->withStatus(418));

        // HTTP request...
        $this->get('test-route')
            ->assertStatus(418)
            ->assertSeeText("I'm a teapot", false);

        // JSON request...
        $this->getJson('test-route')
            ->assertStatus(418)
            ->assertExactJson([
                'message' => "I'm a teapot",
            ]);
    }

    public function testItHasFallbackErrorMessageForUnknownStatusCodes()
    {
        Route::get('test-route', fn () => throw (new AuthorizationException())->withStatus(399));

        // HTTP request...
        $this->get('test-route')
            ->assertStatus(399)
            ->assertSeeText('Whoops, looks like something went wrong.');

        // JSON request...
        $this->getJson('test-route')
            ->assertStatus(399)
            ->assertExactJson([
                'message' => 'Whoops, looks like something went wrong.',
            ]);
    }

    /**
     * @dataProvider exitCodesProvider
     */
    public function testItReturnsNonZeroExitCodesForUncaughtExceptions($providers, $successful)
    {
        $basePath = static::applicationBasePath();
        $providers = json_encode($providers, true);

        $process = new PhpProcess(<<<EOF
<?php

require 'vendor/autoload.php';

\$quantaforge = Orchestra\Testbench\Foundation\Application::create(basePath: '$basePath', options: ['extra' => ['providers' => $providers]]);
\$quantaforge->singleton('QuantaForge\Contracts\Debug\ExceptionHandler', 'QuantaForge\Foundation\Exceptions\Handler');

\$kernel = \$quantaforge[QuantaForge\Contracts\Console\Kernel::class];

return \$kernel->call('throw-exception-command');
EOF, __DIR__.'/../../../', ['APP_RUNNING_IN_CONSOLE' => true]);

        $process->run();

        $this->assertSame($successful, $process->isSuccessful());
    }

    public static function exitCodesProvider()
    {
        yield 'Throw exception' => [[Fixtures\Providers\ThrowUncaughtExceptionServiceProvider::class], false];
        yield 'Do not throw exception' => [[Fixtures\Providers\ThrowExceptionServiceProvider::class], true];
    }
}
