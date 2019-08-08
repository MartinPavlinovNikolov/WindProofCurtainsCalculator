<table style="position: relative;" class="table table-condensed table-sm table-bordered text-center">
	<thead class="table-info">
	    <tr>
	    	<th v-for="header in headers" scope="col"><p v-html="header"></p></th>
	    </tr>
	</thead>
	<tbody>
		<tr v-for="(order, o) in orders" :class="isCompleated(o) ? 'table-success' : 'table-danger'">
	    	<th scope="row">{{order.id}}</th>
	    	<th scope="row">{{order.username}}</th>
	    	<td scope="row">{{(order.paid / 100)}}£</td>
	    	<td scope="row">{{order.ordered_at}}</td>
	    	<td scope="row">

	    		<select class="form-control w-75 m-auto select-me" :name="'select-order-O'+order.id" v-model="order.status">
	    			<option v-for="(email_template, e) in order.email_templates" :value="email_template.slug" @click="updateOrderStatus(o, e)">{{email_template.title}}</option>
	    		</select>

	    	</td>
	    	<td>
	    		<button type="button" class="btn btn-sm btn-secondary notify-with-email" @click="sendEmailManualy(order, o)" v-html="order.btn_text(order, o)"></button>
	    	</td>
	    	<td>
				<!-- Button trigger modal -->
				<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" :data-target="'#exampleModalCenterO'+order.id+'A'">виж</button>

				<!-- Modal -->
				<div class="modal fade" :id="'exampleModalCenterO'+order.id+'A'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
					<div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width: 1175px;margin-right: 0;">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body" style="background-color: #F0F0F0;">
								<div class="row">
									<div class="col-4 ml-3" style="background-color: #FFFFFF;border-radius: 10px;">
										<h5 class="mt-3"><b>Данни на клиента</b></h5>
										<ul class="col text-left pt-3 pb-3">
						    				<li>
						    					<span><b>Име: </b></span>
						    					<span>{{order.username}}</span>&nbsp;&nbsp;
						    				</li>
						    				<li>
						    					<span><b>Тел: </b></span>
						    					<span>{{order.phone}}</span>
						    				</li>
						    				<li>
						    					<span><b>е-маил: </b></span>
						    					<span>{{order.email}}</span>&nbsp;&nbsp;
						    				</li>
						    				<li>
						    					<span><b>адрес: </b></span>
						    					<span>{{order.address}}</span>&nbsp;&nbsp;
						    				</li>
										</ul>
									</div>
									<div class="col-4 ml-3" style="background-color: #FFFFFF;border-radius: 10px;">
										<h5 class="mt-3"><b>Данни на поръчката</b></h5>
										<ul class="col text-left pt-3 pb-3">
						    				<li>
						    					<span><b>No: </b></span>
						    					<span>{{order.id}}</span>&nbsp;&nbsp;
						    				</li>
						    				<li>
						    					<span><b>Дата: </b></span>
						    					<span>{{order.ordered_at}}</span>&nbsp;&nbsp;
						    				</li>
						    				<li>
						    					<span><b>Сума: </b></span>
						    					<span>{{(order.paid / 100).toFixed(2)}}£</span>&nbsp;&nbsp;
						    				</li>
						    				<li>
						    					<span><b>Статус: </b></span>
												<span>{{orderStatus(o)}}</span>
						    				</li>
						    				<li>
						    					<span><b>Цвят: </b></span>
						    					<span>{{order.color}}</span>
						    				</li>
										</ul>
									</div>
									<div class="col-3 ml-3" style="background-color: #FFFFFF;border-radius: 10px;">
				    					<h5 class="mt-3"><b>Снимка на мястото за монтаж</b></h5>
										<img class="img-fluid img-thumbnail" :src="order.image_of_the_place">
										<a :href="order.image_of_the_place" class="btn btn-info btn-sm mt-3 mb-3" download>изтегли оригинал</a>
									</div>
								</div>
								<div class="row mt-3">
									<div class="col-12">
										<ul class="nav nav-tabs" id="myWallTabO" role="tablist">
										  	<li v-for="(wall, w) in order.walls" class="nav-item">
										    	<a class="nav-link" :class="w == 0 ? 'active' : ''" :id="'wall-tabO'+wall.id+order.id" data-toggle="tab" :href="'#wallO'+wall.id+order.id" role="tab" :aria-controls="'wallO'+wall.id+order.id" :aria-selected="w == 0 ? 'true' : 'false'">{{(w + 1)}}</a>
										  	</li>
										</ul>
									</div>
									<div class="col-12" style="margin-left: .95rem !important;border-left: 1px solid #dee2e6;border-right: 1px solid #dee2e6;border-bottom: 1px solid #dee2e6;max-width: 97.3% !important;background-color: #FFFFFF;">
										<div class="tab-content" id="myWallContentO">
										  	<div v-for="(wall, w) in order.walls" class="tab-pane fade" :class="w == 0 ? 'show active' : ''" :id="'wallO'+wall.id+order.id" role="tabpanel" :aria-labelledby="'wall-tabO'+wall.id+order.id">
												<div class="container mt-3 mb-3">
													<div class="row">
														<div class="col-8 text-left">
															<div v-for="(dimension, d) in wall.dimensions">
																<p v-if="wall.door_starts_from && d == 0"><b>Врата:</b> {{wall.door_starts_from}}{{order.measurment}} от долният ляв ъгъл
																<p v-else-if="d == 0"><b>Врата:</b> не</p>
																<p v-if="d == 0"><b>Параметри:</b>
																<p class="ml-3">- Страна "{{dimension.letter}}": {{dimension.value}}{{order.measurment}}</p>
															</div>
															<p v-if="wall.note">Допълнителна информация от клиента: <i>{{wall.note}}</i></p>
															<p v-else>Допълнителна информация от клиента: няма</p>
														</div>
														<div class="col-4">
															<div class="row img-thumbnail">
																<div class="col">
																	<img class="img-fluid" :src="wall.shape">
																	<p style="font-size: 10px !important;"><i>(схема на избраната стена)</i></p>
																</div>
															</div>
														</div>
													</div>
												</div>
										  	</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
	    	</td>
	    </tr>
	</tbody>
</table>