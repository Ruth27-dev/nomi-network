<?php

namespace App\Http\Resources\Web\ListOfValue;

use App\Models\UploadFile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class ReportDocumentCollection extends ResourceCollection
{
    protected Collection $categories;

    public function __construct($resource, ?Collection $categories = null)
    {
        parent::__construct($resource);
        $this->categories = $categories ?? collect();
    }

    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($item) {
            $categoryId = data_get($item, 'add_on.category_id');
            $category = $categoryId ? $this->categories->get($categoryId) : null;
            $file = data_get($item, 'add_on.file');

            return [
                'id' => $item->id,
                'title' => $item->title,
                'category_id' => $categoryId,
                'category' => $category ? [
                    'id' => $category->id,
                    'title' => $category->title,
                ] : null,
                'date' => data_get($item, 'add_on.date'),
                'file' => $file,
                'file_url' => UploadFile::resolvePublicUrl($file, 'report-document'),
                'sequence' => $item->sequence,
            ];
        })->toArray();
    }
}
