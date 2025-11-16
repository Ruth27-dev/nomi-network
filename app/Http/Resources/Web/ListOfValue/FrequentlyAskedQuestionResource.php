<?php

namespace App\Http\Resources\Web\ListOfValue;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FrequentlyAskedQuestionResource extends JsonResource
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
            'page' => $this->page,
            'title' => $this->title,
            'content' => $this->short_detail,
            'dataDetail' => collect($this->content['dataDetail'] ?? [])
                ->sortBy('ordering')
                ->map(function ($item) {
                    return [
                        'ordering' => $item['ordering'] ?? null,
                        'question_en' => $item['question_en'] ?? null,
                        'question_km' => $item['question_km'] ?? null,
                        'answer_en' => $item['answer_en'] ?? null,
                        'answer_km' => $item['answer_km'] ?? null,
                    ];
                })
                ->values()
                ->toArray(),
        ];
    }
}
