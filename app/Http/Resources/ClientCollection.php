<?php

namespace App\Http\Resources;

use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ClientCollection extends ResourceCollection
{
    public $collects = ClientResource::class;
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'data' => $this->collection,
            'links' => $this->getLinks(),
            'meta' => $this->getMeta()
        ];
    }

    public function toResponse($request)
    {
        return JsonResource::toResponse($request);
    }

    public function getLinks()
    {
        return [
            'path' => $this->getOptions()['path'],
            'firstPageUrl' => $this->url(1),
            'lastPageUrl' => $this->url($this->lastPage()),
            'prevPageUrl' => $this->previousPageUrl(),
            'nextPageUrl' => $this->nextPageUrl(),
        ];
    }

    public function getMeta()
    {
        return [
            'currentPage' => $this->currentPage(),
            'from' => $this->firstItem(),
            'lastPage' => $this->lastPage(),
            'perPage' => $this->perPage(),
            'to' => $this->lastItem(),
            'total' => $this->total(),
            'count' => $this->count(),
        ];
    }
}
