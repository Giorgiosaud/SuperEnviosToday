<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToPasswordTokenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('password_resets', function (Blueprint $table) {
            $table->string('idn')->after('email');
            $table->enum('idn_type', ['CI', 'DNI', 'RUT', 'PASSPORT', 'RIF'])->after('email');
            $table->unique(['idn', 'idn_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('password_resets', function (Blueprint $table) {
            $table->dropColumn('idn');
            $table->dropColumn('idn_type');
        });
    }
}
