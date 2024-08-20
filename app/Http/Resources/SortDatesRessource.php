<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SortDatesRessource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->sortDatesByLatest(parent::toArray($request));
    }

    /**
     * Returns an array sorted by most recent date in `updated_at` and `created_at`.
     *
     * @param  array $data
     * @return array $data
     */
    public function sortDatesByLatest(array $data)
    {
        usort($data, function ($a, $b) {
            $dateA = $a['updated_at'] ? strtotime($a['updated_at']) : strtotime($a['created_at']);
            $dateB = $b['updated_at'] ? strtotime($b['updated_at']) : strtotime($b['created_at']);

            return $dateB <=> $dateA;
        });

        return $data;
    }
}
