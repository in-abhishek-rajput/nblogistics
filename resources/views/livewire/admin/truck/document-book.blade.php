<div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/css/lightgallery-bundle.min.css" />

    <div wire:ignore.self class="offcanvas offcanvas-end" tabindex="-1" id="documentBookOffcanvas" aria-labelledby="documentBookOffcanvasLabel"
        style="width:500px;">
        <div class="offcanvas-header border-bottom py-3 px-4">
            <div>
                <h5 class="offcanvas-title fw-bold" id="documentBookOffcanvasLabel">Document Book</h5>
                <div class="text-muted small mt-1">
                    {{ $truck->truck_number }} · {{ $types[$truck->truck_type] ?? ucfirst($truck->truck_type) }} · {{ ucfirst($truck->status ?? 'Unknown') }}
                </div>
            </div>
            <button type="button" class="btn-close text-dark" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-4 position-relative" style="min-height: calc(100vh - 110px);">
            <div class="row g-3 mb-3">
                @foreach ($defaultDocuments as $item)
                    @php
                        $document = $item['document'];
                        $status = $document?->expiry_status ?? 'not_set';
                        $statusLabels = [
                            'not_set' => ['label' => 'Not Set', 'class' => 'bg-secondary'],
                            'valid' => ['label' => 'Valid', 'class' => 'bg-success'],
                            'expiring_soon' => ['label' => 'Expiring Soon', 'class' => 'bg-warning text-dark'],
                            'expired' => ['label' => 'Expired', 'class' => 'bg-danger'],
                        ];
                        $statusInfo = $statusLabels[$status] ?? $statusLabels['not_set'];
                    @endphp
                    <div class="col-12">
                        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <div class="fw-semibold">{{ $item['label'] }}</div>
                                        <span class="badge {{ $statusInfo['class'] }} mt-1" style="font-size:0.68rem;">{{ $statusInfo['label'] }}</span>
                                        @if ($document?->expiry_date)
                                            <div class="text-muted small mt-1">Expires: {{ $document->expiry_date->format('d M Y') }}</div>
                                        @endif
                                    </div>
                                    @if ($document?->document_file && Storage::disk('public')->exists($document->document_file))
                                        @php
                                            $fileUrl = asset('storage/' . $document->document_file);
                                            $extension = strtolower(pathinfo($document->document_file, PATHINFO_EXTENSION));
                                        @endphp
                                        <div data-lightbox="doc-{{ $item['slug'] }}">
                                            @if (in_array($extension, ['jpg', 'jpeg', 'png'], true))
                                                <a href="{{ $fileUrl }}" data-src="{{ $fileUrl }}" data-download-url="{{ $fileUrl }}">
                                                    <img src="{{ $fileUrl }}" alt="{{ $item['label'] }}" class="img-thumbnail" style="width:64px;height:64px;object-fit:cover;cursor:pointer;">
                                                </a>
                                            @else
                                                <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-muted small text-center" style="width:64px;">
                                            <i class="bi bi-file-earmark fs-3 d-block"></i>
                                            No file
                                        </div>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="showEditDefaultDocument('{{ $item['slug'] }}')">
                                    {{ $document ? 'Edit Expiry' : 'Add Expiry' }}
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($otherDocuments->count())
                <h6 class="mb-3">Other Documents</h6>
                <div class="row g-3 mb-3">
                    @foreach ($otherDocuments as $document)
                        @php
                            $status = $document->expiry_status;
                            $statusLabels = [
                                'not_set' => ['label' => 'Not Set', 'class' => 'bg-secondary'],
                                'valid' => ['label' => 'Valid', 'class' => 'bg-success'],
                                'expiring_soon' => ['label' => 'Expiring Soon', 'class' => 'bg-warning text-dark'],
                                'expired' => ['label' => 'Expired', 'class' => 'bg-danger'],
                            ];
                            $statusInfo = $statusLabels[$status] ?? $statusLabels['not_set'];
                        @endphp
                        <div class="col-12">
                            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <div class="fw-semibold">{{ $document->document_name }}</div>
                                            <span class="badge {{ $statusInfo['class'] }} mt-1" style="font-size:0.68rem;">{{ $statusInfo['label'] }}</span>
                                            @if ($document->expiry_date)
                                                <div class="text-muted small mt-1">Expires: {{ $document->expiry_date->format('d M Y') }}</div>
                                            @endif
                                            @if ($document->expense_amount)
                                                <div class="text-muted small">Expense: ₹ {{ number_format($document->expense_amount, 2) }}</div>
                                            @endif
                                        </div>
                                        @if ($document->document_file && Storage::disk('public')->exists($document->document_file))
                                            @php
                                                $fileUrl = asset('storage/' . $document->document_file);
                                                $extension = strtolower(pathinfo($document->document_file, PATHINFO_EXTENSION));
                                            @endphp
                                            <div data-lightbox="doc-other-{{ $document->id }}">
                                                @if (in_array($extension, ['jpg', 'jpeg', 'png'], true))
                                                    <a href="{{ $fileUrl }}" data-src="{{ $fileUrl }}" data-download-url="{{ $fileUrl }}">
                                                        <img src="{{ $fileUrl }}" alt="{{ $document->document_name }}" class="img-thumbnail" style="width:64px;height:64px;object-fit:cover;cursor:pointer;">
                                                    </a>
                                                @else
                                                    <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-file-earmark-pdf"></i> PDF
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" wire:click="editDocument({{ $document->id }})">Edit</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="confirm('Delete this document?') || event.stopImmediatePropagation()"
                                            wire:click="deleteDocument({{ $document->id }})">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <button type="button" class="btn btn-primary w-100" wire:click="showAddOtherDocumentModal">
                + Add Other Document
            </button>
        </div>
    </div>

    @include('livewire.admin.truck.modals.add-document-modal')

    @script
        <script>
            const documentCanvasEl = document.getElementById('documentBookOffcanvas');
            const documentOffcanvas = new bootstrap.Offcanvas(documentCanvasEl);

            window.addEventListener('openDocumentBookOffcanvas', () => {
                documentOffcanvas.show();
            });

            window.addEventListener('showAddDocumentModal', () => {
                const modal = new bootstrap.Modal(document.getElementById('addDocumentModal'));
                modal.show();
            });

            window.addEventListener('closeModal', event => {
                const modalElement = document.getElementById(event.detail);
                if (!modalElement) return;
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    const focusedElement = modalElement.querySelector(':focus');
                    if (focusedElement) focusedElement.blur();
                    modal.hide();
                }
            });

            function initDocumentLightGallery() {
                if (typeof lightGallery === 'undefined') return;
                document.querySelectorAll('[data-lightbox]').forEach(el => {
                    if (el.classList.contains('lg-initialized')) return;
                    lightGallery(el, {
                        selector: 'a[data-src]',
                        plugins: typeof lgZoom !== 'undefined' ? [lgZoom] : [],
                        download: true,
                        zoom: true,
                    });
                    el.classList.add('lg-initialized');
                });
            }

            document.addEventListener('DOMContentLoaded', initDocumentLightGallery);
            window.addEventListener('livewire:update', () => {
                initDocumentLightGallery();
                if (documentCanvasEl.classList.contains('show')) {
                    documentOffcanvas.show();
                }
            });
        </script>
    @endscript

    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/lightgallery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/plugins/zoom/lg-zoom.min.js"></script>
</div>
