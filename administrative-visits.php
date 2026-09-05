<?php 
require("connect.php");
$title = "Administrative Visits";
require("header.php");
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <?php include("content-header.php"); ?>
    <link rel="stylesheet" href="assets/node_modules/datatables/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="dist/dist/css/bootstrapValidator.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.2.7/css/select.dataTables.min.css">
    
    <style>
        .filter-box {
            background: #fff;
            padding: 15px 15px 5px 15px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 15px;
        }
        .kpi-badges .badge {
            font-size: 13px;
            padding: 8px 14px;
            margin-right: 6px;
            margin-bottom: 6px;
            font-weight: 600;
        }
        .store-pill {
            display: inline-block;
            padding: 2px 7px;
            margin: 2px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 3px;
            background: #eef2f7;
            color: #333;
            border: 1px solid #dcdfe6;
        }
        .table-wrap {
            overflow-x: auto;
        }
        .modal-label {
            font-weight: bold;
            color: #555;
        }
    </style>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-12">
                
                <!-- Search / Filter Card -->
                <div class="filter-box">
                    <h4 style="margin-top: 0; margin-bottom: 15px; font-weight: 600;">
                        <i class="fa fa-filter text-green"></i> Search & Filter Administrative Visits
                    </h4>
                    
                    <form id="filterForm" class="form-inline" onsubmit="return false;">
                        
                        <!-- Date Range -->
                        <div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
                            <label style="display: block; font-size: 12px; margin-bottom: 3px;">Date Range:</label>
                            <div class="input-group" style="width: 210px;">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" class="form-control input-sm" id="reservation" name="reservation" placeholder="Select Date Range" autocomplete="off"/>
                            </div>
                        </div>

                        <!-- Employee Select -->
                        <div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
                            <label style="display: block; font-size: 12px; margin-bottom: 3px;">Employee:</label>
                            <select class="form-control select2 input-sm" id="employee" name="employee" style="width: 180px;">
                                <option value="">All Employees</option>
                                <?php 
                                $empRes = mysqli_query($con, "SELECT id, name, empid FROM employees WHERE usertype IN (1, 3) ORDER BY name ASC");
                                while($emp = mysqli_fetch_assoc($empRes)) {
                                    $label = htmlspecialchars($emp['name']) . ($emp['empid'] ? ' (' . htmlspecialchars($emp['empid']) . ')' : '');
                                    echo "<option value='{$emp['id']}'>{$label}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Category Select -->
                        <div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
                            <label style="display: block; font-size: 12px; margin-bottom: 3px;">Visit Category:</label>
                            <select class="form-control select2 input-sm" id="reason_category" name="reason_category" style="width: 190px;">
                                <option value="">All Categories</option>
                                <option value="1">1 - New Distributor Search</option>
                                <option value="2">2 - New Distributor KYC</option>
                                <option value="3">3 - Miscellaneous Visit</option>
                            </select>
                        </div>

                        <!-- City Select -->
                        <div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
                            <label style="display: block; font-size: 12px; margin-bottom: 3px;">City:</label>
                            <select class="form-control select2 input-sm" id="city" name="city" style="width: 150px;">
                                <option value="">All Cities</option>
                                <?php 
                                $cityRes = mysqli_query($con, "SELECT DISTINCT city FROM administrative_visit WHERE city != '' ORDER BY city ASC");
                                while($c = mysqli_fetch_assoc($cityRes)) {
                                    $cityName = htmlspecialchars($c['city']);
                                    echo "<option value='{$cityName}'>{$cityName}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Keyword Search -->
                        <div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
                            <label style="display: block; font-size: 12px; margin-bottom: 3px;">Keyword Search:</label>
                            <input type="text" class="form-control input-sm" id="keyword" name="keyword" placeholder="Company, Contact, Phone..." style="width: 170px;" autocomplete="off"/>
                        </div>

                        <!-- Action Buttons -->
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label style="display: block; font-size: 12px; margin-bottom: 3px;">&nbsp;</label>
                            <button type="button" id="btnsearch" class="btn btn-sm btn-success">
                                <i class="fa fa-search"></i> Search
                            </button>
                            <button type="button" id="btnreset" class="btn btn-sm btn-default" style="margin-left: 5px;">
                                <i class="fa fa-refresh"></i> Reset
                            </button>
                        </div>

                    </form>
                </div>

                <!-- Table Box -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="pull-left">
                            <h3 class="box-title" style="font-weight: 600; margin-right: 15px;">
                                <i class="fa fa-list"></i> Administrative Visit Records
                            </h3>
                        </div>
                        
                        <!-- KPI Summary Badges -->
                        <div class="pull-left kpi-badges">
                            <span id="stat_total" class="badge bg-blue">Total Visits: 0</span>
                            <span id="stat_cat1" class="badge bg-green">New Search: 0</span>
                            <span id="stat_cat2" class="badge bg-aqua">KYC: 0</span>
                            <span id="stat_cat3" class="badge bg-yellow">Misc: 0</span>
                        </div>
                        
                        <div class="pull-right">
                            <button class="btn btn-xs btn-default" onclick="loadVisitsData();">
                                <i class="fa fa-refresh"></i> Reload Data
                            </button>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="box-body table-responsive">
                        <div class="progress progress-striped active" id="progressLoading" style="display: none; margin-bottom: 15px;">
                            <div class="progress-bar progress-bar-success" style="width: 100%">Loading visits...</div>
                        </div>

                        <div class="table-wrap">
                            <table id="visitsTable" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">ID</th>
                                        <th>Date & Time</th>
                                        <th>Employee</th>
                                        <th>Category</th>
                                        <th>Company / Firm</th>
                                        <th>Contact Person</th>
                                        <th>Phone</th>
                                        <th>City & PIN</th>
                                        <th>Location</th>
                                        <th>Search Details / Reason</th>
                                        <th style="width: 60px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Dynamic rows loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

<!-- Detailed Visit Modal -->
<div id="detailModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalTitle">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalTitle" style="color: #fff; font-weight: 600;">
                    <i class="fa fa-building-o"></i> Visit Details
                </h4>
            </div>
            <div class="modal-body" id="modalContent">
                <!-- Populated by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include("footer.php"); ?>
<?php include("jsscript.php"); ?>

<!-- DataTables & Export Extensions -->
<script src="assets/node_modules/datatables/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>

<script>
var visitsCache = [];

$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2();

    // Initialize Date Range Picker
    $('#reservation').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear',
            format: 'YYYY-MM-DD'
        }
    });

    $('#reservation').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
    });

    $('#reservation').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    // Load initial data
    loadVisitsData();

    // Search button
    $('#btnsearch').on('click', function() {
        loadVisitsData();
    });

    // Reset button
    $('#btnreset').on('click', function() {
        $('#reservation').val('');
        $('#employee').val('').trigger('change');
        $('#reason_category').val('').trigger('change');
        $('#city').val('').trigger('change');
        $('#keyword').val('');
        loadVisitsData();
    });

    // Handle View Details button click
    $(document).on('click', '.view-detail-btn', function() {
        var id = $(this).data('id');
        var visit = visitsCache.find(function(v) { return v.id == id; });
        if (visit) {
            renderVisitModal(visit);
        }
    });
});

