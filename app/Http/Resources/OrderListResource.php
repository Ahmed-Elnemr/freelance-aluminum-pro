<?php

namespace App\Http\Resources;

use App\Filament\Resources\MaintenanceTypeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locationData = json_decode($this->location_data, true);
        $locationName = $locationData['location_name'] ?? '';
        return [
            'id' => $this->id,
            'user_name' => $this->user?->name,
            'maintenance_type' => $this->maintenanceType?->name,
            'location' => $locationName,
            'description' => $this->description,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'created' => $this->created_at?->format('d-m-Y'),
        ];
    }
}
