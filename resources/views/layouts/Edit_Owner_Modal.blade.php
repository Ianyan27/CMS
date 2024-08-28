<!-- Edit Owner Modal -->
<div class="modal fade" id="editOwnerModal" tabindex="-1" aria-labelledby="editOwnerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-0">
            <div class="modal-header d-flex justify-content-between align-items-center"
            style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);
                        border:none;border-top-left-radius: 0; border-top-right-radius: 0;">
                <h5 class="modal-title font-educ" id="editOwnerModalLabel">
                    <strong>Edit Owner</strong>
                </h5>
                <img src="{{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png') }}" alt="Company Logo" style="height: 30px;">
            </div>
            <div class="modal-body">
                <form action="{{ route('owner#update-owner', $editOwner->owner_pid) }}" method="POST" id="editOwnerForm">
                    @csrf
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-educ" for="name">Name</label>
                                <input type="text" class="form-control fonts" id="name" name="name" value="{{ $editOwner->owner_name }}" readonly>
                            </div>
                            <div class="form-group">
                                <label class="font-educ" for="email">Email</label>
                                <input type="email" class="form-control fonts" id="email" name="email" value="{{ $editOwner->owner_email_id }}" readonly>
                            </div>
                            <div class="form-group">
                                <label class="font-educ" for="owner-hubspot-id">HubSpot ID</label>
                                <input type="text" class="form-control fonts" id="owner-hubspot-id" name="hubspot_id" value="{{ $editOwner->owner_hubspot_id }}" readonly>
                            </div>
                        </div>
                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-educ" for="business-unit">Business Unit</label>
                                <input type="text" class="form-control fonts" id="business-unit" name="business_unit" value="{{ $editOwner->owner_business_unit }}" required>
                            </div>
                            <div class="form-group">
                                <label class="font-educ" for="country">Country</label>
                                <input type="text" class="form-control fonts" id="country" name="country" value="{{ $editOwner->country }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn" style="background: #91264c; color: white;">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>