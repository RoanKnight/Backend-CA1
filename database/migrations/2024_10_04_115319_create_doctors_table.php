<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDoctorsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('doctors', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('phone_number')->nullable();
      $table->string('email');
      $table->string('specialization');
      $table->unsignedBigInteger('appointment_id')->nullable();
      $table->timestamps();

      $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('set null');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('doctors');
  }
}
