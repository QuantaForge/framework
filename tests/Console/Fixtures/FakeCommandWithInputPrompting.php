<?php

namespace QuantaQuirk\Tests\Console\Fixtures;

use QuantaQuirk\Console\Command;
use QuantaQuirk\Contracts\Console\PromptsForMissingInput;
use QuantaQuirk\Prompts\Prompt;
use QuantaQuirk\Prompts\TextPrompt;
use Symfony\Component\Console\Input\InputInterface;

class FakeCommandWithInputPrompting extends Command implements PromptsForMissingInput
{
    protected $signature = 'fake-command-for-testing {name : An argument}';

    public $prompted = false;

    protected function configurePrompts(InputInterface $input)
    {
        Prompt::interactive(true);
        Prompt::fallbackWhen(true);

        TextPrompt::fallbackUsing(function () {
            $this->prompted = true;

            return 'foo';
        });
    }

    public function handle(): int
    {
        return self::SUCCESS;
    }
}