/**
 * Fetch and Render Data into DataTable
 */
function loadVisitsData() {
    var progress = $('#progressLoading');
    progress.fadeIn('fast');

    var params = {
        reservation: $('#reservation').val(),
        user_id: $('#employee').val(),
        reason_category: $('#reason_category').val(),
        city: $('#city').val(),
        keyword: $('#keyword').val()
    };

    $.ajax({
        url: 'api/administrative-visit.php',
        type: 'GET',
        data: params,
        dataType: 'json',
        success: function(response) {
            progress.fadeOut('fast');
            if (response && response.status === 1) {
                visitsCache = response.data || [];
                
                // Update KPI summary badges
                var summary = response.summary || {};
                $('#stat_total').text('Total Visits: ' + (summary.total_visits || 0));
                $('#stat_cat1').text('New Search: ' + (summary.new_distributor_search || 0));
                $('#stat_cat2').text('KYC: ' + (summary.new_distributor_kyc || 0));
                $('#stat_cat3').text('Misc: ' + (summary.miscellaneous_visit || 0));

                initDataTable(visitsCache);
            } else {
                alert(response.message || 'Failed to fetch data');
                initDataTable([]);
            }
        },
        error: function(err) {
            progress.fadeOut('fast');
            console.error('Error fetching visits:', err);
            alert('Error loading visit records from server.');
        }
    });
}

