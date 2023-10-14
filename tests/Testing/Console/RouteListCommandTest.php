<?php

namespace QuantaForge\Tests\Testing\Console;

use QuantaForge\Contracts\Routing\Registrar;
use QuantaForge\Foundation\Auth\User;
use QuantaForge\Foundation\Console\RouteListCommand;
use QuantaForge\Foundation\Testing\Concerns\InteractsWithDeprecationHandling;
use QuantaForge\Http\RedirectResponse;
use QuantaForge\Routing\Controller;
use QuantaForge\Support\Facades\Facade;
use Orchestra\Testbench\TestCase;

class RouteListCommandTest extends TestCase
{
    use InteractsWithDeprecationHandling;

    /**
     * @var \QuantaForge\Contracts\Routing\Registrar
     */
    private $router;

    /**
     * @var \QuantaForge\Routing\UrlGenerator
     */
    private $urlGenerator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->router = $this->app->make(Registrar::class);

        RouteListCommand::resolveTerminalWidthUsing(function () {
            return 70;
        });
    }

    public function testDisplayRoutesForCli()
    {
        $this->router->get('/', function () {
            //
        });

        $this->router->get('closure', function () {
            return new RedirectResponse($this->urlGenerator->signedRoute('signed-route'));
        });

        $this->router->get('controller-method/{user}', [FooController::class, 'show']);
        $this->router->post('controller-invokable', FooController::class);
        $this->router->domain('{account}.example.com')->group(function () {
            $this->router->get('/', function () {
                //
            });

            $this->router->get('user/{id}', function ($account, $id) {
                //
            })->name('user.show')->middleware('web');
        });

        $this->artisan(RouteListCommand::class)
            ->assertSuccessful()
            ->expectsOutput('')
            ->expectsOutput('  GET|HEAD   / ..................................................... ')
            ->expectsOutput('  GET|HEAD   {account}.example.com/ ................................ ')
            ->expectsOutput('  GET|HEAD   closure ............................................... ')
            ->expectsOutput('  POST       controller-invokable QuantaForge\Tests\Testing\Console\…')
            ->expectsOutput('  GET|HEAD   controller-method/{user} QuantaForge\Tests\Testing\Cons…')
            ->expectsOutput('  GET|HEAD   {account}.example.com/user/{id} ............. user.show')
            ->expectsOutput('')
            ->expectsOutput('                                                  Showing [6] routes')
            ->expectsOutput('');
    }

    public function testDisplayRoutesForCliInVerboseMode()
    {
        $this->router->get('closure', function () {
            return new RedirectResponse($this->urlGenerator->signedRoute('signed-route'));
        });

        $this->router->get('controller-method/{user}', [FooController::class, 'show']);
        $this->router->post('controller-invokable', FooController::class);
        $this->router->domain('{account}.example.com')->group(function () {
            $this->router->get('user/{id}', function ($account, $id) {
                //
            })->name('user.show')->middleware('web');
        });

        $this->artisan(RouteListCommand::class, ['-v' => true])
            ->assertSuccessful()
            ->expectsOutput('')
            ->expectsOutput('  GET|HEAD   closure ............................................... ')
            ->expectsOutput('  POST       controller-invokable QuantaForge\\Tests\\Testing\\Console\\FooController')
            ->expectsOutput('  GET|HEAD   controller-method/{user} QuantaForge\\Tests\\Testing\\Console\\FooController@show')
            ->expectsOutput('  GET|HEAD   {account}.example.com/user/{id} ............. user.show')
            ->expectsOutput('             ⇂ web')
            ->expectsOutput('')
            ->expectsOutput('                                                  Showing [4] routes')
            ->expectsOutput('');
    }

    public function testRouteCanBeFilteredByName()
    {
        $this->withoutDeprecationHandling();

        $this->router->get('/', function () {
            //
        });
        $this->router->get('/foo', function () {
            //
        })->name('foo.show');

        $this->artisan(RouteListCommand::class, ['--name' => 'foo'])
            ->assertSuccessful()
            ->expectsOutput('')
            ->expectsOutput('  GET|HEAD       foo ...................................... foo.show')
            ->expectsOutput('')
            ->expectsOutput('                                                  Showing [1] routes')
            ->expectsOutput('');
    }

    public function testDisplayRoutesExceptVendor()
    {
        $this->router->get('foo/{user}', [FooController::class, 'show']);
        $this->router->view('view', 'blade.path');
        $this->router->redirect('redirect', 'destination');

        $this->artisan(RouteListCommand::class, ['-v' => true, '--except-vendor' => true])
            ->assertSuccessful()
            ->expectsOutput('')
            ->expectsOutput('  GET|HEAD       foo/{user} QuantaForge\Tests\Testing\Console\FooController@show')
            ->expectsOutput('  ANY            redirect .... QuantaForge\Routing\RedirectController')
            ->expectsOutput('  GET|HEAD       view .............................................. ')
            ->expectsOutput('')
            ->expectsOutput('                                                  Showing [3] routes')
            ->expectsOutput('');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Facade::setFacadeApplication(null);

        RouteListCommand::resolveTerminalWidthUsing(null);
    }
}

class FooController extends Controller
{
    public function show(User $user)
    {
        // ..
    }

    public function __invoke()
    {
        // ..
    }
}
