<!-- Add Sales Agent Modal -->
<div class="modal fade" id="addSalesAgentModal" tabindex="-1" aria-labelledby="addSalesAgentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center"
                style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);border:none;">
                <h5 class="modal-title" id="addSalesAgentModalLabel">
                    <strong style="color: #91264c">Add Sales Agent</strong>
                </h5>
                <!-- Adding a logo on the right side -->
                <img src="{{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png') }}" alt="Company Logo"
                    style="height: 30px;">
            </div>
            <div class="modal-body">
                <form action="#" method="POST" id="addSalesAgentForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row row-margin-bottom row-border-bottom">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-educ" for="agentName">Sales Agent Name</label>
                                <input type="text" name="agentName" class="form-control fonts" id="agentName"
                                    placeholder="Enter agent name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-educ" for="hubspotId">Hubspot ID</label>
                                <input type="text" name="hubspotId" class="form-control fonts" id="hubspotId"
                                    placeholder="Enter Hubspot ID" required>
                            </div>
                        </div>
                    </div>
                    <div class="row row-margin-bottom ">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-educ" for="businessUnit">Owner Business Unit</label>
                                <input type="text" name="businessUnit" class="form-control fonts" id="businessUnit"
                                    placeholder="Enter business unit" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-educ" for="country">Country</label>
                                <input type="text" name="country" class="form-control fonts" id="country"
                                    placeholder="Enter country" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="border: none">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn" style="background: #91264c; color: white;">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
