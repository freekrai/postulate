<h1 class="page-header">Users</h1>
<div class="table-responsive">
	<table class="table table-striped">
	<thead>
	<tr>
		<th>Phone Number</th>
		<th>Name</th>
		<th>Login</th>
		<th>&nbsp;</th>
	</tr>
	</thead>
	<tbody>
<?php foreach($user as $u){	?>
	<tr>
		<td><?php echo $u['phone']?></td>
		<td><?php echo $u['name']?></td>
		<td><?php echo $u['login']?></td>
		<td>
			<a class="modalButton btn btn-primary" data-toggle="modal" data-src="/admin/user/edit/<?php echo $u['_id']?>?embed=1" data-title="<?php echo $u['name']?>"data-target="#modalbox">Edit</a>
			<a class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user member?');" href="/admin/user/delete/<?php echo $u['_id']?>">Delete</a>		
		</td>
	</tr>
<?php 	}	?>
	</tbody>
	</table>
</div>