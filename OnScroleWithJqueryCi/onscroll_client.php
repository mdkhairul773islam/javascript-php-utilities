<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/css/bootstrap-select.min.css" />
<style>
    @media print{
        aside, .panel-heading, .panel-footer, nav, .none{display: none !important;}
        .panel{border: 1px solid transparent;left: 0px;position: absolute;top: 0px;width: 100%;}
        .hide{display: block !important;}
        table tr th,table tr td{font-size: 12px;}
        .print_banner_logo {width: 19%;float: left;}
        .print_banner_logo img {margin-top: 10px;}
		.print_banner_text {width: 80%; float: right;text-align: center;}
		.print_banner_text h2 {margin:0;line-height: 38px;text-transform: uppercase !important;}
		.print_banner_text p {margin-bottom: 5px !important;}
		.print_banner_text p:last-child {padding-bottom: 0 !important;margin-bottom: 0 !important;}
    }
    .action-btn a{
        margin-right: 0;
        margin: 3px 0;
    }
    .Receivable{color: green; font-weight: bold;};
    .Payable{color: red; font-weight: bold;};
</style>
<div class="container-fluid">
    <div class="row">
        <?php echo $this->session->flashdata('confirmation'); ?>

    	<div class="panel panel-default" id="data">
            <div class="panel-heading">
                <div class="panal-header-title">
                    <h1 class="pull-left">View All Customers</h1>
                    <a class="btn btn-primery pull-right" style="font-size: 14px; margin-top: 0;" onclick="window.print()"><i class="fa fa-print"></i> Print</a>
				</div>
            </div>

            <div class="panel-body">
                <!-- Print banner Start Here -->
                    <?php $this->load->view('print', $this->data); ?>
                <!-- Print banner End Here -->

                <!--<h4 class="text-center hide" style="margin-top: 0px;">All Customers</h4>-->
                <div class="col-md-12 text-center hide">
                    <h3>All Customer</h3>
                </div>

                <?php echo form_open('', ['id' => 'searchForm']); ?>
                <div class="row none">
                    <?php if(checkAuth('super')) { ?>
                     <div class="col-md-3">
                        <div class="form-group">
                            <select  name="godown_code" id="godownCode" onchange="getGodownWiseData()" class="form-control">
                                <option value="" selected disabled>-- Select Showroom --</option>
                                <option value="all">All Showroom</option>
                                <?php if(!empty($allGodowns)){ foreach($allGodowns as $row){ ?>
                                <option value="<?php echo $row->code; ?>">
                                    <?php echo filter($row->name)." ( ".$row->address." ) "; ?>
                                </option>
                                <?php } } ?>
                            </select>
                        </div>
                    </div>
                    <?php }else{ ?>
                         <input type="hidden" name="godown_code" id="godownCode" value ='<?php echo $this->data['branch']; ?>' >
                    <?php } ?>
                    
                    
                    <div class="col-md-2">
                        <div class="form-group">
                            <select name="customer_type" id="customerType" class="selectpicker form-control">
                                <option value="" selected>-- Select Type ---</option>
                                <option value="dealer" >Dealer</option>
                                <option value="hire">Hire</option>
                                <option value="weekly">Weekly</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <select name="code" id="clientList"  class="form-control" data-show-subtext="true" data-live-search="true">
                                    <option value="" selected>Select Client</option>
                            </select>
                        </div>
                    </div>
                             
                    <div class="col-md-2">
                        <div class="form-group">
                            <select name="zone" id="zoneList"  class="form-control" data-show-subtext="true" data-live-search="true">
                                    <option value="" selected>Select Zone</option>
                            </select>
                        </div>
                    </div>
                        
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="btn-group">
                                <input type="submit" name="show" value="Search" class="btn btn-primary" style="margin-right: 15px;">
                                <a href="<?php echo current_url(); ?>" class="btn btn-warning">Refresh</a>
                          </div>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?> 
                <hr class="none" style="margin-top: 0px;">
                <p class="none"> <span style="color: green;font-weight: bold;">Green = Receivable</span>&nbsp;<span style="color: red;font-weight: bold;">Red = Payable</span></p>
                
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataList">
                        <thead>
                            <tr>
                            <th width="50">SL</th>
                            <th width="75">C.ID</th>
                            <th width="60">Photo</th>
                            <th>Customer Name</th>
                            <th>Dealer Type</th>
                            <th>Customer Type</th>
                            <th>Zone</th>
                            <th>Address</th>
                            <th width="120">Mobile</th>
                            <th width="115">Balance</th>
                            <th>Showroom</th>
                            <th class="none" style="width: 160px;">Action</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot></tfoot>
                    </table>
                </div>
                
                <div class="text-center loadingData">
                    <span class="btn btn-default" onClick="clientData()">Loading data....</span>
                </div>
            </div>
            <div class="panel-footer">&nbsp;</div>
        </div>
    </div>
