<?php include 'db_connect.php'; ?>

<div class="container-fluid">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header">
				<large class="card-title">
					<div class="row">
						<b class="col-md-8">Payment List</b>
						<div class="col-md-2">
							<button class="btn btn-info btn-block btn-sm float-right" type="button" id="print_payments"><i class="fa fa-print"></i> Print</button>
						</div>
						<div class="col-md-2">
							<button class="btn btn-primary btn-block btn-sm float-right"  type="button" id="new_payments"><i class="fa fa-plus"></i> New Payment</button>
						</div>
					</div>
				</large>
				
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="loan-list">
					<colgroup>
						<col width="10%">
						<col width="25%">
						<col width="25%">
						<col width="20%">
						<col width="10%">
						<col width="10%">
					</colgroup>
					<thead>
						<tr>
							<th class="text-center">#</th>
							<th>Payment Date</th>
							<th class="text-center">Loan Reference No</th>
							<th class="text-center">Payee</th>
							<th class="text-center">Amount</th>
							<th class="text-center">Penalty</th>
							<th class="text-center remove-on-print">Action</th>
						</tr>
					</thead>
					<tbody>
						<?php

                            $i = 1;

                            $qry = $conn->query("SELECT p.*,l.ref_no,concat(b.lastname,', ',b.firstname,' ',b.middlename)as name, b.contact_no, b.address, l.status from payments p inner join loan_list l on l.id = p.loan_id inner join borrowers b on b.id = l.borrower_id  order by p.id asc");
                            while ($row = $qry->fetch_assoc()):

                         ?>
						 <tr>
						 	
						 	<td class="text-center"><?php echo $i++; ?></td>
							<td>
								<?php echo (new DateTime($row['date_created']))->format('M d, Y h:i A'); ?>
							</td>
						 	<td>
						 			<?php echo $row['ref_no']; ?>
						 	</td>
						 	<td>
						 		<?php echo $row['payee']; ?>
						 		
						 	</td>
						 	<td class="compute-on-print">
						 		<?php echo number_format($row['amount'], 2); ?>
						 		
						 	</td>
						 	<td class="text-center">
						 		<?php echo number_format($row['penalty_amount'], 2); ?>
						 	</td>
						 	<td class="text-center remove-on-print">
						 			<button class="btn btn-outline-primary btn-sm edit_payment" type="button" data-id="<?php echo $row['id']; ?>"><i class="fa fa-edit"></i></button>
						 			<button class="btn btn-outline-danger btn-sm delete_payment" type="button" data-id="<?php echo $row['id']; ?>"><i class="fa fa-trash"></i></button>
						 	</td>

						 </tr>

						<?php endwhile; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<style>
	td p {
		margin:unset;
	}
	td img {
	    width: 8vw;
	    height: 12vh;
	}
	td{
		vertical-align: middle !important;
	}
</style>	
<script>
	$('#loan-list').dataTable()
	$('#new_payments').click(function(){
		uni_modal("New Payement","manage_payment.php",'mid-large')
	})
	$('.edit_payment').click(function(){
		uni_modal("Edit Payement","manage_payment.php?id="+$(this).attr('data-id'),'mid-large')
	})
	$('.delete_payment').click(function(){
		_conf("Are you sure to delete this data?","delete_payment",[$(this).attr('data-id')])
	})
	$('#print_payments').click(function(e) {
		e.preventDefault();
		var html = $('html').clone().find('body').empty()
		var printcontent = $('#loan-list').clone();
		printcontent.attr('border', "1");
		var total = 0;
		printcontent.find('.compute-on-print').each(function(idx, e) {
			var amount = $(e).text().trim().replace(',', '');
			total = total + parseFloat(amount)
			console.log(total)
		})

		console.log(total);
		$("<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td colspan='2'>Payment total: "+String(total)+"</td></tr>").appendTo(printcontent.find('tbody'))
		var printbody = html.append(printcontent)
		printbody.find('.remove-on-print').remove();
		setTimeout(() => {
			var mywindow = window.open('', 'Print Section', 'height=400,width=600');
			mywindow.document.write(printbody.prop('outerHTML'));
			mywindow.document.close(); // necessary for IE >= 10
			mywindow.focus(); // necessary for IE >= 10*/
			mywindow.print();
			mywindow.close();
		}, 500);
		
	})
function delete_payment($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_payment',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Payment successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>