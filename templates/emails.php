<div class="container-fluid pt-3">
	<div style="
			position: fixed;
			top: 20%;
			right: 2%;
			width: 30%;
			background-color: #f8f8f8;
		"  class="card">
		<div class="card-header text-center">
		Легенда на заместителите
		</div>
		<div class="card-body">
			<ul>
				<li>[username] - името на клиента;</li>
				<li>[phone] - телефон на клиента;</li>
				<li>[customer_email] - имейл на клиента;</li>
				<li>[address] - адрес на клиента;</li>
				<li>[paid_at] - дата на плащането;</li>
				<li>[order] - номер на поръчката;</li>
				<li>[paid] - платена сума;</li>
				<li>[ordered_at] - дата на поръчване;</li>
				<li>[finished_at] - дата на приключване;</li>
			</ul>
		</div>
	</div>
	<div class="row">
		<div class="col-7">
			<h2>Имейли</h2>
		</div>
	</div>
	<?php foreach($email_templates as $template){ ?>
	<div class="row">
		<div style="background-color: #f8f8f8;" class="col-7 card mt-5">
			<form id="form<?= $template['id'] ?>" action="#" method="post">
				<input type="hidden" name="id" value="<?= $template['id'] ?>">
				<div class="form-group">
					<h6>Вид на имейла: "<i><?= $template['title'] ?></i>"</h6>
				</div>
				<div class="form-group">
					<label for="">Тема</label>
					<input name="subject" type="text" class="form-control" placeholder="subject" value="<?= $template['subject'] ?>" required>
				</div>
				<div class="form-group">
					<label for="">Съдържание</label>
					<textarea name="body" class="form-control" rows="5"><?= $template['body'] ?></textarea required>
				</div>
				<input id="submit<?= $template['id'] ?>" class="btn btn-success col-3 offset-9 etfs" type="submit" name="" value="запази/обнови">
			</form>
		</div>
	</div>
	<?php } ?>
</div>

<script type="text/javascript">
	jQuery(document).ready(function(){

		jQuery('.etfs').click(function(e){
			e.preventDefault();
			let form = jQuery(e.target.parentElement);
			let subject = form.find('input[type="text"]').val();
			let body = form.find('textarea').val();
			let id = form.find('input[type="hidden"]').val();
			let action = 'update_email_template';
			let data = {
				action: action,
				id: id,
				subject: subject,
				body: body
			};
			jQuery.post(ajaxurl, data, function(response){
				alert(response);
			});
		});
	});
</script>