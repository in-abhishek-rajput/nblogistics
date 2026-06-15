{{-- resources/views/admin/layouts/vendor-scripts.blade.php --}}
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="{{ asset('lib/chart/chart.min.js') }}"></script>
<script src="{{ asset('lib/easing/easing.min.js') }}"></script>
<script src="{{ asset('lib/waypoints/waypoints.min.js') }}"></script>
<script src="{{ asset('lib/owlcarousel/owl.carousel.min.js') }}"></script>
<script src="{{ asset('lib/tempusdominus/js/moment.min.js') }}"></script>
<script src="{{ asset('lib/tempusdominus/js/moment-timezone.min.js') }}"></script>
<script src="{{ asset('lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js') }}"></script>


<script src="{{ asset('js/main.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>


 <!-- SweetAlert CDN -->
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <!-- Toastr JS -->
 <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

 <script>
    window.showTruckBookModal = function(modalId) {
        const modalEl = document.getElementById(modalId);
        if (!modalEl || typeof bootstrap === 'undefined') {
            return null;
        }

        const pausedOffcanvases = [];
        const disabledBackdrops = [];
        document.querySelectorAll('.offcanvas.show').forEach(function (offcanvasEl) {
            offcanvasEl.setAttribute('inert', '');
            const instance = bootstrap.Offcanvas.getInstance(offcanvasEl);
            if (instance && instance._focustrap) {
                instance._focustrap.deactivate();
                pausedOffcanvases.push({ el: offcanvasEl, trap: instance._focustrap });
            }
        });
        document.querySelectorAll('.offcanvas-backdrop.show').forEach(function (backdropEl) {
            backdropEl.style.pointerEvents = 'none';
            disabledBackdrops.push(backdropEl);
        });

        const onHidden = function () {
            pausedOffcanvases.forEach(function (item) {
                item.el.removeAttribute('inert');
                if (item.trap && typeof item.trap.activate === 'function') {
                    item.trap.activate();
                }
            });
            disabledBackdrops.forEach(function (backdropEl) {
                backdropEl.style.pointerEvents = '';
            });
            modalEl.removeEventListener('hidden.bs.modal', onHidden);
        };
        modalEl.addEventListener('hidden.bs.modal', onHidden);

        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl, { focus: false });
        modal.show();
        return modal;
    };

    window.hideTruckBookModal = function(modalId) {
        const modalEl = document.getElementById(modalId);
        if (!modalEl) {
            return;
        }

        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) {
            modalEl.querySelector(':focus')?.blur();
            modal.hide();
        }

        document.querySelectorAll('.offcanvas[inert]').forEach(function (offcanvasEl) {
            offcanvasEl.removeAttribute('inert');
            const instance = bootstrap.Offcanvas.getInstance(offcanvasEl);
            if (instance && instance._focustrap && typeof instance._focustrap.activate === 'function') {
                instance._focustrap.activate();
            }
        });
    };

    // Catch Livewire validation errors globally and display them using SweetAlert
    document.addEventListener('livewire:init', () => {
        Livewire.hook('request', ({ uri, options, payload, respond, succeed, fail }) => {
            fail(({ status, content, preventDefault }) => {
                if (status === 422) {
                    try {
                        let response = JSON.parse(content);
                        if (response.errors) {
                            let errorsHtml = '';
                            for (let field in response.errors) {
                                response.errors[field].forEach(errorMessage => {
                                    errorsHtml += '• ' + errorMessage + '<br>';
                                });
                            }
                            
                            if (errorsHtml) {
                                // SweetAlert is already loaded in the project
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Validation Error',
                                        html: '<div style="text-align: left;">' + errorsHtml + '</div>',
                                        confirmButtonText: 'OK',
                                        confirmButtonColor: '#0d6efd'
                                    });
                                } else {
                                    alert(errorsHtml.replace(/<br>/g, '\n'));
                                }
                            }
                        }
                    } catch (e) {
                        console.error('Error parsing validation response', e);
                    }
                }
            });
        });
    });
 </script>

@stack('scripts')