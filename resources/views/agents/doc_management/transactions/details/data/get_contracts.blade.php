<div class="container p-1 p-sm-4">
    <div class="row">
        <div class="col-12">

            <div class="h4-responsive text-primary ml-3"><i class="fad fa-file-signature mr-2 mb-3"></i> Accepted {{ $for_sale ? 'Contracts' : 'Leases' }}</div>

            <div class="row">

            @foreach($contracts as $contract)
                @php
                $status_id = $resource_items -> GetResourceID('Active', 'contract_status');
                $status = ($contract -> Status == $status_id ? 'active' : null);
                @endphp
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3 mb-3">
                    <a href="/agents/doc_management/transactions/transaction_details/{{ $contract -> Contract_ID }}/contract/">

                        <div class="contract-div @if($status != '') bg-primary @else bg-default @endif p-3 mb-2 m-sm-2 h-100 z-depth-1 {{ $status }}">
                            <div class="d-flex justify-content-start align-items-center">
                                <div class="mr-3"><i class="fad fa-file-signature text-white mr-2 fa-3x"></i></div>
                                <div>
                                    <div class="h4-responsive text-white">{{ $resource_items -> GetResourceName($contract -> Status) }}</div>
                                    <div class="h4-responsive text-white">${{ $for_sale ? number_format($contract -> ContractPrice) : number_format($contract -> LeaseAmount) }}</div>
                                </div>
                            </div>
                            <hr class="bg-white">
                            <div class="row text-white">
                                @if($for_sale)
                                <div class="col-5 text-right">Contact Date</div>
                                <div class="col-7 text-left">{{ date('m/d/Y', strtotime($contract -> ContractDate)) }}</div>
                                @endif
                                <div class="col-5 text-right">{{ $for_sale ? 'Settle' : 'Lease' }} Date</div>
                                <div class="col-7 text-left">{{ date('m/d/Y', strtotime($contract -> CloseDate)) }}</div>

                                <div class="col-12">
                                    <hr class="bg-white">
                                </div>

                                <div class="col-5 text-right">{{ $for_sale ? 'Buyer' : 'Renter' }}'s Agent</div>
                                <div class="col-7 text-left">{{ $contract -> BuyerAgentFirstName.' '.$contract -> BuyerAgentLastName }}</div>
                                <div class="col-5 text-right">Company</div>
                                <div class="col-7 text-left">{{ $contract -> BuyerOfficeName }}</div>

                                <div class="col-12">
                                    <hr class="bg-white">
                                </div>

                                <div class="col-5 text-right">{{ $for_sale ? 'Buyer' : 'Renter' }}</div>
                                <div class="col-7 text-left">{{ $contract -> BuyerOneFirstName.' '.$contract -> BuyerOneLastName }}</div>
                                @if($contract -> BuyerTwoFirstName)
                                <div class="col-5 text-right">{{ $for_sale ? 'Buyer' : 'Renter' }}</div>
                                <div class="col-7 text-left">{{ $contract -> BuyerTwoFirstName.' '.$contract -> BuyerTwoLastName }}</div>
                                @endif

                            </div>
                        </div>

                    </a>
                </div>

            @endforeach

            </div>

        </div>
    </div>
</div>
