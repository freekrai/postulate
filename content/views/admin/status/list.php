<h1 class="page-header">Chart Assist - Care@Home Admin <small>Call-in number: <?=$fromNumber?></small></h1>
<div class="table-responsive">
	<table class="table table-striped">
	<thead>
	<tr>
	<th>Date</th>
	<th>Patient or Phone Number</th>
	<th>Staff Name or ID</th>
	<th>Status</th>
	<th>Message</th>
	<th>&nbsp;</th>
	</tr>
	</thead>
	<tbody>
<?php
	foreach($status as $s){
		$patient = get_patient($s->patient_id );
		$staff = get_staff( $s->staff_id );
		$type = $s->type;
		if( strtolower( $s->type ) == 'in' || strtolower( $s->type ) == 'out' ){
			$type = 'Checking '.ucfirst( $s->type );
		}
		$message = ' - ';
		if( !empty($s->message) ){
			$message = '<audio src="'.$s->message.'.mp3" controls preload="auto" autobuffer></audio>';
		}
?>
		<tr>
			<td><?php echo date("F j, Y, g:i a",$s->created);?></td>
			<td><?php echo $patient->name?></td>
			<td><?php echo $staff->name?></td>
			<td><?php echo $type?></td>
			<td><?php echo $message?></td>
			<td>
				<a class="modalButton  btn btn-primary" data-toggle="modal" data-src="/status/<?php echo $s->id?>?embed=1" data-title="<?php echo $patient->name?> - <?php echo date("F j, Y, g:i a",$s->created);?>" data-target="#modalbox">Details</a>
			</td>
		</tr>
<?php		
	}
?>
	</tbody>
	</table>
</div>