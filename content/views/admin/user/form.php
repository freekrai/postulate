<h1 class="page-header"><?php echo $pageTitle?></h1>
<form role="form"  method="POST" action="<?php echo $action?>">
<fieldset>
	<div class="form-group">
		<label for="login">Email address</label>
		<input type="email" class="form-control" id="login" name="login" placeholder="Enter email" value="<?php echo ( isset($user) ? $user['login'] : '');?>" required>
	</div>
	<div class="form-group">
		<label for="name">Name</label>
		<input type="text" class="form-control" id="name" name="name" placeholder="Enter name" value="<?php echo ( isset($user) ? $user['name'] : '');?>">
	</div>
	<div class="form-group">
		<label for="name">Phone Number</label>
		<input type="tel" class="form-control" autocomplete="off" id="phone" name="phone" placeholder="Enter Phone Number" value="<?php echo ( isset($user) ? $user['phone'] : '');?>">
	</div>
	<div class="form-group">
		<label for="name">Password</label>
		<div class="row">
		  <div class="col-xs-6">
				<input type="password" class="form-control" autocomplete="off"  id="pass1" name="pass1" placeholder="Enter password" value="">
		  </div>
  		  <div class="col-xs-6">
				<input type="password" class="form-control" autocomplete="off" id="pass2" name="pass2" placeholder="Confirm password"  value="">
  		  </div>
		</div>
	</div>
	<button type="submit" class="btn btn-primary">Save</button>
</fieldset>
</form>