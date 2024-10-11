<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'insurance',
    'email',
    'phone_number',
    'doctor_id',
  ];

  /**
   * Get the doctor that owns the patient.
   */
  public function doctor()
  {
    return $this->belongsTo(Doctor::class);
  }

  /**
   * Get the appointments for the patient.
   */
  public function appointments()
  {
    return $this->hasMany(Appointment::class);
  }
}
