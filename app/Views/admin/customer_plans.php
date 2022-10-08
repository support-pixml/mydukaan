<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Customer Plans | D-Store</title>

    <?= $this->include('admin/header') ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Customer Plans</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Plans</li>
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
                            <button type="button" class="btn btn-primary add-customer float-right" data-toggle="modal" data-target="#AddCustomerPlan">Add Plan</button>
                        </div>
                        <div class="modal fade" id="AddCustomerPlan">
                            <div class="modal-dialog modal-md">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Add Customer Plan</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="addCustomerPlanForm">
                                        <div class="row">
                                            <div class="col-md-12 plan_customer_name">                                                
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="total_products">Products Upload Limit</label>
                                                    <input type="text" class="form-control" id="total_products" name="total_products"
                                                        autofocus="true" placeholder="Enter Total Products">
                                                        <input type="hidden" id="plan_id" name="plan_id"/>
                                                </div>										
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="total_products">Amount</label>
                                                    <input type="text" class="form-control" id="amount" name="amount"
                                                        placeholder="Enter Amount">
                                                </div>										
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="expiry_date">Expiry Date</label>
                                                    <input type="text" class="form-control datetimepicker-input" id="datetimepicker4" data-target="#datetimepicker4"
                                                         name="expiry_date" data-toggle="datetimepicker"
                                                        placeholder="Enter Expiry Date">
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
                            <table id="customer_plans" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Products Upload Limit</th>
                                        <th>Expiry Date</th>
                                        <th>Amount</th>
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
        <?php $uri = service('uri'); ?>
        $(function () {
            $("#customer_plans").DataTable({"ajax": {url: "/get_customer_plan/<?= $uri->getSegment(2); ?>"},
				"language": {"zeroRecords": "No plan found."},
                'ordering': true
			});
        });

        $('.modal').on('shown.bs.modal', function () {
			$(this).find('[autofocus]').focus();
            $('.modal-title').html('Add Plan');
		});

        $('.add-customer').click(function() {
            $("form").trigger("reset");
        });       

        $('#addCustomerPlanForm').on("submit", function(e){
            e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: "/save_customer_plan/<?= $uri->getSegment(2); ?>",
                    data: new FormData(addCustomerPlanForm),
                    dataType: 'json',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(response)
                    {
                        $('#AddCustomerPlan').modal('toggle');
                        if(response.status == 400)
                        {
                            toastr.error(response.messages.error);
                        }
                        else
                        {
                            $("form").trigger("reset");
                            toastr.success(response.message);
                            $("#customer_plans").DataTable().ajax.reload();
                        }
                    }
            }); 
        });   

        function delete_plan(plan_id) {
		$.confirm({
			title: 'Confirm!',
			icon: 'fa fa-exclamation-triangle',
			type: 'red',
			content: 'Do you want to delete this plan?',
			buttons: {
				confirm: function () {
					$.ajax({
						url: '/delete_customer_plan',
						type: 'post',
						data: {plan_id : plan_id},
						success : function(response)
						{
							toastr.success('Plan Removed.')
                            $("#customer_plans").DataTable().ajax.reload();
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

    function fetch_plan_data(plan_id)
    {
        $.ajax({
            type: 'post',
            url: "/get_plan_data",
            data: {plan_id : plan_id},
            success : function(response)
            {
                if(response.status == 0)
                {
                    toastr.error(response.message)
                }
                $("form").trigger("reset");
                var plan_data = response;
                $('.modal-title').html('Edit Plan');
                $('#plan_id').val(plan_data.id);
                $('#total_products').val(plan_data.total_products); 
                $('#datetimepicker4').val(plan_data.expiry_date); 
                $('#amount').val(plan_data.amount); 
            }
        });
    }

    </script>
    </body>

</html>