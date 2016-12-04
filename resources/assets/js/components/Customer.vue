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
                    	Customers Management
                    	<button class="btn btn-primary btn-sm pull-right" @click="showForm()">Add New</button>
                	</div>

                    <div class="panel-body">
                        <h4>Customers Management</h4> <hr/>
                        
						<table class="table table-striped m-b-none">
							<thead>
								<tr>
		                    		<th>#</th>
		                    		<th>Name</th>
                                    <th>Phone Number</th>
		                    		<th>Receivable</th>
		                    		<th>Active</th>
		                    		<th>Actions</th>
		                    	</tr>	
							</thead>
	                    	
	                    	<tbody>
	                    		<tr v-for="customer in customers">
		                    		<td>{{ customer.id }}</td>
		                    		<td>{{ customer.name }}</td>
		                    		<td>{{ customer.phone_number }}</td>
                                    <td>{{ customer.receivable_amount }}</td>
		                    		<td>
		                    			<label class="label label-sm label-primary" v-if="customer.is_active == 1">Yes</label>
		                    			<label class="label label-sm label-danger" v-else>No</label>
	                    			</td>
		                    		<td>
		                    			<button class="btn btn-sm btn-info action-link" @click="showForm(customer)">Edit</button>
		                    			<button class="btn btn-sm btn-danger action-link" @click="destroy(customer)">Del</button>
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
                				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

		                        <h4 class="modal-title" v-if="customer.id == 0">Create New Customer</h4>
		                        <h4 class="modal-title" v-if="customer.id > 0">Edit Customer</h4>
                			</div>

                			<div class="modal-body">
                				<!-- Form Errors -->
		                        <div class="alert alert-danger" v-if="customer.errors.length > 0">
		                            <p><strong>Whoops!</strong> Something went wrong!</p>
		                            <br>
		                            <ul>
		                                <li v-for="error in customer.errors">
		                                    {{ error }}
		                                </li>
		                            </ul>
		                        </div>

								<form class="form-horizontal" role="form">
								 	<!-- Name -->
		                            <div class="form-group">
		                                <label class="col-md-3 control-label">Name</label>

		                                <div class="col-md-7">
		                                    <input type="text" class="form-control" id="customer_name" v-model="customer.name">
		                                </div>
		                            </div>

		                            <!-- Phone Number -->
		                            <div class="form-group">
		                                <label class="col-md-3 control-label">Phone Number</label>

		                                <div class="col-md-7">
		                                    <input type="text" class="form-control" v-model="customer.phone_number">
		                                </div>
		                            </div>

		                            <!-- Address -->
		                            <div class="form-group">
		                                <label class="col-md-3 control-label">Address</label>

		                                <div class="col-md-7">
                                            <textarea class="form-control" v-model="customer.address"></textarea>
		                                </div>
		                            </div>

		                            <!-- Active -->
		                            <div class="form-group">
		                            	<label class="col-md-3 control-label">Active</label>

		                            	<div class="col-md-7">
		                            		<input type="checkbox" value="1" v-model="customer.is_active">
		                            	</div>
		                            </div>

								</form>
                			</div>

                			 <!-- Modal Actions -->
		                    <div class="modal-footer">
		                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

		                        <button type="button" class="btn btn-primary" @click="update()">
		                            {{ customer.id == 0 ? 'Create' : 'Update' }}
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
				customers: [],
				customer: {
					name: '',
					is_active: 0,
					errors: []
				}
			};
		},

        mounted() {
            this.prepareComponent();
        },

        methods: {
        	/**
        	 * Get All Customers
        	 */
        	getCustomers() {
        		this.$http.get('/customer/0').then(response => {
        			this.customers = response.data;
        		});
        	},

        	/**
        	 * Show Popup Modal
        	 */
        	showForm(customer) {        		
    			if (customer == null) {
        			this.customer = { id: 0, name: '', is_active: 0, errors: [] };
        		} else {
        			this.customer = customer;
        			this.customer.errors = [];
        		}

        		$('#modal_form').modal('show');
        	},

        	/**
        	 * Prepare the component
        	 */
        	prepareComponent() {
        		this.getCustomers();

        		$('#modal_form').on('shown.bs.modal', () => {
        			$('#customer_name').focus();
        		});
        	},

        	/**
        	 * Update means create or update
        	 */
        	update() {
        		if (this.customer.id > 0) {
        			this.persistClient('put', '/customer/' + this.customer.id, this.customer, '#modal_form');
        		} else {
        			this.persistClient('post', '/customer', this.customer, '#modal_form');
        		}
        	},

        	 /**
             * Persist the client to storage using the given form.
             */
            persistClient(method, uri, form, modal) {
                form.errors = [];

                this.$http[method](uri, form)
                    .then(response => {
                    	this.getCustomers();

                        form.errors = [];

                        $(modal).modal('hide');
                    })
                    .catch(response => {
                        if (typeof response.data === 'object') {
                            form.errors = _.flatten(_.toArray(response.data));
                        } else {
                            form.errors = ['Something went wrong. Please try again.'];
                        }
                        $('#customer_name').focus();
                    });
            },

            destroy(customer) {
            	var vm = this;

            	swal({
            		title: 'Are you sure?',
            		type: 'warning',
            		showCancelButton: true,
            		confirmButtonText: 'Yes, delete it!'
            	}).then(function () {
            		vm.$http.delete('/customer/' + customer.id).then(response => {
            			vm.getCustomers();
            		});
            	}, function () {});
            }
        }
    }
</script>