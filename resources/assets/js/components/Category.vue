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
                    	Categories Management
                    	<button class="btn btn-primary btn-sm pull-right" @click="showForm()">Add New</button>
                	</div>

                    <div class="panel-body">
						<table class="table table-striped m-b-none">
							<thead>
								<tr>
		                    		<th>#</th>
		                    		<th>Name</th>
		                    		<th>Status</th>
		                    		<th>Actions</th>
		                    	</tr>	
							</thead>
	                    	
	                    	<tbody>
	                    		<tr v-for="category in categories">
		                    		<td>{{ category.id }}</td>
		                    		<td>{{ category.name }}</td>
		                    		<td>
		                    			<label class="label label-sm label-primary" v-if="category.status == 1">Yes</label>
		                    			<label class="label label-sm label-danger" v-else>No</label>
	                    			</td>
		                    		<td>
		                    			<button class="btn btn-sm btn-info action-link" @click="showForm(category)">Edit</button>
		                    			<button class="btn btn-sm btn-danger action-link" @click="destroy(category)">Del</button>
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

		                        <h4 class="modal-title" v-if="category.id == 0">Create New Category</h4>
		                        <h4 class="modal-title" v-if="category.id > 0">Edit Category</h4>
                			</div>

                			<div class="modal-body">
                				<!-- Form Errors -->
		                        <div class="alert alert-danger" v-if="category.errors.length > 0">
		                            <p><strong>Whoops!</strong> Something went wrong!</p>
		                            <br>
		                            <ul>
		                                <li v-for="error in category.errors">
		                                    {{ error }}
		                                </li>
		                            </ul>
		                        </div>

								<form class="form-horizontal" role="form">
									 <!-- Name -->
		                            <div class="form-group">
		                                <label class="col-md-3 control-label">Name</label>

		                                <div class="col-md-7">
		                                    <input type="text" class="form-control" id="category_name" v-model="category.name">
		                                </div>
		                            </div>

		                            <!-- Status -->
		                            <div class="form-group">
		                            	<label class="col-md-3 control-label">Status</label>

		                            	<div class="col-md-7">
		                            		<input type="checkbox" value="1" v-model="category.status">
		                            	</div>
		                            </div>

								</form>
                			</div>

                			 <!-- Modal Actions -->
		                    <div class="modal-footer">
		                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

		                        <button type="button" class="btn btn-primary" @click="update()">
		                            {{ category.id == 0 ? 'Create' : 'Update' }}
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
				categories: [],
				category: {
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
        	 * Get All Category
        	 */
        	getCategories() {
        		this.$http.get('/category/0').then(response => {
        			this.categories = response.data;
        		});
        	},

        	/**
        	 * Show Popup Modal
        	 */
        	showForm(category) {

        		if (category == null) {
        			this.category = { id: 0, name: '', status: 0, errors: [] };
        		} else {
        			this.category = category;
        			this.category.errors = [];
        		}

        		$('#modal_form').modal('show');
        	},

        	/**
        	 * Prepare the component
        	 */
        	prepareComponent() {
        		this.getCategories();

        		$('#modal_form').on('shown.bs.modal', () => {
        			$('#category_name').focus();
        		});
        	},

        	/**
        	 * Update means create or update
        	 */
        	update() {
        		if (this.category.id > 0) {
        			this.persistClient('put', '/category/' + this.category.id, this.category, '#modal_form');
        		} else {
        			this.persistClient('post', '/category', this.category, '#modal_form');
        		}
        	},

        	 /**
             * Persist the client to storage using the given form.
             */
            persistClient(method, uri, form, modal) {
                form.errors = [];

                this.$http[method](uri, form)
                    .then(response => {
                    	this.getCategories();

                        form.errors = [];

                        $(modal).modal('hide');
                    })
                    .catch(response => {
                        if (typeof response.data === 'object') {
                            form.errors = _.flatten(_.toArray(response.data));
                        } else {
                            form.errors = ['Something went wrong. Please try again.'];
                        }
                        $('#category_name').focus();
                    });
            },

            destroy(category) {
            	var vm = this;

            	swal({
            		title: 'Are you sure?',
            		type: 'warning',
            		showCancelButton: true,
            		confirmButtonText: 'Yes, delete it!'
            	}).then(function () {
            		vm.$http.delete('/category/' + category.id).then(response => {
            			vm.getCategories();
            		}).catch(response => {
                        swal({
                            type: 'warning',
                            text: response.data.message
                        });
                    });
            	}, function () {});
            }
        }
    }
</script>