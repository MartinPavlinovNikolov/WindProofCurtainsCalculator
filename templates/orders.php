<div id="orders-menager" class="container-fluid pt-3">
	<div class="row">
		<div class="col-12">
			<h2>Поръчки</h2>
		</div>
	</div>
	<div class="row">
		<div class="col-12 mt-5">
			<ul class="nav nav-tabs" id="myTab" role="tablist">
			  	<li class="nav-item">
			    	<a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Всички</a>
			  	</li>
			  	<li class="nav-item">
			    	<a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Активни</a>
			  	</li>
			  	<li class="nav-item">
			    	<a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Приключени</a>
			  	</li>
			</ul>
		</div>
		<div class="col-12">
			<div class="tab-content" id="myTabContent">
			  	<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
			  		<?php
			  		include(plugin_dir_path( dirname( __FILE__, 1 ) ) . 'templates/orders_table.php')
			  		?>
			  	</div>
			  	<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
			  		<?php
			  		include(plugin_dir_path( dirname( __FILE__, 1 ) ) . 'templates/incompleated_orders_table.php')
			  		?>
			  	</div>
			  	<div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
			  		<?php
			  		include(plugin_dir_path( dirname( __FILE__, 1 ) ) . 'templates/compleated_orders_table.php')
			  		?>
			  	</div>
			</div>
		</div>
	</div>

<script type="text/javascript">

	let orders_menager = new Vue({
		el: '#orders-menager',
		data: {
			headers: ['номер', 'име', 'сума', 'дата', 'статус', 'уведомление на клиента</br>за статус на поръчката', 'детайли', 'изтрии'],
			orders: <?= json_encode($orders) ?>
		},
		created: function(){
			this.orders.map(function(o){
				o.btn_text = function(order, i){
					let str = 'Уведоми';
					if(order.email_templates[6].sended == 'true' && order.email_templates[6].selected == 'true' && order.status == order.email_templates[6].slug){
						str = '<span class="dashicons dashicons-yes"></span>';
					}
					if(order.email_templates[5].sended == 'true' && order.email_templates[5].selected == 'true' && order.status == order.email_templates[5].slug){
						str = '<span class="dashicons dashicons-yes"></span>';
					}
					if(order.email_templates[4].sended == 'true' && order.email_templates[4].selected == 'true' && order.status == order.email_templates[4].slug){
						str = '<span class="dashicons dashicons-yes"></span>';
					}
					if(order.email_templates[3].sended == 'true' && order.email_templates[3].selected == 'true' && order.status == order.email_templates[3].slug){
						str = '<span class="dashicons dashicons-yes"></span>';
					}
					if(order.email_templates[2].sended == 'true' && order.email_templates[2].selected == 'true' && order.status == order.email_templates[2].slug){
						str = '<span class="dashicons dashicons-yes"></span>';
					}
					if(order.email_templates[1].sended == 'true' && order.email_templates[1].selected == 'true' && order.status == order.email_templates[1].slug){
						str = '<span class="dashicons dashicons-yes"></span>';
					}
					if(order.email_templates[0].sended == 'true' && order.email_templates[0].selected == 'true' && order.status == order.email_templates[0].slug){
						str = '<span class="dashicons dashicons-yes"></span>';
					}
					return str;
				}
			});
		},
		methods: {
			deleteOrder: function(order, index){
				let that = this;
				if(confirm('Наистина ли желаете да изтриете тази поръчка?')){
					jQuery.ajax({
			            type: "POST",
			            url: '/wp-admin/admin-ajax.php',
			            data: {
			            	"action": "delete_order",
			                "order_id": order.id
			            },
			            success: function(response){
			            	that.orders.splice(index, 1);
			            	alert('Поръчка с номер: '+order.id+' беше изтрита успешно!');
			            }
			        });
				}
			},
			orderStatus: function(index){
				let status = '';
				let current_status = this.orders[index].status;
				this.orders[index].email_templates.map(function(e, i){
					if(e.slug == current_status){
						status = e.title;
					}
				});
				return status;
			},
			isCompleated(index){
				let compleated = false;
				this.orders[index].email_templates.map(function(et, i){
					if(i == 4 || i == 6){
						if(et.selected == 'true'){
							compleated = true;
						}
					}
				});
				return compleated;
			},
			updateOrderStatus: function(o){
				let that = this;
				jQuery.ajax({
		            type: "POST",
		            url: '/wp-admin/admin-ajax.php',
		            data: {
		            	"action": "update_order_status",
		                "order_id": that.orders[o].id,
		                "status": that.orders[o].status
		            },
		            success: function(response){
		            	that.$forceUpdate();
		            }
		        });
			},
			sendEmailManualy: function(order, index){
				let that = this;
				if(order.btn_text(order, index) == 'Уведоми'){
			        event.preventDefault();
			        let email_slug;
			        order.email_templates.map(function(e){
			        	if(e.slug == order.status){
			        		email_slug = e.slug;
			        	}
			        });
			        let order_id = order.id;

			        jQuery.ajax({
			            type: "POST",
			            url: '/wp-admin/admin-ajax.php',
			            data: {
			            	"action": "send_email_manualy",
			                "email_slug": email_slug,
			                "order_id": order_id
			            },
			            success: function(response){
			                if(response === 'success'){
			                    alert('Успешно изпратен!');
			                    order.email_templates.map(function(e){
			                    	if(e.slug == order.status){
			                    		e.sended = 'true';
			                    		e.selected = 'true';
			                    	}
			                    });
			                }
			                else {
			                    alert(response);
			                }
			            }
			        });
				}
			}
		}
	});

</script>