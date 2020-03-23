<!-- Search form -->
<div class="ml-5">
    <input class="form-input-search" id="main_search" type="text" placeholder="Search" aria-label="Search">
</div>
<ul class="nav nav-tabs mt-5" id="sub_nav_tabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="doc_management_tab" data-toggle="tab" href="#doc_management_div" role="tab" aria-controls="doc_management_div"
            aria-selected="true">Doc Management</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="agents_tab" data-toggle="tab" href="#agents_div" role="tab" aria-controls="agents_div"
            aria-selected="false">Agents</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="loan_officers_tab" data-toggle="tab" href="#loan_officers_div" role="tab" aria-controls="loan_officers_div"
            aria-selected="false">Loan Officers</a>
    </li>
</ul>
<div class="tab-content card pt-1" id="sub_menu_divs">
    <div class="tab-pane fade show active" id="doc_management_div" role="tabpanel" aria-labelledby="doc_management_tab">
        <ul class="mt-5">
            <li>
                <a class="text-primary" href="/doc_management/create/upload/files">
                    <i class="fas fa-caret-right pl-2 pr-3"></i>View/Add Uploaded Files
                </a>
            </li>
            <li>
                <a class="text-primary" href="/doc_management/resources/resources">
                    <i class="fas fa-caret-right pl-2 pr-3"></i>Resources
                </a>
            </li>
            <li>
                <a class="text-primary" href="/doc_management/create/fill/fillable_files">
                    <i class="fas fa-caret-right pl-2 pr-3"></i>Fillable Files
                </a>
            </li>
            <li>
                <a class="text-primary" href="/doc_management/checklists">
                    <i class="fas fa-caret-right pl-2 pr-3"></i>Checklists
                </a>
            </li>
        </ul>
    </div>

</div>






