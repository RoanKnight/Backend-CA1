<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */

  protected $fillable = [
    'name',
    'phone_number',
    'email',
    'specialization',
    'appointment_id',
    'user_id',
  ];

  /**
   * Get the appointments for the doctor.
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
