<?php $this->load->view('header');?>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/dropzone.css" />
<body>
    <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <!-- preloader area start -->
    <div id="preloader">
        <div class="loader"></div>
    </div>
    <!-- preloader area end -->
    <!-- page container area start -->
    <div class="page-container">
        <?php $this->load->view('admin_menu');?>
            <!-- page title area end -->
            <div class="main-content-inner">
                <div class="row">
                    <!-- data table start -->
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <div class="row mb-2">
                                        <div class="col-6">
                                            <h4 class="header-title">Products</h4>
                                        </div>
                                        <div class="col-6">
                                            <a href="#" class="btn btn-info pull-right btn-xs" style="margin-left: 3px;" data-toggle="modal" data-target="#modal_form2"><span class="ti-upload"></span> UPLOAD</a>
                                            <a href="<?php echo base_url();?>admin/download_products" class="btn btn-warning pull-right btn-xs" style="margin-left: 3px;"><span class="ti-download"></span> DOWNLOAD</a>
                                            <a href="#" class="btn btn-success pull-right btn-xs" style="margin-left: 3px;" data-toggle="modal" data-target="#modal_form"><span class="ti-plus"></span> ADD PRODUCT</a>
                                        </div>
                                    </div>
                                    <table id="dataTable" class="text-center">
                                        <thead class="bg-light text-capitalize">
                                            <tr>
                                                <th>No.</th>
                                                <th>Name</th>
                                                <th>Category</th>
                                                <th>Tax</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                            </div>
                        </div>
                    </div>
                    <!-- data table end -->
                </div>
                
            </div>
        </div>
        <div class="modal fade" id="modal_form" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Product</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                    </div>
                    <div class="modal-body">
                        <form id="myForm" class="needs-validation" novalidate="">
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label for="validationCustom01">Product Name</label>
                                    <input type="text" class="form-control" id="validationCustom01" name="product_name" placeholder="Product Name" required="">
                                    <div class="invalid-feedback">
                                        Please Enter Product Name
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="validationCustom06">Tax</label>
                                    <input type="text" class="form-control" id="validationCustom06" name="tax_percent" placeholder="Tax" required="">
                                    <div class="invalid-feedback">
                                        Please Enter Tax
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="validationCustom07">Category</label>
                                    <select id="validationCustom07" name="category_id" class="form-control" required="">
                                        <option value="">Choose Category</option>
                                        <?php foreach($categories AS $category) {?>
                                            <option value="<?php echo $category['id'];?>"><?php echo $category['name'];?></option>
                                        <?php }?>
                                    </select>
                                    <div class="invalid-feedback">
                                        Please Select Category
                                    </div>
                                </div>
                                <input type="hidden" name="product_id">
                            </div>
                            <button type="submit" class="btn btn-success btn-sm mt-2">SUBMIT</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal_form2" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload CSV Here</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                    </div>
                    <div class="modal-body">
                        <?php $attributes = array('class'=>'dropzone');
                        echo form_open_multipart('admin/upload_csv',$attributes); ?>
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                                                        
                                </div>
                            </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- main content area end -->
    <?php $this->load->view('footer');?>
    <script src="<?php echo base_url()?>assets/js/dropzone.js"></script>
    <script type="text/javascript">


        $(document).ready(function(){

            $('#myForm').on('submit', function(ev){
                var $myForm = $('#myForm');

                ev.preventDefault();

                if(document.getElementById("myForm").checkValidity() === false)
                {
                    ev.preventDefault();
                    ev.stopPropagation();

                    $myForm.addClass('was-validated');
                    return false;
                }

                $myForm.addClass('was-validated');

                $.ajax({
                    url: "<?php echo base_url();?>admin/ajax_save_product",
                    type:   'POST',
                    data: $myForm.serialize(),
                    async: false,
                    success: function(msg){
                        $('#modal_form').modal('hide');
                        refresh_dataTable(); 
                    },
                    error: function() {
                        alert("Something Wrong");
                    }
                });                
            });


            $( "#dataTable tbody" ).on( "click", ".delete-action", function() {
                var product_id = $(this).data('product_id');

                if(!confirm("Are you sure want to delete this Product?"))
                {
                    return false;
                }

                $.ajax({
                    url: "<?php echo base_url();?>admin/ajax_delete_product",
                    type: 'POST',
                    data: {'product_id' : product_id},
                    async: false,
                    success: function(msg){
                        refresh_dataTable();
                    },
                    error: function() {
                        alert("Something wrong");
                    }
                });
            });

            $( "#dataTable tbody" ).on( "click", ".edit-action", function() {
                var product_id = $(this).data('product_id');

                $.ajax({
                    url: "<?php echo base_url();?>admin/ajax_get_product_detail",
                    type: 'POST',
                    data: {'product_id' : product_id},
                    async: false,
                    success: function(data){
                        $('#modal_form').find('input[name=product_name]').val(data.name);
                        $('#modal_form').find('input[name=tax_percent]').val(data.tax_percent);
                        $('#modal_form').find('select[name=category_id]').val(data.category_id);
                        $('#modal_form').find('input[name=product_id]').val(data.long_id);
                        $('#modal_form').modal('show');
                    },
                    error: function() {
                        alert("Something wrong");
                    }
                });
            });

            $('#dataTable').dataTable( {
                "ajax": '<?php echo base_url();?>admin/ajax_product_list',
                'ordering' : false,
                'processing': true,
                'language' : {
                    'zeroRecords' : 'No Products Found',
                    'processing' : '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                }
            } );
        });

        function refresh_dataTable()
        {
            $('#dataTable').dataTable().api().ajax.reload();
        }

        $('#modal_form').on('hidden.bs.modal', function (e) {
          $(this)
            .find("input,textarea,select")
               .val('')
               .end()
            .find("input[type=checkbox], input[type=radio]")
               .prop("checked", "")
               .end();

               $('#myForm').removeClass('was-validated');
        });
    </script>
</body>

</html>