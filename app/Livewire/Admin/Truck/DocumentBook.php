<?php

namespace App\Livewire\Admin\Truck;

use App\Models\Truck;
use App\Models\TruckDocument;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentBook extends Component
{
    use WithFileUploads;

    public int $truckId;
    public Truck $truck;

    public ?int $editingDocumentId = null;
    public string $document_type = 'other';
    public string $document_name = '';
    public string $document_number = '';
    public string $expiry_date = '';
    public $document_file = null;
    public string $expense_amount = '';
    public string $expense_date = '';
    public string $notes = '';
    public bool $isDefaultCategory = false;

    public array $defaultDocumentCategories = [
        'insurance' => 'Insurance',
        'rc_permit' => 'RC Permit',
        'registration_certificate' => 'Registration Certificate',
        'national_permit' => 'National Permit',
        'road_tax' => 'Road Tax',
        'fitness_certificate' => 'Fitness Certificate',
        'driver_license' => 'Driver License',
    ];

    protected $listeners = [
        'openDocumentBookPanel' => 'openPanel',
        'editDocument' => 'editDocument',
        'deleteDocument' => 'deleteDocument',
    ];

    public function mount(int $truckId): void
    {
        $this->truckId = $truckId;
        $this->refreshData();
    }

    public function refreshData(): void
    {
        $this->truck = Truck::with('driver')->findOrFail($this->truckId);
    }

    public function openPanel(): void
    {
        $this->refreshData();
        $this->dispatch('openDocumentBookOffcanvas');
    }

    public function showAddOtherDocumentModal(): void
    {
        $this->resetForm();
        $this->document_type = 'other';
        $this->isDefaultCategory = false;
        $this->dispatch('showAddDocumentModal');
    }

    public function showEditDefaultDocument(string $categorySlug): void
    {
        $this->resetForm();
        $this->document_type = $categorySlug;
        $this->document_name = $this->defaultDocumentCategories[$categorySlug] ?? $categorySlug;
        $this->isDefaultCategory = true;

        $document = TruckDocument::where('truck_id', $this->truckId)
            ->where('document_type', $categorySlug)
            ->first();

        if ($document) {
            $this->loadDocumentIntoForm($document);
        }

        $this->dispatch('showAddDocumentModal');
    }

    public function editDocument(int $documentId): void
    {
        $document = TruckDocument::findOrFail($documentId);
        $this->resetForm();
        $this->loadDocumentIntoForm($document);
        $this->isDefaultCategory = $document->document_type !== 'other'
            && array_key_exists($document->document_type, $this->defaultDocumentCategories);
        $this->dispatch('showAddDocumentModal');
    }

    public function saveDocument(): void
    {
        $rules = [
            'document_name' => 'required|string|max:255',
            'expiry_date' => 'nullable|date',
            'document_number' => 'nullable|string|max:255',
            'document_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'expense_amount' => 'nullable|numeric|min:0',
            'expense_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ];

        $this->validate($rules);

        $existingDocument = null;
        if ($this->editingDocumentId) {
            $existingDocument = TruckDocument::findOrFail($this->editingDocumentId);
        } elseif ($this->isDefaultCategory) {
            $existingDocument = TruckDocument::where('truck_id', $this->truckId)
                ->where('document_type', $this->document_type)
                ->first();
        }

        $filePath = $this->storeDocumentFile($existingDocument?->document_file);

        $data = [
            'truck_id' => $this->truckId,
            'document_type' => $this->document_type,
            'document_name' => $this->document_name,
            'document_number' => $this->document_number ?: null,
            'expiry_date' => $this->expiry_date ?: null,
            'document_file' => $filePath,
            'expense_amount' => $this->expense_amount !== '' ? $this->expense_amount : null,
            'expense_date' => $this->expense_date ?: null,
            'notes' => $this->notes ?: null,
            'updated_by' => auth()->id(),
        ];

        if ($existingDocument) {
            $existingDocument->update($data);
        } else {
            TruckDocument::create(array_merge($data, [
                'created_by' => auth()->id(),
            ]));
        }

        $this->dispatch('documentBookUpdated');
        $this->dispatch('closeModal', 'addDocumentModal');
        $this->dispatch('openDocumentBookOffcanvas');
        $this->refreshData();
    }

    public function deleteDocument(int $documentId): void
    {
        $document = TruckDocument::findOrFail($documentId);
        $document->update(['deleted_by' => auth()->id()]);
        $document->delete();

        $this->dispatch('documentBookUpdated');
        $this->dispatch('openDocumentBookOffcanvas');
        $this->refreshData();
    }

    public function getDefaultDocumentsProperty(): array
    {
        $saved = TruckDocument::where('truck_id', $this->truckId)
            ->whereIn('document_type', array_keys($this->defaultDocumentCategories))
            ->get()
            ->keyBy('document_type');

        $documents = [];
        foreach ($this->defaultDocumentCategories as $slug => $label) {
            $documents[] = [
                'slug' => $slug,
                'label' => $label,
                'document' => $saved->get($slug),
            ];
        }

        return $documents;
    }

    public function getOtherDocumentsProperty()
    {
        return TruckDocument::where('truck_id', $this->truckId)
            ->where(function ($query) {
                $query->where('document_type', 'other')
                    ->orWhereNotIn('document_type', array_keys($this->defaultDocumentCategories));
            })
            ->orderByDesc('updated_at')
            ->get();
    }

    public function getTypesProperty()
    {
        return config('truck.types');
    }

    protected function resetForm(): void
    {
        $this->reset([
            'editingDocumentId',
            'document_type',
            'document_name',
            'document_number',
            'expiry_date',
            'document_file',
            'expense_amount',
            'expense_date',
            'notes',
            'isDefaultCategory',
        ]);
    }

    protected function loadDocumentIntoForm(TruckDocument $document): void
    {
        $this->editingDocumentId = $document->id;
        $this->document_type = $document->document_type;
        $this->document_name = $document->document_name;
        $this->document_number = $document->document_number ?? '';
        $this->expiry_date = $document->expiry_date?->format('Y-m-d') ?? '';
        $this->expense_amount = $document->expense_amount !== null ? (string) $document->expense_amount : '';
        $this->expense_date = $document->expense_date?->format('Y-m-d') ?? '';
        $this->notes = $document->notes ?? '';
    }

    protected function storeDocumentFile(?string $existingPath = null): ?string
    {
        if ($this->document_file) {
            return $this->document_file->store('trucks/documents', 'public');
        }

        return $existingPath;
    }

    public function render()
    {
        return view('livewire.admin.truck.document-book', [
            'defaultDocuments' => $this->defaultDocuments,
            'otherDocuments' => $this->otherDocuments,
            'types' => $this->types,
        ]);
    }
}
