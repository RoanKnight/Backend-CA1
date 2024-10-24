<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'at' => $this->at,
      'cost' => $this->cost,
      'paid' => $this->paid,
      'patient_id' => $this->patient_id,
      'doctor_id' => $this->doctor_id,
      'deleted' => $this->deleted,
    ];
  }
}
