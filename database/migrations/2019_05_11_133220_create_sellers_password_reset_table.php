<?php

  use Illuminate\Database\Migrations\Migration;
  use Illuminate\Database\Schema\Blueprint;
  use Illuminate\Support\Facades\Schema;

  class CreateSellersPasswordResetTable extends Migration
  {
      /**
       * Run the migrations.
       *
       * @return void
       */
      public function up()
      {
          Schema::create('super_password_resets', function (Blueprint $table) {
              $table->increments('id');
              $table->string('idn');
              $table->enum('idn_type', ['CI', 'DNI', 'RUT', 'PASSPORT', 'RIF']);
              $table->unique(['idn', 'idn_type']);
              $table->string('token');
              $table->timestamp('created_at')->nullable();
          });
      }

      /**
       * Reverse the migrations.
       *
       * @return void
       */
      public function down()
      {
          Schema::dropIfExists('super_password_resets');
      }
  }
