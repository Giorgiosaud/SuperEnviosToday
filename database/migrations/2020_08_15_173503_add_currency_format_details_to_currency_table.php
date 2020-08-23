<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCurrencyFormatDetailsToCurrencyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->string('symbol')->nullable()->after('sign');
            $table->integer('precision')->default(2)->after('sign');
            $table->char('separator',1)->default('.')->after('sign');
            $table->char('decimal',1)->default(',')->after('sign');
            $table->boolean('format_with_symbol')->default(true)->after('sign');
        });
        DB::statement('UPDATE currencies SET symbol=CONCAT(sign," ")');
        Schema::table('currencies', function (Blueprint $table) {
            $table->string('symbol')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->dropColumn(['symbol','precision','separator','decimal','format_with_symbol']);
        });
    }
}
