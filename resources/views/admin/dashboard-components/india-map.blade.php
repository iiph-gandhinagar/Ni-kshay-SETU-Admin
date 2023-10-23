<div class="col-xl-7 col-lg-7 col-md-7 col-sm-12 col-12">
    <div class="card cus-border-top height-100 custom-card-border">
        <div class="widget-chart widget-chart2">
            <div class="col-12">
                <h4 class="heading mt-3" id="map-title"></h4>
                <div class="widget-content state-subscriber-card" id="state-national-count">
                    <span class=" box-text-color map-subscriber-count"></span>
                    <span class=" total-count ml-1" id="state-subscriber"></span>{{-- <div  style="cursor: pointer;"> --}}
                </div><br>
                <span id="back-btn" style="position: absolute;top: 0;"></span>
            </div>
            

            <div class="widget-chart-wrapper widget-chart-wrapper-lg opacity-10">
                <div id="chart" class="text-center col-12 map-container p-4 mt-0 mb-5" style="max-height:510px;height:510px">
                </div>
            </div>
            <div id="show-hide-map"></div>
            <div class="pos-bl pl-3 ml-2 pb-4" id="map_slider_wrapper">
                <div class="card bg-transparent border-0 rounded-0">
                    <div class="map_slider_image round" style="height: 13px;width: 124px;background: url(./legend.svg)">
                    </div>
                    <div class="for-slider mt-n5">

                        <input id="map_change" name="slider" style="display:none" readonly="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('graph-scripts')
    <script>
        let mat_view_name = {
            "state": "State Name",
            "state_id": "Sr. No.",
            "State Name": "State Name",
            "corrected_total_points": "TB Index",
            "district_name": "District Name",
            "score": "TB Index",
            "notify_target": "1a Annual Target ",
            "notify_target_quarter": "1b Target prorata",
            "notify_total": "1c Achievement ",
            "points_notify": "1. TB Notification (wt=20)",
            "notify_hiv_total": "2a Notification (Target)",
            "notify_hiv_screened": "2b HIV Status Known (Achievement)",
            "points_notify_hiv": "2. HIV status  known(wt=10)",
            "udst_notified": "3a Notification",
            "udst_tested": "3b UDST eligibility (Target)",
            "udst_tested_quarter": "3c  UDST Tested (Achievement)",
            "points_udst": "3. UDST (wt=10)",
            "success_total_patient": "4a Notification (Target)",
            "success_patients": "4b Treatment Success (Achievement)",
            "points_sucess_rate": "4. Treatment Success (wt=15)",
            "npy_total_benef": "5a Beneficiaries Eligible(NPY)-(Target)",
            "npy_benef_paid": "5b Beneficiaries Paid atleast once (Achievement)",
            "points_npy": "5. Beneficiaries paid-NPY (wt=10)",
            "drtb_mdr_patients": "6a DRTB diagnosed (Target)",
            "drtb_initiated": "6b DRTB Treatment Initiated (Achievement)",
            "points_drtb_patients": "6. DRTB Treatment initiation(wt=15)",
            "exp_state_rop": "7a  ROP (in Lakhs)",
            "exp_total": "7b Expenditure (in Lakhs)",
            "points_expenditure": "7. Expenditure (wt=10)",
            "child_indentified": "8a Child Contacts",
            "child_treatment": "8b Child contacts on TB treatment",
            "child_eligible_chemo": "8c Chemoprophylaxis eligibility",
            "child_given_chemo": "8d Chemoprophylaxis given",
            "points_chemo": "8. Chemoprophylaxis (wts=5)",
            "plhiv_active_care": "9a PLHIV on active care",
            "plhiv_eligible_tpt": "9b PLHIV eligible for TPT",
            "plhiv_initiated_tpt": "9c Cumulative PLHIV initiated on TPT",
            "points_plhiv": "9. TPT to PLHIV(wts=5)"
        }
        let tooltip_data = [{
            "id": 1,
            "title": "TB Index",
            "formula": "Sum of all the important indicators to calculate TB index",
            "note": "TB Index has been calculated only at State level",
            "other_info": "Important indicator are: 1. TB notification (wt=20) 2. HIV status  known (wt=10) 3. UDST (wt=10) 4. Treatment success (wt=15) 5. Beneficiaries paid-npy (wt=10) 6. DRTB treatment initiation (wt=15) 7. Expenditure (wt=10) 8. Chemoprophylaxis (wts=5) 9. TPT to plhiv (wts=5)"
        }, {
            "id": 2,
            "title": "TB Notification",
            "formula": "(%Target achieved in TB notification in a state or district/total target*20)",
            "note": "Total Target is 100% and TB Notification weightage is 20",
            "other_info": "In case if the State/District % Target achieved in TB Notification is greater than the Total Target (i.e. 100%)",
            "then the maximum TB Notification points can be allotted to that State/District (i.e. 20)": null
        }, {
            "id": 3,
            "title": "HIV Testing",
            "formula": "(%Patients with known HIV testing in a state or district/total target*10)",
            "note": "Total Target is 100% and  HIV Status known weightage is 10",
            "other_info": "In case if the State/District %Patients with known HIV testing is greater than the Total Target (i.e. 100%), then the maximum HIV Status known points can be allotted to that State/District (i.e. 10)"
        }, {
            "id": 4,
            "title": "UDST",
            "formula": "(%TB notified patients tested for UDST in a state or district/total target*10)",
            "note": "Total Target is 100% and  UDST weightage is 10",
            "other_info": "In case if the State/District %TB notified patients tested for UDST  is greater than the Total Target (i.e. 100%), then the maximum UDST points can be allotted to that State/District (i.e. 10)"
        }, {
            "id": 5,
            "title": "Success Rate",
            "formula": "(Treatment success rate in a state or district/total target*15)",
            "note": "Total Target is 90% and  Treatment Success weightage is 15",
            "other_info": "In case if the State/District Treatment Success Rate  is greater than the Total Target (i.e. 90%), then the maximum Treatment Success can be allotted to that State/District (i.e. 15)"
        }, {
            "id": 6,
            "title": "NPY",
            "formula": "(%Beneficiaries paid-NPY in a state or district/total target*10)",
            "note": "Total Target is 100% and  Beneficiaries Paid-NPY weightage is 10",
            "other_info": "In case if the State/District %Beneficiaries paid-NPY is greater than the Total Target (i.e. 100%), then the maximum Beneficiaries Paid-NPY can be allotted to that State/District (i.e. 10)"
        }, {
            "id": 7,
            "title": "DRTB",
            "formula": "(%DRTB patients initiated on treatment in a state or district/total target*15)",
            "note": "Total Target is 100% and DRTB Treatment initiation weightage is 15",
            "other_info": "In case if the State/District %DRTB patients initiated on Treatment is greater than the Total Target (i.e. 100%), then the maximum DRTB Treatment initiation can be allotted to that State/District (i.e. 15)"
        }, {
            "id": 8,
            "title": "Expenditure",
            "formula": "(%Expenditure in a state or district/total target*10)",
            "note": "Total Target is 100% and Expenditure weightage is 10",
            "other_info": "In case if the State/District %Expenditure is greater than the Total Target (i.e. 100%), then the maximum Expenditure can be allotted to that State/District (i.e. 10)"
        }, {
            "id": 9,
            "title": "Chemoprophylaxis",
            "formula": "(%Children given chemoprophylaxis in a state or district/total target*5)",
            "note": "Total Target is 100% and  Chemoprophylaxis weightage is 5",
            "other_info": "In case if State/District %children given chemoprophylaxis is greater than the Total Target (i.e. 100%), then the maximum Chemoprophylaxis can be allotted to that State/District (i.e. 5)"
        }, {
            "id": 10,
            "title": "PLHIV",
            "formula": "% Eligible PLHIV received TB preventive therapy in a state or district/total target*5)",
            "note": "Total Target is 100% and TPT to PLHIV weightage is 5",
            "other_info": "In case if State/District % eligible PLHIV received TB preventive therapy is greater than the Total Target (i.e. 100%), then the maximum TPT to PLHIV can be allotted to that State/District (i.e. 5)"
        }]
        let data_table_dict = [{
            "data": "State Name",
            "title": "State Name"
        }, {
            "data": "TB Score",
            "title": "TB Score"
        }, {
            "data": "Points on TB notification achieved (wts 20)",
            "title": "Points on TB notification achieved (wts 20)"
        }, {
            "data": "Points on TB notified patients with HIV (wts 10)",
            "title": "Points on TB notified patients with HIV (wts 10)"
        }, {
            "data": "Points on UDST (wts 10)",
            "title": "Points on UDST (wts 10)"
        }, {
            "data": "Points on treatment success rate (wts 15)",
            "title": "Points on treatment success rate (wts 15)"
        }, {
            "data": "Points on Beneficiaries Paid under Nikshay Poshan Yojana (wts 10)",
            "title": "Points on Beneficiaries Paid under Nikshay Poshan Yojana (wts 10)"
        }, {
            "data": "Points on DRTB Patients treatment initiation regimen (wts 15)",
            "title": "Points on DRTB Patients treatment initiation regimen (wts 15)"
        }, {
            "data": "Points Expenditure (wts 10)",
            "title": "Points Expenditure (wts 10)"
        }, {
            "data": "Points on chemoprophylaxis (wts 5)",
            "title": "Points on chemoprophylaxis (wts 5)"
        }, {
            "data": "Points PLHIV Received (wts 5)",
            "title": "Points PLHIV Received (wts 5)"
        }]
        let annotations_coordinates = [{
            "ny": 80,
            "nx": 85
        }, {
            "ny": 185,
            "nx": 85
        }, {
            "ny": 345,
            "nx": 85
        }, {
            "ny": 90,
            "nx": 390
        }]
        let order_ind = ["corrected_total_points", "points_notify", "points_notify_hiv", "points_udst",
            "points_sucess_rate", "points_npy", "points_drtb_patients", "points_expenditure", "points_chemo",
            "points_plhiv"
        ]
    </script>
    <script type="text/javascript">
        $('document').ready(function name(params) {
            draw_india_map()
        })

        function updateMapBoxCount(data) {
            $('#state-subscriber').empty();
            console.log("data--->IN", data);
            $('#state-subscriber').text(data);
        }
    </script>
@endpush
