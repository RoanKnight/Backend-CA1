<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('patients', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('insurance')->nullable();
      $table->string('email');
      $table->string('phone_number')->nullable();
      $table->unsignedBigInteger('doctor_id');
      $table->timestamps();

      $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('patients');
  }
}
