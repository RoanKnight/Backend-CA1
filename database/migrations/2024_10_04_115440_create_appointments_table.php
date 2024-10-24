<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('appointments', function (Blueprint $table) {
      $table->id();
      $table->dateTime('at');
      $table->decimal('cost', 8, 2);
      $table->boolean('paid')->default(false);
      $table->unsignedBigInteger('patient_id');
      $table->unsignedBigInteger('doctor_id');
      $table->boolean('deleted')->default(false);
      $table->timestamps();

      $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
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
    Schema::dropIfExists('appointments');
  }
}
