<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/../../database/migrations/create_currencies_table.php.stub';

class CreateProductionTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        (new CreateCurrenciesTable())->up();

        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('price_amount');
            $table->string('price_currency')->nullable();
            $table->timestamps();
        });
    }
}
