{{-- Add Modal --}}
<div class="modal fade" id="addBUModal" tabindex="-1" aria-labelledby="addBUModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-0">
            <div class="modal-header d-flex justify-content-between align-items-center"
                style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                        border:none;border-top-left-radius: 0; border-top-right-radius: 0;">
                <h5 class="modal-title" id="addBUModalLabel">
                    <strong class="headings">Add New BU</strong>
                </h5>
                <!-- Adding the logo on the right side -->
                <img src="{{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png') }}" alt="Company Logo"
                    style="height: 30px;">
            </div>
            <div class="modal-body">
                <form action="{{ Auth::user()->role == 'Admin' ? route('admin#add-bu') : route('sales-admin#add-bu') }}"
                    method="POST" id="addBUForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row row-margin-bottom row-border-bottom">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-educ" for="bu-name">Bussines Unit Name</label>
                                <input type="text" name="bu-name" class="form-control fonts" id="bu-name" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn" style="background: #91264c; color:white;">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- edit modal --}}
@if ($bus)
    @foreach ($bus as $bu)
        <div class="modal fade" id="editBUModal{{ $bu->id }}" tabindex="-1"
            aria-labelledby="editBUModalLabel{{ $bu->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content rounded-0">
                    <div class="modal-header d-flex justify-content-between align-items-center"
                        style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                        border:none;border-top-left-radius: 0; border-top-right-radius: 0;">
                        <h5 class="modal-title" id="editBUModalLabel{{ $bu->id }}">
                            <strong>Edit Business Unit</strong>
                        </h5>
                        <!-- Adding the logo on the right side -->
                        <img src="{{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png') }}" alt="Company Logo"
                            style="height: 30px;">
                    </div>
                    <div class="modal-body">
                        <form
                            action="{{ Auth::user()->role == 'Admin' ? route('admin#update-bu', $bu->id) : route('sales-admin#update-bu', $bu->id) }}"
                            method="POST" id="editBUForm" enctype="multipart/form-data">
                            @csrf
                            <div class="row row-margin-bottom row-border-bottom">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-educ" for="bu-name">Name</label>
                                        <input type="text" name="bu-name" class="form-control fonts" id="bu-name"
                                            value="{{ $bu->name }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn"
                                    style="background: #91264c; color:white;">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif
