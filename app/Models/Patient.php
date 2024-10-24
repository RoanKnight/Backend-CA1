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
    'insurance',
    'user_id',
    'doctor_id',
    'deleted',
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

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
