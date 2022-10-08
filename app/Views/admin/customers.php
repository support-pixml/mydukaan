<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>D-Store | Dashboard</title>

    <?= $this->include('admin/header') ?>
    <style type="text/css">
  .txt_domain{
    display: block;
    width: 100%;
    padding: 0;
    font-size: .9rem;
    line-height: 1.25;
    color: #464a4c;
    background-color: #fff;
    background-image: none;
    background-clip: padding-box;
    border: 2px solid #dde2ec;
    border-radius: .25rem;
    transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s, -webkit-box-shadow ease-in-out 0.15s;
  }
  .txt_domain input{
    background-color: transparent;
    border: none;
    display: inline-block;
    width: auto;
    padding-left: 0px;
    padding-right: 0px;
    color: green;
  }

  .txt_domain input::placeholder, .txt_domain input:focus{
    color: green;
  }

  .txt_domain span{
    font-size: .9rem;
    line-height: 1.25;
    padding: .5rem .7rem;
    font-weight: 300;
    color: #8d9293;
  }
  </style>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Customers</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">DataTables</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-12">

                    <div class="card">
                        <div class="card-header">
                            <button type="button" class="btn btn-primary add-customer float-right" data-toggle="modal" data-target="#AddCustomer">Add Customer</button>
                        </div>
                        <div class="modal fade" id="AddCustomer">
                            <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Add Customer</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="addCustomerForm">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="customer_name">Customer Name</label>
                                                    <input type="text" class="form-control" id="customer_name" name="customer_name"
                                                        autofocus="true" placeholder="Enter Customer Name">
                                                </div>										
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="company_name">Company Name</label>
                                                    <input type="text" class="form-control" id="company_name" name="company_name"
                                                        placeholder="Enter Company Name">
                                                    <input type="hidden" id="customer_long_id" name="customer_long_id" />
                                                    <input type="hidden" id="customer_user_long_id" name="customer_user_long_id" />
                                                </div>										
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="customer_phone">Customer Phone</label>
                                                    <label><span style="color: #aaaaaa; font-size: 12px;"> can be used as username</span></label>
                                                    <input type="text" class="form-control" id="customer_phone" name="customer_phone"
                                                        placeholder="Enter Customer Phone">
                                                </div>										
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="password">Password</label>
                                                    <label><span style="color: #aaaaaa; font-size: 12px;"> password length must be 8 characters.</span></label>
                                                    <input type="text" class="form-control" id="password" name="password"
                                                        placeholder="Enter Password">
                                                </div>										
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="laneName">URL Link</label>
                                                <label><span style="color: #aaaaaa; font-size: 12px;"> Only Characters, Numbers & Dash (-) is allowed</span></label>
                                                
                                                <div class="txt_domain">
                                                    <span style="padding-right: 0;">https://</span>
                                                    <input class="form-control" autocomplete="off" data-error="Enter your user name" placeholder="url-name" required="required" type="text" name="url_title" id="url_title" value="" maxlength="24" spellcheck="false" autocapitalize="off" autocorrect="off"/>
                                                    <span style="padding-left: 0;">.d-store.co.in</span>
                                                </div>									
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="laneName">Logo</label>
                                                    <div id="logo">
                                                        <input type="file" name="logo" class="dropify" data-height="300" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="bank_name">Bank Name</label>
                                                    <input type="text" class="form-control" id="bank_name" name="bank_name"
                                                        placeholder="Enter Bank Name">
                                                </div>										
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="bank_ac_no">Bank Account No.</label>
                                                    <input type="text" class="form-control" id="bank_ac_no" name="bank_ac_no"
                                                        placeholder="Enter Bank Account No.">
                                                </div>										
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="bank_ifsc">Bank IFSC Code</label>
                                                    <input type="text" class="form-control" id="bank_ifsc_code" name="bank_ifsc_code"
                                                        placeholder="Enter Bank IFSC Code">
                                                </div>										
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="address">Company Address</label>
                                                    <textarea class="form-control" id="address" name="address"
                                                        placeholder="Enter Company Address"></textarea>
                                                </div>										
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="gst_no">GST No.</label>
                                                    <input type="text" class="form-control" id="gst_no" name="gst_no"
                                                        placeholder="Enter GST No.">
                                                </div>										
                                            </div>
                                        </div>
                                        <div class="dup row pt-3 border-top">
                                            <div class="col-md-6">
                                                <label>Duplicate Products </label>
                                                <div class="form-group">
                                                    <label>Select Company</label>
                                                    <select class="form-control select2bs4" name="company" id="company" style="width: 100%;">
                                                        <option value=""></option>
                                                        <?php
                                                        foreach($customers as $customer) { ?>
                                                        <option value="<?php echo $customer->long_id; ?>"><?php echo $customer->company_name; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>									
                                            </div>                                            
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-primary">Save changes</button>
                                            </div>
                                        </div>
									</form>
                                </div>
                            </div>
                            <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                        
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="customers" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Company</th>
                                        <th>Link</th>
                                        <th>Customer Name</th>
                                        <th>Expiry Date</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <?= $this->include('admin/footer') ?>
    <script>
        $(function () {
            $("#customers").DataTable({"ajax": {url: "/get_customers"},
				"language": {"zeroRecords": "No customers found."},
                'ordering': true
			});
        });   
        
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        });

        $('.modal').on('shown.bs.modal', function () {
			$(this).find('[autofocus]').focus();
		});
        
        $('.add-customer').click(function() {
            $("form").trigger("reset");
            html2 = '<input type="file" name="logo" class="dropify" data-height="300"/>';
            $('#logo').html(html2);
            $('.dropify').dropify();   
            $('#customer_long_id').val('');
            $('#url_title').prop('disabled', false);
            $('#customer_phone').val('');
            $('#customer_user_long_id').val('');
            $('#customer_phone').prop('readonly', false);
            $('.dup').hide();
        });

        $('body').on('change', 'input[name=url_title]', check_availability);

        function check_availability()
        {
            event.preventDefault();
            var url_title = $('input[name=url_title]').val();

            if($.trim(url_title) == "" || $.trim(url_title) == "www" || $.trim(url_title).indexOf(' ') >= 0)
            {
                toastr.error('Please enter valid url title.');
                $('input[name="url_title"]').val('');
                return false;
            }

            $.ajax({
                url:'/check_url_title',
                type: 'POST',
                data: {url_title : url_title},
                // datatype:'json',
                success: function(data)
                {
                    toastr.success('https://'+$.trim(url_title)+'.d-store.co.in is available');
                },
                error: function(error)
                {
                    toastr.error(error.responseJSON.messages.error);
                    $('input[name="url_title"]').val('');
                }
            });
        }

        $('#addCustomerForm').on("submit", function(e){
            e.preventDefault();
            if($('#company').val())
            {
                $.confirm({
                    title: 'Confirm!',
                    icon: 'fa fa-exclamation-triangle',
                    type: 'orange',
                    content: 'Do you want to duplicate data from selected company?',
                    buttons: {
                        confirm: function () {
                            save_customer();			
                        },
                        cancel: function () {
                        }
                    }
                });
            }
            else
            {
                save_customer();
            }           
        });

        function save_customer()
        {
            $.ajax({
                type: 'POST',
                url: "/save_customer",
                data: new FormData(addCustomerForm),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function(response)
                {
                    $('#AddCustomer').modal('toggle');
                    if(response.status == 400)
                    {
                        toastr.error(response.messages.error);
                    }
                    else
                    {
                        $("form").trigger("reset");
                        toastr.success(response.message);
                        $("#customers").DataTable().ajax.reload();
                    }
                }
            }); 
        }

        function remove_customer(customer_long_id) {
            $.confirm({
                title: 'Confirm!',
                icon: 'fa fa-exclamation-triangle',
                type: 'red',
                content: 'Do you want to delete this customer?',
                buttons: {
                    confirm: function () {
                        $.ajax({
                            url: '/delete_customer',
                            type: 'post',
                            data: {customer_long_id : customer_long_id},
                            success : function(response)
                            {
                                toastr.success('Customer Removed.')
                                $("#customers").DataTable().ajax.reload();
                            },
                            error : function(response)
                            {
                                toastr.error('Something Wrong!')
                            }
                        });						
                    },
                    cancel: function () {
                    }
                }
            });
        }

        function fetch_customer_data(customer_long_id)
        {
            $('.dup').show();
            $.ajax({
                type: 'post',
                url: "/get_customer_data",
                data: {customer_long_id : customer_long_id},
                success : function(response)
                {
                    if(response.status == 0)
                    {
                        toastr.error(response.message)
                    }
                    $("form").trigger("reset");
                    var customer_data = response;
                    $('.modal_title').html('Edit Customer');
                    $('#customer_long_id').val(customer_data.long_id);
                    $('#customer_name').val(customer_data.customer_name); 
                    $('#company_name').val(customer_data.company_name); 
                    $('#url_title').val(customer_data.url_title); 
                    $('#url_title').prop('disabled', true);
                    $('#customer_user_long_id').val(customer_data.user_data.long_id);
                    $('#customer_phone').val(customer_data.user_data.phone);
                    $('#customer_phone').prop('readonly', true);
                    $('#bank_name').val(customer_data.bank_name);
                    $('#bank_ac_no').val(customer_data.bank_ac_no);
                    $('#bank_ifsc_code').val(customer_data.bank_ifsc_code);
                    $('#gst_no').val(customer_data.gst_no);
                    $('#address').val(customer_data.address);
                    if(customer_data.logo == null)
                    {
                        html = '<input type="file" name="logo" class="dropify" data-height="300" />';
                    }
                    else
                    {
                        var nameImage = "/uploads/customers/"+customer_data.logo;
                        html = '<input type="file" name="logo" class="dropify" data-height="300" data-default-file="'+nameImage+'"/>';
                    }
                    $('#logo').html(html);
                    $('.dropify').dropify();   
                    var drEvent = $('.dropify').dropify();
        
                    drEvent.on('dropify.beforeClear', function(event, element){
                        if(confirm("Do you really want to delete this logo?"))
                        {
                            $.ajax({
                                url: '/remove_customer_logo',
                                type: 'post',
                                data: {customer_long_id : customer_data.long_id},
                                success : function(response)
                                {
                                    toastr.success('Logo Removed.')
                                },
                                error : function(response)
                                {
                                    toastr.error('Something Wrong!')
                                }
                            });	
                        }
                        else
                            return false;
                    });
                }
            });
        }

    </script>
    </body>

</html>