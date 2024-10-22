<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'at',
    'cost',
    'paid',
    'patient_id',
    'doctor_id',
    'deleted',
  ];

  /**
   * Get the patient that owns the appointment.
   */
  public function patient()
  {
    return $this->belongsTo(Patient::class);
  }

  /**
   * Get the doctor that owns the appointment.
   */
  public function doctor()
  {
    return $this->belongsTo(Doctor::class);
  }
}