</div>
<!-- Select Option 2 Script -->


<script>
   
   /* load stock data */
   var _limit  = 200;
   var _offset = 0;
   var counter = 1;
   var balance = 0;
   
   
   $('.loadingData').hide();
   
   function clientData(){
       
       $('.loadingData').show();
       
       // search data
       var _godownCode    = document.getElementById('godownCode').value;
       var _customer_type = document.getElementById('customerType').value;
       var _code          = document.getElementById('clientList').value;
       var _zone          = document.getElementById('zoneList').value;
       
       $.post("<?= site_url('client/client/allClientList') ?>", {
           godown_code  : _godownCode,
           customer_type: _customer_type,
           code         : _code,
           zone         : _zone,
           limit        : _limit,
           offset       : _offset,
       }).success(function(response){
           
           var data = JSON.parse(response);
           console.log('data', data);
            if (data.length > 0) {

                $('#dataList tfoot').empty();

                if(data.length < _limit){
                    $('.loadingData').hide();
                }else{
                  $('.loadingData').show();  
                }
                
                $('.loadingData span').text('Click and load more data.');
                
                var itemData = data.map(function (row) {
                    if(row.balance > 0){  
                    balance += parseFloat(row.main_balance);
                    
                    return (`
                        <tr>
                            <td>${(counter++)}</td>
                            <td>${row.code}</td>
                            <td>${row.avatar}</td>
                            <td>${row.name}</td>
                            <td>${row.dealer_type}</td>
                            <td>${row.customer_type}</td>
                            <td>${row.zone}</td>
                            <td>${row.address}</td>
                            <td>${row.mobile}</td>
                            <td class="${row.status} text-right">${row.balance.toFixed(2)}</td>
                            <td>${row.godown_name}</td>
                            <td class="none">${row.action}</td>
                        </tr>
                    `);
                }});

                _offset += _limit;

                $('#dataList tbody').append(itemData);

                // load footer data
                var tfooter = `
                    <tr>
                        <th colspan="9" class="text-right">Total</th>
                        <th class="text-right">${balance} Tk</th>
                        <th></th>
                        <th></th>
                    </tr>
                `;
                $('#dataList tfoot').append(tfooter);
            }else{
                $('.loadingData').hide();
            }
       });
   }
   
   
    // form submit and search data
    $( "#searchForm" ).submit(function( event ) {
      event.preventDefault();
      $('#dataList tbody').empty();
      $('#dataList tfoot').empty();
      _offset = 0;
      balance = 0;
      counter = 1;
      clientData();
    });
    
    
    // text filter
    function strFilter(string) {
        if (string) {
            var text = string.replace(/_/g, " ");
            return text.replace(/^\w/, c => c.toUpperCase());
        }
        return '';
    }
   
   
    // get all godwon wise data
    function getGodownWiseData(){
        var _godownCode = document.getElementById('godownCode').value;
        clientList(_godownCode);
        zoneList(_godownCode);
        
        $('#dataList tbody').empty();
        $('#dataList tfoot').empty();
        _offset = 0;
        balance = 0;
        counter = 1;
        clientData();
    }
    
    getGodownWiseData(); 
    
    // get all party list
    function clientList(godownCode){
       
       if(godownCode != ''){
            $('#clientList').html('');
            
            $.post("<?= site_url('client/client/clientList') ?>", {godown_code: godownCode}).success(function(data){
                $('#clientList').html(data);
                $('#clientList').selectpicker('refresh');
            });
       }
    }  
   
   // get all party list
   function zoneList(godownCode){
       
       if(godownCode != ''){
           $('#zoneList').html();
           
            $.post("<?= site_url('client/client/zoneList') ?>", {godown_code: godownCode}).success(function(data){
                $('#zoneList').html(data);
                $('#zoneList').selectpicker('refresh');
            });
       }
   }
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/js/bootstrap-select.min.js"></script>