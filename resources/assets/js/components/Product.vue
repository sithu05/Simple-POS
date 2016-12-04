<style scoped>
    .action-link {
        cursor: pointer;
    }

    .m-b-none {
        margin-bottom: 0;
    }
</style>

<template>
	<div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">
                    	Products Management
                    	<button class="btn btn-primary btn-sm pull-right" @click="showForm()">Add New</button>
                	</div>

                    <div class="panel-body">
						<table class="table table-striped m-b-none">
							<thead>
								<tr>
		                    		<th>#</th>
		                    		<th>Category</th>
		                    		<th>Name</th>
		                    		<th>Price</th>
		                    		<th>Status</th>
		                    		<th>Actions</th>
		                    	</tr>	
							</thead>
	                    	
	                    	<tbody>
	                    		<tr v-for="product in products">
		                    		<td>{{ product.id }}</td>
		                    		<td>{{ product.category.name }}</td>
		                    		<td>{{ product.name }}</td>
		                    		<td>{{ product.price }}</td>
		                    		<td>
		                    			<label class="label label-sm label-primary" v-if="product.status == 1">Yes</label>
		                    			<label class="label label-sm label-danger" v-else>No</label>
	                    			</td>
		                    		<td>
		                    			<button class="btn btn-sm btn-info action-link" @click="showForm(product)">Edit</button>
		                    			<button class="btn btn-sm btn-danger action-link" @click="destroy(product)">Del</button>
		                    		</td>
		                    	</tr>
	                    	</tbody>
	                    </table>
                    </div>
                </div>

                <!-- Create & Edit Modal -->
                <div class="modal fade" id="modal_form" tabindex="-1" role="dialog">
                	<div class="modal-dialog">
                		<div class="modal-content">
                			<div class="modal-header">
                				<button type="button " class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

		                        <h4 class="modal-title" v-if="product.id == 0">Create New Product</h4>
		                        <h4 class="modal-title" v-if="product.id > 0">Edit Product</h4>
                			</div>

                			<div class="modal-body">
                				<!-- Form Errors -->
		                        <div class="alert alert-danger" v-if="product.errors.length > 0">
		                            <p><strong>Whoops!</strong> Something went wrong!</p>
		                            <br>
		                            <ul>
		                                <li v-for="error in product.errors">
		                                    {{ error }}
		                                </li>
		                            </ul>
		                        </div>

								<form class="form-horizontal" role="form">
									<!-- Categories -->
									<div class="form-group">
										<label class="col-md-3 control-label">Category</label>

										<div class="col-md-7">
											<select id="category_id" v-model="product.category_id" class="form-control">
												<option value="">Please Select</option>
												<option v-for="category in categories" v-bind:value="category.id">{{ category.name }}</option>
											</select>
										</div>
									</div>

								 	<!-- Name -->
		                            <div class="form-group">
		                                <label class="col-md-3 control-label">Name</label>

		                                <div class="col-md-7">
		                                    <input type="text" class="form-control" v-model="product.name">
		                                </div>
		                            </div>

		                            <!-- Description -->
		                            <div class="form-group">
		                                <label class="col-md-3 control-label">Description</label>

		                                <div class="col-md-7">
		                                    <input type="text" class="form-control" v-model="product.description">
		                                </div>
		                            </div>

		                            <!-- Price -->
		                            <div class="form-group">
		                                <label class="col-md-3 control-label">Price</label>

		                                <div class="col-md-7">
		                                    <input type="number" class="form-control" v-model="product.price">
		                                </div>
		                            </div>

		                            <!-- Status -->
		                            <div class="form-group">
		                            	<label class="col-md-3 control-label">Status</label>

		                            	<div class="col-md-7">
		                            		<input type="checkbox" value="1" v-model="product.status">
		                            	</div>
		                            </div>

								</form>
                			</div>

                			 <!-- Modal Actions -->
		                    <div class="modal-footer">
		                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

		                        <button type="button" class="btn btn-primary" @click="update()">
		                            {{ product.id == 0 ? 'Create' : 'Update' }}
		                        </button>
		                    </div>
                		</div>
                	</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
	export default {
		data() {
			return {
				products: [],
				categories: [],
				product: {
					name: '',
					status: 0,
					errors: []
				}
			};
		},

        mounted() {
            this.prepareComponent();
        },

        methods: {
        	/**
        	 * Get All Products
        	 */
        	getProducts() {
        		this.$http.get('/product/0').then(response => {
        			this.products = response.data;
        		});
        	},

        	/**
        	 * Show Popup Modal
        	 */
        	showForm(product) {
        		this.$http.get('/category/1').then(response => {
        			this.categories = response.data;

        			if (product == null) {
	        			this.product = { id: 0, name: '', status: 0, errors: [] };
	        		} else {
	        			this.product = product;
	        			this.product.errors = [];
	        		}

	        		$('#modal_form').modal('show');
        		});
        	},

        	/**
        	 * Prepare the component
        	 */
        	prepareComponent() {
        		this.getProducts();

        		$('#modal_form').on('shown.bs.modal', () => {
        			$('#category_id').focus();
        		});
        	},

        	/**
        	 * Update means create or update
        	 */
        	update() {
        		if (this.product.id > 0) {
        			this.persistClient('put', '/product/' + this.product.id, this.product, '#modal_form');
        		} else {
        			this.persistClient('post', '/product', this.product, '#modal_form');
        		}
        	},

        	 /**
             * Persist the client to storage using the given form.
             */
            persistClient(method, uri, form, modal) {
                form.errors = [];

                this.$http[method](uri, form)
                    .then(response => {
                    	this.getProducts();

                        form.errors = [];

                        $(modal).modal('hide');
                    })
                    .catch(response => {
                        if (typeof response.data === 'object') {
                            form.errors = _.flatten(_.toArray(response.data));
                        } else {
                            form.errors = ['Something went wrong. Please try again.'];
                        }
                        $('#category_id').focus();
                    });
            },

            destroy(product) {
            	var vm = this;

            	swal({
            		title: 'Are you sure?',
            		type: 'warning',
            		showCancelButton: true,
            		confirmButtonText: 'Yes, delete it!'
            	}).then(function () {
            		vm.$http.delete('/product/' + product.id).then(response => {
            			vm.getProducts();
            		});
            	}, function () {});
            }
        }
    }
</script>