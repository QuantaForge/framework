<?php

namespace QuantaQuirk\Tests\Integration\Notifications;

use QuantaQuirk\Contracts\Translation\HasLocalePreference;
use QuantaQuirk\Database\Eloquent\Model;
use QuantaQuirk\Database\Schema\Blueprint;
use QuantaQuirk\Foundation\Events\LocaleUpdated;
use QuantaQuirk\Mail\Mailable;
use QuantaQuirk\Notifications\Channels\MailChannel;
use QuantaQuirk\Notifications\Messages\MailMessage;
use QuantaQuirk\Notifications\Notifiable;
use QuantaQuirk\Notifications\Notification;
use QuantaQuirk\Support\Carbon;
use QuantaQuirk\Support\Facades\Event;
use QuantaQuirk\Support\Facades\Notification as NotificationFacade;
use QuantaQuirk\Support\Facades\Schema;
use QuantaQuirk\Support\Facades\View;
use QuantaQuirk\Testing\Assert;
use Orchestra\Testbench\TestCase;

class SendingNotificationsWithLocaleTest extends TestCase
{
    public $mailer;

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('mail.driver', 'array');

        $app['config']->set('app.locale', 'en');

        View::addLocation(__DIR__.'/Fixtures');

        app('translator')->setLoaded([
            '*' => [
                '*' => [
                    'en' => ['hi' => 'hello'],
                    'es' => ['hi' => 'hola'],
                    'fr' => ['hi' => 'bonjour'],
                ],
            ],
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->string('name')->nullable();
        });
    }

    public function testMailIsSentWithDefaultLocale()
    {
        $user = NotifiableLocalizedUser::forceCreate([
            'email' => 'taylor@quantaquirk.com',
            'name' => 'Taylor Otwell',
        ]);

        NotificationFacade::send($user, new GreetingMailNotification);

        $this->assertStringContainsString('hello',
            app('mailer')->getSymfonyTransport()->messages()[0]->toString()
        );
    }

    public function testMailIsSentWithFacadeSelectedLocale()
    {
        $user = NotifiableLocalizedUser::forceCreate([
            'email' => 'taylor@quantaquirk.com',
            'name' => 'Taylor Otwell',
        ]);

        NotificationFacade::locale('fr')->send($user, new GreetingMailNotification);

        $this->assertStringContainsString('bonjour',
            app('mailer')->getSymfonyTransport()->messages()[0]->toString()
        );
    }

    public function testMailIsSentWithNotificationSelectedLocale()
    {
        $users = [
            NotifiableLocalizedUser::forceCreate([
                'email' => 'taylor@quantaquirk.com',
                'name' => 'Taylor Otwell',
            ]),
            NotifiableLocalizedUser::forceCreate([
                'email' => 'mohamed@quantaquirk.com',
                'name' => 'Mohamed Said',
            ]),
        ];

        NotificationFacade::send($users, (new GreetingMailNotification)->locale('fr'));

        $this->assertStringContainsString('bonjour',
            app('mailer')->getSymfonyTransport()->messages()[0]->toString()
        );

        $this->assertStringContainsString('bonjour',
            app('mailer')->getSymfonyTransport()->messages()[1]->toString()
        );
    }

    public function testMailableIsSentWithSelectedLocale()
    {
        $user = NotifiableLocalizedUser::forceCreate([
            'email' => 'taylor@quantaquirk.com',
            'name' => 'Taylor Otwell',
        ]);

        NotificationFacade::locale('fr')->send($user, new GreetingMailNotificationWithMailable);

        $this->assertStringContainsString('bonjour',
            app('mailer')->getSymfonyTransport()->messages()[0]->toString()
        );
    }

    public function testMailIsSentWithLocaleUpdatedListenersCalled()
    {
        Carbon::setTestNow('2018-07-25');

        Event::listen(LocaleUpdated::class, function ($event) {
            Carbon::setLocale($event->locale);
        });

        $user = NotifiableLocalizedUser::forceCreate([
            'email' => 'taylor@quantaquirk.com',
            'name' => 'Taylor Otwell',
        ]);

        $user->notify((new GreetingMailNotification)->locale('fr'));

        $this->assertStringContainsString('bonjour',
            app('mailer')->getSymfonyTransport()->messages()[0]->toString()
        );

        Assert::assertMatchesRegularExpression('/dans (1|un) jour/',
            app('mailer')->getSymfonyTransport()->messages()[0]->toString()
        );

        $this->assertTrue($this->app->isLocale('en'));

        $this->assertSame('en', Carbon::getLocale());

        Carbon::setTestNow(null);
    }

    public function testLocaleIsSentWithNotifiablePreferredLocale()
    {
        $recipient = new NotifiableEmailLocalePreferredUser([
            'email' => 'test@mail.com',
            'email_locale' => 'fr',
        ]);

        $recipient->notify(new GreetingMailNotification);

        $this->assertStringContainsString('bonjour',
            app('mailer')->getSymfonyTransport()->messages()[0]->toString()
        );
    }

    public function testLocaleIsSentWithNotifiablePreferredLocaleForMultipleRecipients()
    {
        $recipients = [
            new NotifiableEmailLocalePreferredUser([
                'email' => 'test@mail.com',
                'email_locale' => 'fr',
            ]),
            new NotifiableEmailLocalePreferredUser([
                'email' => 'test.2@mail.com',
                'email_locale' => 'es',
            ]),
            NotifiableLocalizedUser::forceCreate([
                'email' => 'test.3@mail.com',
            ]),
        ];

        NotificationFacade::send(
            $recipients, new GreetingMailNotification
        );

        $this->assertStringContainsString('bonjour',
            app('mailer')->getSymfonyTransport()->messages()[0]->toString()
        );
        $this->assertStringContainsString('hola',
            app('mailer')->getSymfonyTransport()->messages()[1]->toString()
        );
        $this->assertStringContainsString('hello',
            app('mailer')->getSymfonyTransport()->messages()[2]->toString()
        );
    }

    public function testLocaleIsSentWithNotificationSelectedLocaleOverridingNotifiablePreferredLocale()
    {
        $recipient = new NotifiableEmailLocalePreferredUser([
            'email' => 'test@mail.com',
            'email_locale' => 'es',
        ]);

        $recipient->notify(
            (new GreetingMailNotification)->locale('fr')
        );

        $this->assertStringContainsString('bonjour',
            app('mailer')->getSymfonyTransport()->messages()[0]->toString()
        );
    }

    public function testLocaleIsSentWithFacadeSelectedLocaleOverridingNotifiablePreferredLocale()
    {
        $recipient = new NotifiableEmailLocalePreferredUser([
            'email' => 'test@mail.com',
            'email_locale' => 'es',
        ]);

        NotificationFacade::locale('fr')->send(
            $recipient, new GreetingMailNotification
        );

        $this->assertStringContainsString('bonjour',
            app('mailer')->getSymfonyTransport()->messages()[0]->toString()
        );
    }
}

class NotifiableLocalizedUser extends Model
{
    use Notifiable;

    public $table = 'users';
    public $timestamps = false;
}

class NotifiableEmailLocalePreferredUser extends Model implements HasLocalePreference
{
    use Notifiable;

    protected $fillable = [
        'email',
        'email_locale',
    ];

    public function preferredLocale()
    {
        return $this->email_locale;
    }
}

class GreetingMailNotification extends Notification
{
    public function via($notifiable)
    {
        return [MailChannel::class];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting(__('hi'))
            ->line(Carbon::tomorrow()->diffForHumans());
    }
}

class GreetingMailNotificationWithMailable extends Notification
{
    public function via($notifiable)
    {
        return [MailChannel::class];
    }

    public function toMail($notifiable)
    {
        return (new GreetingMailable)
            ->to($notifiable->email);
    }
}

class GreetingMailable extends Mailable
{
    public function build()
    {
        return $this->view('greeting');
    }
}