/**
 * Initialize DataTable with loaded records
 */
function initDataTable(data) {
    $('#visitsTable').DataTable({
        data: data,
        destroy: true,
        paging: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        dom: 'Bfrtip',
        order: [[0, 'desc']],
        buttons: [
            'copy',
            { extend: 'csv', title: 'Administrative_Visits_Report' },
            { extend: 'excel', title: 'Administrative_Visits_Report' },
            { extend: 'pdf', title: 'Administrative_Visits_Report' },
            { extend: 'print', title: 'Administrative Visits Report' }
        ],
        columns: [
            { 
                data: 'id',
                render: function(val) {
                    return '<strong>' + val + '</strong>';
                }
            },
            { 
                data: 'formatted_in_time',
                render: function(val, type, row) {
                    return '<span>' + val + '</span>';
                }
            },
            { 
                data: 'employee_name',
                render: function(val, type, row) {
                    var empid = row.employee_empid ? ' <small class="text-muted">(' + row.employee_empid + ')</small>' : '';
                    return '<strong>' + val + '</strong>' + empid;
                }
            },
            { 
                data: 'reason_category',
                render: function(val, type, row) {
                    if (val === 1) {
                        return '<span class="label label-success" style="font-size: 11px;">1 - New Search</span>';
                    } else if (val === 2) {
                        return '<span class="label label-info" style="font-size: 11px;">2 - New KYC</span>';
                    } else if (val === 3) {
                        return '<span class="label label-warning" style="font-size: 11px;">3 - Miscellaneous</span>';
                    }
                    return '<span class="label label-default">Unknown</span>';
                }
            },
            { 
                data: 'company_name',
                render: function(val) {
                    return '<strong>' + val + '</strong>';
                }
            },
            { 
                data: 'contact_person',
                render: function(val) {
                    return val || '-';
                }
            },
            { 
                data: 'cell_no',
                render: function(val) {
                    return val ? '<a href="tel:' + val + '"><i class="fa fa-phone"></i> ' + val + '</a>' : '-';
                }
            },
            { 
                data: 'city',
                render: function(val, type, row) {
                    var pin = row.pin_code ? ' - ' + row.pin_code : '';
                    return val + pin;
                }
            },
            { 
                data: 'gps_latitude',
                render: function(val, type, row) {
                    var lat = row.gps_latitude;
                    var lng = row.gps_longitude;
                    if (lat && lng) {
                        return '<a href="https://maps.google.com/?q=' + lat + ',' + lng + '" target="_blank" class="btn btn-xs btn-default text-red" title="View on Google Maps"><i class="fa fa-map-marker text-danger"></i> Map</a>';
                    }
                    return '-';
                }
            },
            { 
                data: 'reason_category',
                render: function(val, type, row) {
                    if (val === 1 && row.new_distributor_search_details) {
                        var sd = row.new_distributor_search_details;
                        var pills = '';
                        pills += '<span class="store-pill">GT: ' + sd.gt_stores_covered + '</span>';
                        pills += '<span class="store-pill">MT: ' + sd.mt_stores_covered + '</span>';
                        pills += '<span class="store-pill">WS: ' + sd.wholesalers_covered + '</span>';
                        pills += '<span class="store-pill">HoReCa: ' + sd.horeca_covered + '</span>';
                        var area = sd.areas_covered ? '<div style="font-size: 11px; color: #555; margin-top: 3px;"><em>' + sd.areas_covered.substring(0, 45) + (sd.areas_covered.length > 45 ? '...' : '') + '</em></div>' : '';
                        return pills + area;
                    } else if (val === 3 && row.visit_reason) {
                        return '<span style="font-size: 11px; color: #555;">' + row.visit_reason.substring(0, 60) + (row.visit_reason.length > 60 ? '...' : '') + '</span>';
                    } else if (val === 2) {
                        return '<span class="text-muted" style="font-size: 11px;">Distributor KYC Record</span>';
                    }
                    return '-';
                }
            },
            { 
                data: 'id',
                orderable: false,
                render: function(val) {
                    return '<button type="button" class="btn btn-xs btn-primary view-detail-btn" data-id="' + val + '"><i class="fa fa-eye"></i> View</button>';
                }
            }
        ]
    });
}

