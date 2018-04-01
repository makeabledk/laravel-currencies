<?php

use Illuminate\Database\Migrations\Migration;

require __DIR__.'/../../database/migrations/create_currencies_table.php.stub';

class CreateProductionTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        (new CreateCurrenciesTable())->up();
    }
}
