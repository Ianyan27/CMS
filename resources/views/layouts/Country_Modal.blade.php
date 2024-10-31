{{-- Add Modal --}}
<div class="modal fade" id="addCountryModal" tabindex="-1" aria-labelledby="addCountryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-0">
            <div class="modal-header d-flex justify-content-between align-items-center"
                style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                        border:none;border-top-left-radius: 0; border-top-right-radius: 0;">
                <h5 class="modal-title font-educ" id="addCountryModalLabel">
                    <strong >Add New Country</strong>
                </h5>
                <!-- Adding the logo on the right side -->
                <img src="{{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png') }}" alt="Company Logo"
                    style="height: 30px;">
            </div>
            <div class="modal-body">
                <form
                    action="{{ Auth::user()->role == 'Admin' ? route('admin#add-country') : route('sales-admin#add-country') }}"
                    method="POST" id="addBUForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row row-margin-bottom row-border-bottom">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-educ" for="country-name">Country Name</label>
                                <input type="text" name="country-name" class="form-control fonts" id="country-name"
                                    required>
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
@if ($countries)
    @foreach ($countries as $country)
        <div class="modal fade" id="editCountryModal{{ $country->id }}" tabindex="-1"
            aria-labelledby="editCountryModalLabel{{ $country->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content rounded-0">
                    <div class="modal-header d-flex justify-content-between align-items-center"
                        style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                        border:none;border-top-left-radius: 0; border-top-right-radius: 0;">
                        <h5 class="modal-title font-educ" id="editCountryModalLabel{{ $country->id }}">
                            <strong>Edit Country</strong>
                        </h5>
                        <!-- Adding the logo on the right side -->
                        <img src="{{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png') }}" alt="Company Logo"
                            style="height: 30px;">
                    </div>
                    <div class="modal-body">
                        <form
                            action="{{ Auth::user()->role == 'Admin' ? route('admin#update-country', $country->id) : route('sales-admin#update-country', $country->id) }}"
                            method="POST" id="editCountryForm" enctype="multipart/form-data">
                            @csrf
                            <div class="row row-margin-bottom row-border-bottom">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-educ" for="country-name">Name</label>
                                        <input type="text" name="country-name" class="form-control fonts"
                                            id="country-name" value="{{ $country->name }}" required>
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
