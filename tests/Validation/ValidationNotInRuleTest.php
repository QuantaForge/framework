<?php

namespace QuantaQuirk\Tests\Validation;

use QuantaQuirk\Validation\Rule;
use QuantaQuirk\Validation\Rules\NotIn;
use PHPUnit\Framework\TestCase;

include_once 'Enums.php';

class ValidationNotInRuleTest extends TestCase
{
    public function testItCorrectlyFormatsAStringVersionOfTheRule()
    {
        $rule = new NotIn(['QuantaQuirk', 'Framework', 'PHP']);

        $this->assertSame('not_in:"QuantaQuirk","Framework","PHP"', (string) $rule);

        $rule = Rule::notIn([1, 2, 3, 4]);

        $this->assertSame('not_in:"1","2","3","4"', (string) $rule);

        $rule = Rule::notIn(collect([1, 2, 3, 4]));

        $this->assertSame('not_in:"1","2","3","4"', (string) $rule);

        $rule = Rule::notIn('1', '2', '3', '4');

        $this->assertSame('not_in:"1","2","3","4"', (string) $rule);

        $rule = Rule::notIn([StringStatus::done]);

        $this->assertSame('not_in:"done"', (string) $rule);

        $rule = Rule::notIn([IntegerStatus::done]);

        $this->assertSame('not_in:"2"', (string) $rule);

        $rule = Rule::notIn([PureEnum::one]);

        $this->assertSame('not_in:"one"', (string) $rule);
    }
}
