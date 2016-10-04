<h1 class="page-header">Chart Assist - Care@Home Admin <small>Call-in number: <?=$fromNumber?></small></h1>
<div class="table-responsive">
	<table class="table table-striped">
	<tbody>
<?php
		$patient = get_patient($status->patient_id );
		$staff = get_staff( $status->staff_id );
		$type = $status->type;
		if( strtolower( $status->type ) == 'in' || strtolower( $status->type ) == 'out' ){
			$type = 'Checking '.ucfirst( $status->type );
		}
		$message = ' - ';
		if( !empty($status->message) ){
			$message = '<audio src="'.$status->message.'.mp3" controls preload="auto" autobuffer></audio>';
		}
		$q1 = ' - ';
		if( !empty($status->question1) ){
			$q1 = '<audio src="'.$status->question1.'.mp3" controls preload="auto" autobuffer></audio>';
		}
		$q2 = ' - ';
		if( !empty($status->question2) ){
			$q2 = '<audio src="'.$status->question2.'.mp3" controls preload="auto" autobuffer></audio>';
		}
		$q3 = ' - ';
		if( !empty($status->question3) ){
			$q3 = '<audio src="'.$status->question3.'.mp3" controls preload="auto" autobuffer></audio>';
		}
?>
		<tr>
			<th>Date</th>
			<td><?php echo date("F j, Y, g:i a",$status->created);?></td>
		</tr>
		<tr>
			<th>Patient or Phone Number</th>
			<td><?php echo $patient->name?></td>
		</tr>
		<tr>
			<th>Staff Name or ID</th>
			<td><?php echo $staff->name?></td>
		</tr>
		<tr>
			<th>Status</th>
			<td><?php echo $type?></td>
		</tr>
		<tr>
			<th>Message</th>			
			<td><?php echo $message?></td>
		</tr>
	</tbody>
	</table>
</div>
<?php 	if( ($q1 != ' - ') || ($q2 != ' - ') || ($q3 != ' - ') ){	?>
		<hr />
		<h4>Questions</h4>
		<div class="table-responsive">
			<table class="table table-striped">
			<tbody>
				<tr>
					<th>1.</th>
					<td><?php echo $patient->question1?></td>
					<td><?php echo $q1;?></td>
				</tr>
				<tr>
					<th>2.</th>
					<td><?php echo $patient->question2?></td>
					<td><?php echo $q2;?></td>
				</tr>
				<tr>
					<th>3.</th>
					<td><?php echo $patient->question3?></td>
					<td><?php echo $q3;?></td>
				</tr>
			</tbody>
			</table>
		</div>
<?php 	}	?>
<?php 	if( !isset($_REQUEST['embed']) ){	?>
	<br />
	<div>
		<a href="/admin" class="btn btn-primary">&#8592	Back</a>
	</div>
<?php 	}	?>
</div>