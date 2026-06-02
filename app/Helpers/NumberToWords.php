<?php

namespace App\Helpers;

class NumberToWords
{
    protected array $units = [
        0 => 'Zero', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four',
        5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen',
        14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen',
        18 => 'Eighteen', 19 => 'Nineteen',
    ];

    protected array $tens = [
        2 => 'Twenty',
        3 => 'Thirty',
        4 => 'Forty',
        5 => 'Fifty',
        6 => 'Sixty',
        7 => 'Seventy',
        8 => 'Eighty',
        9 => 'Ninety',
    ];

    public function convert(float|int|string $amount): string
    {
        $amount = (float) $amount;
        if ($amount === 0.0) {
            return 'Zero Rupees Only';
        }

        $rupees = (int) floor($amount);
        $paise = (int) round(($amount - $rupees) * 100);

        $words = trim($this->convertIndian($rupees) . ' Rupees');
        if ($paise > 0) {
            $words .= ' and ' . trim($this->convertIndian($paise)) . ' Paise';
        }

        return $words . ' Only';
    }

    protected function convertIndian(int $number): string
    {
        if ($number < 20) {
            return $this->units[$number];
        }

        if ($number < 100) {
            $ten = intdiv($number, 10);
            $rest = $number % 10;

            return trim($this->tens[$ten] . ' ' . ($rest ? $this->units[$rest] : ''));
        }

        if ($number < 1000) {
            $hundred = intdiv($number, 100);
            $rest = $number % 100;

            return trim($this->units[$hundred] . ' Hundred ' . ($rest ? $this->convertIndian($rest) : ''));
        }

        $units = [
            10000000 => 'Crore',
            100000 => 'Lakh',
            1000 => 'Thousand',
        ];

        foreach ($units as $divider => $label) {
            if ($number >= $divider) {
                $head = intdiv($number, $divider);
                $rest = $number % $divider;

                return trim($this->convertIndian($head) . ' ' . $label . ' ' . ($rest ? $this->convertIndian($rest) : ''));
            }
        }

        return (string) $number;
    }
}
