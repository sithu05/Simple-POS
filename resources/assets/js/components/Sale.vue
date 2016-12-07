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
        <div class="row purchase_list">
            <div class="col-md-12 ">
                <div class="panel panel-default">
                    <div class="panel-heading">
                    	Sale Management
                    	<button class="btn btn-primary btn-sm pull-right" @click="showCreateOrUpdateForm()">Add New</button>
                	</div>

                    <div class="panel-body">
                        <h4>Sale Management</h4> <hr/>
                        
						<table class="table table-striped m-b-none">
							<thead>
								<tr>
		                    		<th>#</th>
		                    		<th>Voucher No</th>
                                    <th>Saled Date</th>
                                    <th>Total Amount</th>
                                    <th>Discount</th>
                                    <th>Paid Amount</th>
                                    <th>Net Amount</th>
		                    		<th>Actions</th>
		                    	</tr>	
							</thead>
	                    	
	                    	<tbody>
	                    		<tr v-for="purchase in purchases">
                                    <td>{{ purchase.id }}</td>
                                    <td>{{ purchase.voucher_no }}</td>
                                    <td>{{ getCustomDateFormat(purchase.purchased_date) }}</td>
                                    <td>{{ purchase.total_amount }}</td>
                                    <td>{{ purchase.discount_amount }}</td>
                                    <td>{{ purchase.received_amount }}</td>
                                    <td>{{ purchase.net_amount }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info action-link" @click="showDetail(purchase)">Detail</button>
                                        <button class="btn btn-sm btn-danger action-link" @click="destroy(purchase)">Del</button>
                                    </td>
                                </tr>
	                    	</tbody>
	                    </table>
                    </div>
                </div>
            </div>
        </div> <!-- /.row -->
        
        <!-- Show Detail Modal -->
        <div class="modal fade" id="detail_modal" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button " class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

                        <h4 class="modal-title">Purchase Detail</h4>
                    </div>

                    <div class="modal-body">
                        <table class="table table-striped m-b-none">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="product in purchase.products">
                                    <td>{{ product.product.name }}</td>
                                    <td>{{ product.quantity }}</td>
                                    <td>{{ product.unique_price }}</td>
                                    <td>{{ product.total_amount }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create & Edit Product Modal -->
        <div class="modal fade" id="product_form" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button " class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

                        <h4 class="modal-title">Add Product</h4>
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
                            <div class="form-group">
                                <label class="col-md-2 control-label">Product</label>

                                <div class="col-md-5">
                                    <select class="form-control" id="product_id" v-model="product.id" v-on:change="getProductName(product.id)">
                                        <option value="">Please Select</option>
                                        <option v-for="product in products" v-bind:value="product.id">{{ product.name }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Quantity</label>

                                <div class="col-md-5">
                                    <input type="number" class="form-control" id="product_quantity" v-model="product.quantity">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Price</label>

                                <div class="col-md-5">
                                    <input type="number" class="form-control" v-model="product.price">
                                </div>
                            </div>
                        </form>
                    </div>

                     <!-- Modal Actions -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                        <button type="button" class="btn btn-primary" @click="addProduct()">
                            Add
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row purchase_form">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        {{ purchase.id == 0 ? 'Create New Purchase' : 'Edit Purchase' }}
                    </div>

                    <div class="panel-body">

                        <div class="alert alert-danger" v-if="purchase.errors.length > 0">
                            <p><strong>Whoops!</strong> Something went wrong!</p>
                            <br>
                            <ul>
                                <li v-for="error in purchase.errors">
                                    {{ error }}
                                </li>
                            </ul>
                        </div>

                        <form class="form-horizontal" role="form">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Supplier</label>

                                <div class="col-md-5">
                                    <select class="form-control" id="supplier_id" v-model="purchase.supplier_id">
                                        <option value="">Please Select</option>
                                        <option v-for="supplier in suppliers" v-bind:value="supplier.id">{{ supplier.name }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Purchase Date</label>

                                <div class="col-md-5">
                                    <input type="text" class="form-control purchased_date" v-model="purchase.purchased_date" v-on:keyup="getDate()">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12">
                                    <button class="btn btn-sm btn-danger pull-right" type="button" @click="showProductForm()">Add Product</button>
                                    <table class="table table-striped m-b-none">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Quantity</th>
                                                <th>Price</th>
                                                <th>Total Price</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="product in purchase.products">
                                                <td>{{ product.name }}</td>
                                                <td>{{ product.quantity }}</td>
                                                <td>{{ product.price }}</td>
                                                <td>{{ product.price * product.quantity }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-danger action-link" type="button" @click="removeProduct(product)">Del</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Total Amount</label>

                                <div class="col-md-5">
                                    <input type="text" class="form-control" disabled="true" v-model="purchase.total_amount">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Paid Amount</label>

                                <div class="col-md-5">
                                    <input type="number" class="form-control" v-model="purchase.received_amount" v-on:keyup="calcAmount()">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Discount Amount</label>

                                <div class="col-md-5">
                                    <input type="number" class="form-control" v-model="purchase.discount_amount" v-on:keyup="calcAmount()">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Net Amount</label>

                                <div class="col-md-5">
                                    <input type="number" class="form-control" disabled="true" v-model="purchase.net_amount">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-5 col-md-offset-2">
                                    <button type="button" class="btn btn-primary" v-on:click="savePurchase()">Save</button>    
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="panel-footer">
                        <button type="button" class="btn btn-sm btn-default" @click="backToList()">Close</button>
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
				suppliers: [],
                products: [],
                sales: [],
                purchase: {
                    id: 0,
                    errors: [],
                    products: [],
                    total_amount: 0,
                    received_amount: 0,
                    discount_amount: 0,
                    net_amount: 0,
                    supplier_id: '',
                    purchased_date: ''
                },
                product: {
                    id: 0,
                    errors: []
                }
			};
		},

        mounted() {
            this.prepareComponent();
        },

        methods: {
        	/**
        	 * Get All Suppliers
        	 */
        	getSuppliers() {
        		this.$http.get('/supplier/0').then(response => {
        			this.suppliers = response.data;
        		});
        	},

            /**
             * Get All Purchases
             */
            getSales() {
                this.$http.get('/sale/0').then(response => {
                    this.sales = response.data; 
                });
            },

            /**
             * Calc Amount
             */
            calcAmount() {
                this.calc_amount;
            },

            /**
             * Get Purchase Date
             */
            getDate() {
                var temp = $('.purchased_date').val();
                console.log('Data ' + temp);
            },

        	/**
        	 * Show Popup Modal
        	 */
        	showForm(supplier) {        		
    			if (supplier == null) {
        			this.supplier = { id: 0, name: '', is_active: 0, errors: [] };
        		} else {
        			this.supplier = supplier;
        			this.supplier.errors = [];
        		}

        		$('#modal_form').modal('show');
        	},

        	/**
        	 * Prepare the component
        	 */
        	prepareComponent() {

                $('.purchase_form').hide();
                this.getSales();

        		$('#modal_form').on('shown.bs.modal', () => {
        			$('#supplier_name').focus();
        		});

                $('#product_form').on('shown.bs.modal', () => {
                    $('#product_id').focus();
                });
        	},

            /**
             * Show New Panel
             * 
             * @return mixed
             */
            showCreateOrUpdateForm(purchase) {
                var vm = this;

                this.$http.get('supplier/1').then(response => {
                    this.suppliers = response.data;

                    if (purchase == null) {
                        this.purchase = {
                            id: 0,
                            errors: [],
                            products: [],
                            total_amount: 0,
                            received_amount: 0,
                            discount_amount: 0,
                            net_amount: 0,
                            supplier_id: '',
                            purchased_date: ''
                        };
                    }

                    $('.purchase_list').fadeOut('', function () {
                        $('.purchase_form').fadeIn();
                        $('#supplier_id').focus();

                        $('.purchased_date').datepicker({
                            format: 'yyyy-mm-dd',
                            todayHighlight: true,
                            autoclose: true
                        }).on('changeDate', function (e) {
                            vm.purchase.purchased_date = e.delegateTarget.value;
                        });
                    });    
                });
            },

            /**
             * Show Purchase List
             * 
             * @return mixed
             */
            backToList() {
                $('.purchase_form').fadeOut('', function () {
                    $('.purchase_list').fadeIn();
                });
            },

            /**
             * Add Product Modal
             */
            showProductForm() {
                this.product = { id: 0, quantity: 0, price: 0, errors: [] };

                this.$http.get('product/1').then(response => {
                    this.products = response.data;
                    $('#product_form').modal('show');
                });
            },

            /**
             * Show Purchase Detail
             */
            showDetail(purchase) {
                this.purchase = purchase;
                this.purchase.errors = [];

                $('#detail_modal').modal('show');
            },

            /**
             * Add Products to Purchase
             */
            addProduct() {
                this.product.errors = [];

                if (this.product.id === '' || this.product.id <= 0) {
                    this.product.errors.push('Product is required.');
                    $('#product_id').focus();
                } else if (this.product.quantity <= 0 || this.product.quantity == '') {
                    this.product.errors.push('Quantity must be more than 0.');
                    $('#product_quantity').focus();
                } else {
                    this.purchase.products.push(this.product);
                    this.calc_amount;
                    $('#product_form').modal('hide');
                }
            },

            /**
             * Remove Product
             */
            removeProduct(product) {
                var index = this.purchase.products.indexOf(product);

                if (index >= 0) {
                    this.purchase.products.splice(index, 1);
                }
            },

            /**
             * Save the Purchase
             */
            savePurchase() {
                this.purchase.errors = [];

                if (this.purchase.supplier_id == '') {
                    this.purchase.errors.push('Supplier is required.');
                    $('#supplier_id').focus();

                } else if (this.purchase.purchased_date == '') {
                    this.purchase.errors.push('Purchase Date is required.');
                    $('.purchased_date').focus();

                } else if (this.purchase.products.length == 0) {
                    this.purchase.errors.push('At least One Product needed.');

                } else {
                    this.persistClient('post', '/purchase', this.purchase, '#purchase_form');
                }
            },

        	/**
        	 * Update means create or update
        	 */
        	update() {
        		if (this.supplier.id > 0) {
        			this.persistClient('put', '/supplier/' + this.supplier.id, this.supplier, '#modal_form');
        		} else {
        			this.persistClient('post', '/supplier', this.supplier, '#modal_form');
        		}
        	},

        	 /**
             * Persist the client to storage using the given form.
             */
            persistClient(method, uri, form, modal) {
                form.errors = [];

                this.$http[method](uri, form)
                    .then(response => {
                        this.getPurchases();
                    	this.backToList();

                        form.errors = [];

                        $(modal).modal('hide');
                    })
                    .catch(response => {
                        if (typeof response.data === 'object') {
                            form.errors = _.flatten(_.toArray(response.data));
                        } else {
                            form.errors = ['Something went wrong. Please try again.'];
                        }
                        $('#supplier_name').focus();
                    });
            },

            /**
             * Get the Product name
             */
            getProductName(product_id) {
                var vm = this;
                this.products.find(function (obj) {
                    if (obj.id == product_id) {
                        vm.product.name = obj.name;
                    }
                });
            },

            /**
             * Custom Date Format
             */
            getCustomDateFormat(date) {
                return moment(date, 'YYYY-MM-DD HH:mm:ss').format('DD-MM-YYYY');
            },

            destroy(purchase) {
            	var vm = this;

            	swal({
            		title: 'Are you sure?',
            		type: 'warning',
            		showCancelButton: true,
            		confirmButtonText: 'Yes, delete it!'
            	}).then(function () {
            		vm.$http.delete('/purchase/' + purchase.id).then(response => {
            			vm.getPurchases();
            		});
            	}, function () {});
            }
        },

        computed: {
            calc_amount() {
                var vm = this;
                this.purchase.total_amount = 0;

                this.purchase.products.forEach(function (obj) {
                    vm.purchase.total_amount += (obj.price * obj.quantity);
                });

                this.purchase.received_amount == '' ? this.purchase.received_amount = 0 : '';
                this.purchase.discount_amount == '' ? this.purchase.discount_amount = 0 : '';

                this.purchase.net_amount = parseInt(this.purchase.total_amount) - (parseInt(this.purchase.received_amount) + parseInt(this.purchase.discount_amount));
            }
        }
    }
</script>