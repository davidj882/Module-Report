
<?php 
	/// IF USE BREADCRUMS
	echo $this->breadCrumbs;
?>

<div class="container">
	<div class="page-section">
		<div class="row">
			<div class="col-md-12">
				<!-- IF USE DIALOGBOX -->
				<?php if (isset($this->dialogBox)): ?>
					<?php print_r($this->dialogBox); ?>
				<?php endif ?>

				<div class="panel panel-default">
					
				</div>
			</div>
		</div>
	</div>
</div>


<!-- IF USE DATATABLE IN TEMPLATE -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.bootstrap.min.css">

<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js"></script>

<script type="text/javascript">
	$(document).ready(function() {
		var table = $('#table').DataTable( {
			lengthChange: false,
			buttons: [ 'copy', 'excel', 'pdf', 'colvis' ]
		} );

		table.buttons().container()
		.appendTo( '#table_wrapper .col-sm-6:eq(0)' );
	} );
</script>