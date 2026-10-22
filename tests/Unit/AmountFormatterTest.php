<?php

namespace Tests\Unit;

use App\Support\AmountFormatter;
use PHPUnit\Framework\TestCase;

class AmountFormatterTest extends TestCase
{
    public function test_amounts_are_grouped_and_rounded_to_whole_units()
    {
        $this->assertSame('1,234', AmountFormatter::formatAmount(1234.4));
        $this->assertSame('1,234', AmountFormatter::formatAmount(1234.5));
        $this->assertSame('-1,235', AmountFormatter::formatAmount(-1234.6));
    }

    public function test_amounts_are_spelled_out_in_english()
    {
        $this->assertSame('one thousand and one', AmountFormatter::spellOut(1001));
        $this->assertSame('minus twelve point five', AmountFormatter::spellOut(-12.5));
    }
}
