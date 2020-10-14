<div class="container-1200 commission-container mx-auto p-1 p-md-4">


    <form id="commission_form">

        <div class="row">
            <div class="col-12 col-lg-5 pr-3 pr-lg-0">
                <div class="row">
                    <div class="col-5">
                        <div class="h4-responsive text-orange text-right mb-2">Income</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Checks In --}}
        <div class="row popout-row">

            <div class="col-12 col-lg-5 pr-3 pr-lg-0">

                <div class="row">

                    <div class="col-5 text-gray">
                        <div class="py-3 text-right">
                            Checks In
                        </div>
                    </div>

                    <div class="col-7">
                        <div class="popout-action pr-1 pr-lg-4 py-2 bg-blue-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="javascript: void(0)" class="btn btn-sm btn-primary show-view-add-button">View/Add</a>
                                </div>
                                <div class="badge badge-pill badge-primary py-1" id="checks_in_count"></div>
                                <div class="mr-2 font-10 text-success">
                                    <span id="checks_in_total"></span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-7 p-3 p-lg-0">

                <div class="popout-div">

                    <div class="popout top animated fast flipInX w-100 bg-blue-light active">

                        <div class="px-3 pb-3 pt-1">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="h4 mt-2 text-primary">Checks In</div>
                                <div>
                                    <a href="javascript: void(0)" class="btn btn-sm btn-success add-check-in-button"><i class="fa fa-plus mr-2"></i> Add</a>
                                </div>
                            </div>

                            <div class="view-add-div checks-in-div">

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Money In Escrow --}}
        <div class="row">

            <div class="col-12 col-lg-5 pr-2 pr-lg-0">
                <div class="row">
                    <div class="col-5">
                        <div class="h-100 text-gray d-flex justify-content-end align-items-center">
                            Money In Escrow
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="pr-4">
                            <input type="text" class="custom-form-element form-input money-decimal numbers-only text-success text-right pr-2">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Admin Fee - From Title --}}
        <div class="row">

            <div class="col-12 col-lg-5 pr-2 pr-lg-0">

                <div class="row">
                    <div class="col-5">
                        <div class="h-100 text-gray d-flex justify-content-end align-items-center">
                            Admin Fee - From Title
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="pr-4">
                            <input type="text" class="custom-form-element form-input money-decimal numbers-only text-success text-right pr-2">
                        </div>
                    </div>
                </div>

            </div>

        </div>

        {{-- Check Deductions --}}
        <div class="row popout-row">

            <div class="col-12 col-lg-5 pr-3 pr-lg-0">

                <div class="row">

                    <div class="col-5 text-gray">
                        <div class="py-3 text-right">
                            Check Deductions
                        </div>
                    </div>

                    <div class="col-7">
                        <div class="popout-action pr-1 pr-lg-4 py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="javascript: void(0)" class="btn btn-sm btn-primary show-view-add-button">View/Add</a>
                                </div>
                                <div class="badge badge-pill badge-primary py-1">2</div>
                                <div class="mr-2 font-10 text-danger">
                                    $1,200.00
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

            <div class="col-12 col-lg-7 p-3 p-lg-0">

                <div class="popout-div">

                    <div class="popout middle animated fast flipInX w-100">

                        <div class="p-3">

                            <div class="d-flex justify-content-between align-items-center">
                                <div class="h4 ml-4 mt-1 text-primary">Check Deductions</div>
                                <div>
                                    <a href="javascript: void(0)" class="btn btn-sm btn-success add-check-deduction-button"><i class="fa fa-plus mr-2"></i> Add</a>
                                </div>
                            </div>

                            <div class="view-add-div check-deductions-div">

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Admin Fee - Paid By Client --}}
        <div class="row">

            <div class="col-12 col-lg-5 pr-2 pr-lg-0">
                <div class="row">
                    <div class="col-5">
                        <div class="h-100 text-gray d-flex justify-content-end align-items-center">
                            Admin Fee - Paid By Client
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="pr-4">
                            <input type="text" class="custom-form-element form-input money-decimal numbers-only text-right pr-2">
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </form>

    <div class="modal fade draggable" id="edit_check_in_modal" tabindex="-1" role="dialog" aria-labelledby="edit_check_in_modal_title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary draggable-handle">
                    <h4 class="modal-title" id="edit_check_in_modal_title">Edit Check</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="edit_check_in_form">

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="edit-check-preview-div"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="h5 text-orange">Check Details</div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input datepicker required" name="edit_check_date" id="edit_check_date" data-label="Date On Check">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input numbers-only required" name="edit_check_number" id="edit_check_number" data-label="Check Number">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input money-decimal numbers-only required" name="edit_check_amount" id="edit_check_amount" data-label="Check Amount">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="h5 text-orange">Dates</div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input datepicker required" name="edit_date_received" id="edit_date_received" value="{{ date('Y-m-d') }}" data-label="Date Received">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input datepicker" name="edit_date_deposited" id="edit_date_deposited" data-label="Date Deposited">
                            </div>
                        </div>
                        <input type="hidden" name="check_id" id="check_id">
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                    <a class="btn btn-success" id="save_edit_check_in_button"><i class="fad fa-check mr-2"></i> Save</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade draggable" id="add_check_in_modal" tabindex="-1" role="dialog" aria-labelledby="add_check_in_modal_title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary draggable-handle">
                    <h4 class="modal-title" id="add_check_in_modal_title">Add Check In</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="add_check_in_form" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="h5 text-orange">Upload</div>
                            </div>
                            <div class="col-12">
                                <div><input type="file" accept="application/pdf" class="custom-form-element form-input-file required" name="check_upload" id="check_upload" data-label="Click to search or Drag and Drop files here"></div>
                            </div>
                            <div class="col-12">
                                <div class="check-preview-div"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="h5 text-orange">Check Details</div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input datepicker required" name="check_date" id="check_date" data-label="Date On Check">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input numbers-only required" name="check_number" id="check_number" data-label="Check Number">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input money-decimal numbers-only required" name="check_amount" id="check_amount" data-label="Check Amount">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="h5 text-orange">Dates</div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input datepicker required" name="date_received" id="date_received" value="{{ date('Y-m-d') }}" data-label="Date Received">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input datepicker" name="date_deposited" id="date_deposited" data-label="Date Deposited">
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                    <button type="button" class="btn btn-success" id="save_add_check_in_button"><i class="fad fa-check mr-2"></i> Save</button>
                </div>
            </div>
        </div>
    </div>

</div>


