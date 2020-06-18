<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Separators
    |--------------------------------------------------------------------------
    |
    | Here you may configure how amounts should be formatted out of the box.
    | If you want to customize further you may also specify your own
    | formatter function using Amount::formatUsing(fn () => ...).
    |
    */
    'decimal_separator' => ',',

    'thousands_separator' => '.',


    /*
    |--------------------------------------------------------------------------
    | Decimals
    |--------------------------------------------------------------------------
    |
    | Here you may configure how many decimals should be used for calculation
    | and formatting respectively. By using 4 decimals you achieve increased
    | calculation accuracy and adhere to GAAP Compliance.
    |
    */
    'calculation_decimals' => 4,

    'formatting_decimals' => 2,

];