/**
 * Render Detailed Modal for Single Visit Record
 */
function renderVisitModal(v) {
    $('#modalTitle').html('<i class="fa fa-building-o"></i> Visit  - ' + v.company_name);
    
    var categoryBadge = '';
    if (v.reason_category === 1) {
        categoryBadge = '<span class="label label-success" style="font-size: 12px;">1 - New Distributor Search</span>';
    } else if (v.reason_category === 2) {
        categoryBadge = '<span class="label label-info" style="font-size: 12px;">2 - New Distributor KYC</span>';
    } else if (v.reason_category === 3) {
        categoryBadge = '<span class="label label-warning" style="font-size: 12px;">3 - Miscellaneous Visit</span>';
    }

    var html = '';
    html += '<div class="row">';
    
    // Column 1: Basic & Employee Info
    html += '<div class="col-md-6">';
    html += '<h5 style="border-bottom: 2px solid #3c8dbc; padding-bottom: 5px; font-weight: 600;"><i class="fa fa-user"></i> General Information</h5>';
    html += '<table class="table table-condensed table-bordered">';
    html += '<tr><td class="modal-label">Employee:</td><td><strong>' + v.employee_name + '</strong> ' + (v.employee_empid ? '(' + v.employee_empid + ')' : '') + '</td></tr>';
    html += '<tr><td class="modal-label">Category:</td><td>' + categoryBadge + '</td></tr>';
    html += '<tr><td class="modal-label">In-Time:</td><td>' + (v.formatted_in_time || v.in_time) + '</td></tr>';
    html += '<tr><td class="modal-label">Out-Time:</td><td>' + (v.formatted_out_time || '-') + '</td></tr>';
    html += '<tr><td class="modal-label">Visit Type:</td><td>' + (v.visit_type || 'ADMINISTRATIVE') + '</td></tr>';
    html += '</table>';
    html += '</div>';

    // Column 2: Company & Contact Info
    html += '<div class="col-md-6">';
    html += '<h5 style="border-bottom: 2px solid #3c8dbc; padding-bottom: 5px; font-weight: 600;"><i class="fa fa-address-card-o"></i> Company & Contact</h5>';
    html += '<table class="table table-condensed table-bordered">';
    html += '<tr><td class="modal-label" style="width: 40%;">Company Name:</td><td><strong>' + v.company_name + '</strong></td></tr>';
    html += '<tr><td class="modal-label">Contact Person:</td><td>' + v.contact_person + '</td></tr>';
    html += '<tr><td class="modal-label">Mobile / Phone:</td><td><a href="tel:' + v.cell_no + '">' + v.cell_no + '</a></td></tr>';
    html += '<tr><td class="modal-label">City:</td><td>' + v.city + '</td></tr>';
    html += '<tr><td class="modal-label">PIN Code:</td><td>' + v.pin_code + '</td></tr>';
    html += '<tr><td class="modal-label">Address:</td><td>' + v.address + '</td></tr>';
    html += '</table>';
    html += '</div>';
    
    html += '</div>'; // end row

    // Location Info Section
    html += '<div class="row" style="margin-top: 10px;">';
    html += '<div class="col-md-12">';
    html += '<h5 style="border-bottom: 2px solid #00a65a; padding-bottom: 5px; font-weight: 600;"><i class="fa fa-map-marker text-green"></i> GPS Location</h5>';
    html += '<div class="well well-sm" style="background: #fdfdfd; margin-bottom: 10px;">';
    html += '<p><strong>GPS Address:</strong> ' + (v.gps_address_autofilled || v.address) + '</p>';
    html += '<p><strong>Coordinates:</strong> Latitude: <code>' + v.gps_latitude + '</code> | Longitude: <code>' + v.gps_longitude + '</code> ';
    html += '<a href="https://maps.google.com/?q=' + v.gps_latitude + ',' + v.gps_longitude + '" target="_blank" class="btn btn-xs btn-danger" style="margin-left: 10px;"><i class="fa fa-map-marker"></i> Open in Google Maps</a></p>';
    html += '</div>';
    html += '</div>';
    html += '</div>';

    // Child Data Section
    if (v.reason_category === 1 && v.new_distributor_search_details) {
        var s = v.new_distributor_search_details;
        html += '<div class="row" style="margin-top: 10px;">';
        html += '<div class="col-md-12">';
        html += '<h5 style="border-bottom: 2px solid #3c8dbc; padding-bottom: 5px; font-weight: 600;"><i class="fa fa-search-plus"></i> New Distributor Search Details</h5>';
        
        // Store counter boxes
        html += '<div class="row" style="margin-bottom: 10px;">';
        html += '<div class="col-xs-3"><div class="info-box bg-aqua"><span class="info-box-icon"><i class="fa fa-shopping-basket"></i></span><div class="info-box-content"><span class="info-box-text">GT Stores</span><span class="info-box-number">' + s.gt_stores_covered + '</span></div></div></div>';
        html += '<div class="col-xs-3"><div class="info-box bg-green"><span class="info-box-icon"><i class="fa fa-building"></i></span><div class="info-box-content"><span class="info-box-text">MT Stores</span><span class="info-box-number">' + s.mt_stores_covered + '</span></div></div></div>';
        html += '<div class="col-xs-3"><div class="info-box bg-yellow"><span class="info-box-icon"><i class="fa fa-cubes"></i></span><div class="info-box-content"><span class="info-box-text">Wholesalers</span><span class="info-box-number">' + s.wholesalers_covered + '</span></div></div></div>';
        html += '<div class="col-xs-3"><div class="info-box bg-red"><span class="info-box-icon"><i class="fa fa-cutlery"></i></span><div class="info-box-content"><span class="info-box-text">HoReCa</span><span class="info-box-number">' + s.horeca_covered + '</span></div></div></div>';
        html += '</div>';

        html += '<div class="panel panel-default">';
        html += '<div class="panel-heading"><strong>Areas Covered</strong></div>';
        html += '<div class="panel-body" style="background: #fbfbfb;">' + (s.areas_covered ? s.areas_covered.replace(/\n/g, '<br/>') : '<em>None specified</em>') + '</div>';
        html += '</div>';

        html += '</div>';
        html += '</div>';
    } else if (v.reason_category === 3) {
        html += '<div class="row" style="margin-top: 10px;">';
        html += '<div class="col-md-12">';
        html += '<h5 style="border-bottom: 2px solid #f39c12; padding-bottom: 5px; font-weight: 600;"><i class="fa fa-commenting-o text-yellow"></i> Miscellaneous Visit Reason</h5>';
        html += '<div class="panel panel-default">';
        html += '<div class="panel-body" style="background: #fffdf5; font-size: 13px; line-height: 1.6;">' + (v.visit_reason ? v.visit_reason.replace(/\n/g, '<br/>') : '<em>No reason provided</em>') + '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
    }

    $('#modalContent').html(html);
    $('#detailModal').modal('show');
}
</script>
