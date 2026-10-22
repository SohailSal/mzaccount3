<?php

namespace App\Support;

class AmountFormatter
{
    private const SMALL_NUMBERS = [
        'zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine',
        'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen',
        'seventeen', 'eighteen', 'nineteen',
    ];

    private const TENS = [
        2 => 'twenty',
        3 => 'thirty',
        4 => 'forty',
        5 => 'fifty',
        6 => 'sixty',
        7 => 'seventy',
        8 => 'eighty',
        9 => 'ninety',
    ];

    public static function formatAmount($amount): string
    {
        $rounded = round((float) $amount, 0, PHP_ROUND_HALF_EVEN);

        return number_format($rounded, 0, '.', ',');
    }

    public static function spellOut($amount): string
    {
        $amount = (float) $amount;
        $number = abs($amount);
        $whole = (int) floor($number);
        $formatted = number_format($number, 2, '.', '');
        $fraction = rtrim(substr($formatted, strpos($formatted, '.') + 1), '0');
        $words = self::spellInteger($whole);

        if ($fraction !== '') {
            $words .= ' point '.implode(' ', array_map(
                static fn ($digit) => self::SMALL_NUMBERS[(int) $digit],
                str_split($fraction)
            ));
        }

        return $amount < 0 ? 'minus '.$words : $words;
    }

    private static function spellInteger(int $number): string
    {
        if ($number < 20) {
            return self::SMALL_NUMBERS[$number];
        }

        if ($number < 100) {
            $remainder = $number % 10;

            return self::TENS[intdiv($number, 10)].($remainder ? '-'.self::SMALL_NUMBERS[$remainder] : '');
        }

        if ($number < 1000) {
            $remainder = $number % 100;

            return self::SMALL_NUMBERS[intdiv($number, 100)].' hundred'
                .($remainder ? ' and '.self::spellInteger($remainder) : '');
        }

        foreach ([1000000000 => 'billion', 1000000 => 'million', 1000 => 'thousand'] as $size => $label) {
            if ($number >= $size) {
                $whole = intdiv($number, $size);
                $remainder = $number % $size;
                $separator = $remainder > 0 && $remainder < 100 ? ' and ' : ($remainder > 0 ? ', ' : '');

                return self::spellInteger($whole).' '.$label.$separator
                    .($remainder ? self::spellInteger($remainder) : '');
            }
        }

        return (string) $number;
    }
}
