<?php

namespace QuantaForge\Tests\Integration\Foundation\Fixtures\Console;

use Exception;
use QuantaForge\Console\Command;

class ThrowExceptionCommand extends Command
{
    protected $signature = 'throw-exception-command';

    public function handle()
    {
        throw new Exception('Thrown inside ThrowExceptionCommand');
    }
}
