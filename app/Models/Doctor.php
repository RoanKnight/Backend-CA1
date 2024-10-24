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
    'specialization',
    'user_id',
    'deleted',
  ];

  /**
   * Get the appointments for the doctor.
   */

  public function appointments()
  {
    return $this->hasMany(Appointment::class);
  }

  public function patients()
  {
    return $this->hasMany(Patient::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
