<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class LoanAttachmentResource extends JsonResource
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
            'loan_application_id' => $this->loan_application_id,
            'document_type' => new DocumentTypeResource($this->documentType), // Include document type details
            'path' => Storage::url($this->path), // Path to the document file
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
