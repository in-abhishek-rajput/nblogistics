<div wire:ignore.self class="modal fade" id="addDocumentModal" tabindex="-1" aria-labelledby="addDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold" id="addDocumentModalLabel">
                    {{ $editingDocumentId ? 'Edit Document' : ($isDefaultCategory ? 'Add/Edit Document' : 'Add Other Document') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form wire:submit.prevent="saveDocument">
                <div class="modal-body py-0 px-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Document Name *</label>
                        <input type="text" wire:model.live="document_name" class="form-control" required
                            @if($isDefaultCategory) readonly @endif>
                        @error('document_name') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Expiry Date</label>
                        <input type="date" wire:model.live="expiry_date" class="form-control">
                        @error('expiry_date') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Document Number</label>
                        <input type="text" wire:model.live="document_number" class="form-control">
                        @error('document_number') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Document Image</label>
                        <input type="file" wire:model.live="document_file" class="form-control" accept="image/jpeg,image/png,image/jpg,application/pdf">
                        @if ($document_file)
                            <div class="mt-2 text-muted small">File selected: {{ $document_file->getClientOriginalName() }}</div>
                        @endif
                        @error('document_file') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Expense Amount</label>
                        <input type="number" wire:model.live="expense_amount" class="form-control" min="0" step="0.01">
                        @error('expense_amount') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Expense Date</label>
                        <input type="date" wire:model.live="expense_date" class="form-control">
                        @error('expense_date') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea wire:model.live="notes" class="form-control" rows="2"></textarea>
                        @error('notes') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Document</button>
                </div>
            </form>
        </div>
    </div>
</div>
