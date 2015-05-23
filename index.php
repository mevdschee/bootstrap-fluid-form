<?php
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']);
$is_post = $_SERVER['REQUEST_METHOD']=='POST';

if(!$is_ajax && $is_post) {
	$_POST['submitted'] = 1;
}

$data = $_POST;

$data['errors'] = validate($data);
calculate($data);

if ($data['submitted'] && !$data['errors']) {
	$data['errors'] = validate_required($data);
	if (!$is_ajax && !$data['errors']) {
		die(header('Location: saved.html'));
	}
}
$data['submitted'] = 0;

if($is_ajax) {
	die(json_encode($data));
}

function validate($data)
{
	$errors = array();
	foreach ($data as $key=>$val) {
		if (!empty($val)) {
			switch ($key) {
				case 'number1':
				case 'number2':
				case 'multiplier': $errors[$key] = !is_numeric($val); break;
			}
		}
	}
	return array_filter($errors);
}

function validate_required($data)
{
	$errors = array();
	foreach ($data as $key=>$val) {
		switch ($key) {
			case 'number1':
			case 'number2': $errors[$key] = !trim($val); break;
		}
	}
	return array_filter($errors);
}

function calculate(&$data)
{
	$data['sum'] = $data['number1']+$data['number2'];
	$data['total'] = $data['sum']*($data['multiplier']?:1);
}
?>
<html>
  <head>
    <title>Bootstrap Fluid Form</title>
    <script src="jquery.min.js"></script>
    <script src="jquery.populate.pack.js"></script>
    <script src="bootstrap.min.js"></script>
    <link href="bootstrap.min.css" rel="stylesheet">
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="col-sm-5 col-sm-offset-4">
          <h3>Fluid Form</h3>
        </div>
        <form class="form-horizontal" role="form" method="post" action="">
          <div class="form-group">
            <label for="field1" class="col-sm-4 control-label">* Number 1</label>
            <div class="col-sm-5">
              <input type="text" class="form-control" id="field1" name="number1" value="<?php echo htmlentities($data['number1']); ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="field2" class="col-sm-4 control-label">* Number 2</label>
            <div class="col-sm-5">
              <input type="text" class="form-control" id="field2" name="number2" value="<?php echo htmlentities($data['number2']); ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="field3" class="col-sm-4 control-label">Sum</label>
            <div class="col-sm-5">
              <pre><span id="sum"><?php echo htmlentities($data['sum']); ?></span>&nbsp;</pre>
            </div>
          </div>
          <div class="form-group">
            <label for="field4" class="col-sm-4 control-label">Multiplier</label>
            <div class="col-sm-5">
              <input type="text" class="form-control" id="field4" name="multiplier" value="<?php echo htmlentities($data['multiplier']); ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="field5" class="col-sm-4 control-label">Total</label>
            <div class="col-sm-5">
              <pre><span id="total"><?php echo htmlentities($data['total']); ?></span>&nbsp;</pre>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-4 col-sm-7">
              <input type="hidden" name="submitted" value="<?php echo htmlentities($data['submitted']); ?>"/>
              <button type="submit" class="btn btn-primary">Save</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </body>
</html>
<script>

function showErrors(validation){
	$(":input").each(function() {
		$(this).parents("div.form-group").removeClass('has-error');
	});
	$.each( validation, function( key, value ) {
		$(':input[name="'+key+'"]').parents("div.form-group").addClass('has-error');
	});
}

showErrors(<?php echo json_encode($data['errors']);?>);

$( ":input" ).on( "change", update);

function update() {
	$.post('', $(document.forms[0]).serialize(), function( data ) {
			delete data[$('input:focus').attr('name')];
			$(document.forms[0]).populate(data,{resetForm:false});
			if (data.errors) showErrors(data.errors);
		}, "json");
}
</script>
