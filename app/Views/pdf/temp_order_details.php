<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Temp Invoice : #<?php echo $order_data->id; ?></title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <style>
    table th {
          background: #0c1c60 !important;
          color: #fff !important;
          border: 1px solid #ddd !important;
          line-height:15px!important;
      }
	table td{line-height:15px!important; }
	body{margin-top:20px;
		background:#eee;
}

.invoice {
    padding: 30px;
}

.invoice h2 {
	margin-top: 0px;
	line-height: 0.8em;
}

.invoice .small {
	font-weight: 300;
}

.invoice hr {
	margin-top: 10px;
	border-color: #ddd;
}

.invoice .table tr.line {
	border-bottom: 1px solid #ccc;
}

.invoice .table td {
	border: none;
}

.invoice .identity {
	margin-top: 10px;
	font-size: 1.1em;
	font-weight: 300;
}

.invoice .identity strong {
	font-weight: 600;
}

.grid {
    position: relative;
	width: 100%;
	background: #fff;
	color: #666666;
	border-radius: 2px;
	margin-bottom: 25px;
	box-shadow: 0px 1px 4px rgba(0, 0, 0, 0.1);
}
  </style>
</head>

<body>
	<div class="container">
		<div class="row">
    				<!-- BEGIN INVOICE -->
					<div class="col-xs-12">
						<div class="grid invoice">
							<div class="grid-body">
								<div class="invoice-title">
									<div class="row">
										<div class="col-xs-12">
											<img src="<?php echo base_url(); ?>/uploads/customers/<?php echo $customer_data->logo; ?>" alt="" height="50">
										</div>
									</div>
									<br>
									<div class="row">
										<div class="col-xs-12">
											<h2>Quotation<br>
											<span class="small">Temporary Order #<?php echo $order_data->id; ?></span></h2>
										</div>
										<div class="row">
											<div class="col-xs-6">
												<address>
													<strong><?php echo $customer_data->company_name; ?></strong><br>
													<?php if($customer_data->address) { ?>
													<?php echo $customer_data->address; } ?><br />
													<?php if($customer_data->gst_no) { ?> GST NO:
													<?php echo $customer_data->gst_no; } ?><br />
												</address>
											</div>
											<div class="col-xs-6 text-right">
												<address>
													<?php if($customer_data->bank_name) { ?> Bank Name:
													<?php echo $customer_data->bank_name; } ?><br />
													<?php if($customer_data->bank_ifsc_code) { ?> Bank IFSC Code:
													<?php echo $customer_data->bank_ifsc_code; } ?><br />
													<?php if($customer_data->bank_ac_no) { ?> Bank Acc. No.:
													<?php echo $customer_data->bank_ac_no; } ?><br />
												</address>
											</div>
										</div>
									</div>
								</div>
								<hr>
								<div class="row">
									<div class="col-xs-6">
										<address>
											<strong>Billed To:</strong><br>
											<?php echo $order_data->company_name; ?><br>
											<?php if($order_data->customer_name) { echo $order_data->customer_name; } ?><br/>
											<?php if($order_data->customer_phone) { ?> <abbr title="Phone">P:</abbr> <?php echo $order_data->customer_phone; } ?><br/>										
											<?php if($order_data->customer_email) { ?> <abbr title="Email">E:</abbr> <?php echo $order_data->customer_email; } ?>
										</address>
									</div>
									<div class="col-xs-6 text-right">
										<address>
											<strong>Order Date:</strong><br>
											<?php echo date('d-M-Y', strtotime($order_data->order_at)); ?>
										</address>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<h3>ORDER SUMMARY</h3>
										<table class="table table-striped">
											<thead>
												<tr class="line">
													<td><strong>#</strong></td>
													<td class="text-center"><strong>Category Name</strong></td>
													<td class="text-center"><strong>Product Image</strong></td>
													<td class="text-center"><strong>Product Name</strong></td>
													<td class="text-center"><strong>Description</strong></td>
													<td class="text-center"><strong>Price</strong></td>
													<td class="text-center"><strong>Quantity</strong></td>
													<td class="text-right"><strong>Sub Total</strong></td>
												</tr>
											</thead>
											<tbody>
												<?php $i = 1;
												$total = 0;
												foreach($order_data->order_details as $order_details) { ?>
												<tr>
													<td><?php echo $i; ?></td>
													<td class="text-center"><strong><?php echo $order_details->category_name; ?></strong></td>
													<td class="text-center"><strong><img src="<?php echo base_url(); ?>/uploads/products/<?php echo $order_details->image; ?>" width="20%" /></td>
													<td class="text-center"><strong><?php echo $order_details->name; ?></strong></td>
													<td class="text-center"><?php echo $order_details->product_description; ?></td>
													<td class="text-center">&#8377;<?php echo $order_details->price; ?></td>
													<td class="text-center"><?php echo $order_details->quantity; ?></td>
													<td class="text-right">&#8377;<?php echo ($order_details->quantity*$order_details->price); ?></td>
												</tr>
                        						<?php 
												$total =  $total + ($order_details->quantity*$order_details->price);
												$i++; } ?>
												<tr>
													<td colspan="4">
													</td><td class="text-right"><strong>Total</strong></td>
													<td class="text-right"><strong>&#8377;<?php echo $total; ?></strong></td>
												</tr>
											</tbody>
										</table>
									</div>									
								</div>
                <?php if($order_data->note) { ?>
								<div class="row">
									<div class="col-md-12 text-right identity">
										<p><strong>Note</strong></p>
										<p><?php echo $order_data->note; ?></p>
									</div>
								</div>
                <?php } ?>
                <?php if($order_data->orderby) { ?>
								<div class="row">
									<div class="col-md-12 text-right identity">
										<p>Order By<br><strong><?php echo $order_data->orderby; ?></strong></p>
									</div>
								</div>
                <?php } ?>
                <?php if($order_data->reference) { ?>
								<div class="row">
									<div class="col-md-12 text-right identity">
										<p>Reference Name<br><strong><?php echo $order_data->reference; ?></strong></p>
									</div>
								</div>
                <?php } ?>

							</div>
						</div>
					</div>
					<!-- END INVOICE -->
				</div>
</div>
<script>
window.print();
</script>
</body>

</html>