@if ($errors->has('activity-attachments'))
    <!-- Modern Styled Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header border-0"
                    style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);border:none;
                border-top-left-radius: 0; border-top-right-radius: 0;">
                    <h5 class="modal-title fw-bold" id="errorModalLabel" style="color: #91264c">Upload Error</h5>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger border-0 p-4 rounded-3">
                        <ul class="mb-0 list-unstyled">
                            @foreach ($errors->all() as $error)
                                <li class="mb-2">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
