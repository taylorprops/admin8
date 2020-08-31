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
                        <div class="popout-action pr-1 pr-lg-2 py-2 bg-blue-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <button type="button" class="btn btn-sm btn-primary show-view-add-button">View/Add</button>
                                </div>
                                <div class="badge badge-pill badge-primary py-1">2</div>
                                <div class="mr-2 font-10 text-success">
                                    $4,500.00
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-7 p-3 p-lg-0">

                <div class="popout-div">

                    <div class="popout top animated fast flipInX w-100 bg-blue-light">

                        <div class="p-3">

                            <div class="d-flex justify-content-between align-items-center">
                                <div class="h4 ml-4 mt-1 text-primary">Checks In</div>
                                <div>
                                    <button class="btn btn-sm btn-success add-check-in-button"><i class="fa fa-plus mr-2"></i> Add</button>
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
                        <div class="pr-2">
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
                        <div class="pr-2">
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
                        <div class="popout-action pr-1 pr-lg-2 py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <button type="button" class="btn btn-sm btn-primary show-view-add-button">View/Add</button>
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
                                    <button class="btn btn-sm btn-success add-check-deduction-button"><i class="fa fa-plus mr-2"></i> Add</button>
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
                        <div class="pr-2">
                            <input type="text" class="custom-form-element form-input money-decimal numbers-only text-right pr-2">
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </form>




</div>
