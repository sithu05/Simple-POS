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
        <div class="row sale_list">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Sale Management
                        <button class="btn btn-primary btn-sm pull-right" @click="createNew()">Add New</button>
                    </div>
                    
                    <div class="panel-body">
                        <h4>Sale Management</h4> <hr/>

                        <table class="table table-striped m-b-none">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Voucher No</th>
                                    <th>Sold Date</th>
                                    <th>Total Amount</th>
                                    <th>Discount</th>
                                    <th>Paid Amount</th>
                                    <th>Net Amount</th>
                                    <th>Actions</th>
                                </tr>   
                            </thead>
                            
                            <tbody>
                                <tr v-for="sale in sales">
                                    <td>{{ sale.id }}</td>
                                    <td>{{ sale.voucher_no }}</td>
                                    <td>{{ getCustomDateFormat(sale.sale_date) }}</td>
                                    <td>{{ sale.total_amount }}</td>
                                    <td>{{ sale.discount_amount }}</td>
                                    <td>{{ sale.received_amount }}</td>
                                    <td>{{ sale.net_amount }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info action-link" @click="showDetailProducts(sale)">Detail</button>
                                        <button class="btn btn-sm btn-danger action-link" @click="destroy(sale)">Del</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div> <!-- /.panel -->
            </div> <!-- /.col-md-12 -->
        </div> <!-- /.row -->

        <div class="row sale_form">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Create New Sale
                    </div>

                    <div class="panel-body">
                        <div class="alert alert-danger" v-if="sale.errors.length > 0">
                            <p><strong>Whoops!</strong> Something went wrong!</p>
                            <br>
                            <ul>
                                <li v-for="error in sale.errors">
                                    {{ error }}
                                </li>
                            </ul>
                        </div>
                        
                        <form class="form-horizontal" role="form">

                            <div class="form-group">
                                <label class="col-md-2 control-label">Customer</label>

                                <div class="col-md-5">
                                    <select class="form-control" id="customer_id" v-model="sale.customer_id">
                                        <option>Please Select</option>
                                        <option v-for="customer in customers" v-bind:value="customer.id">{{ customer.name }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Car</label>

                                <div class="col-md-5">
                                    <select class="form-control" id="car_id" v-model="sale.car_id">
                                        <option>Please Select</option>
                                        <option v-for="car in cars" v-bind:value="car.id">{{ car.car_no }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Sale Date</label>

                                <div class="col-md-5">
                                    <input type="text" class="form-control sale_date" v-model="sale.sale_date">
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
                                            <tr v-for="product in sale.products">
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
                                    <input type="text" class="form-control" disabled="true" v-model="sale.total_amount">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Paid Amount</label>

                                <div class="col-md-5">
                                    <input type="number" class="form-control" v-model="sale.received_amount" v-on:keyup="calcAmount()">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Discount Amount</label>

                                <div class="col-md-5">
                                    <input type="number" class="form-control" v-model="sale.discount_amount" v-on:keyup="calcAmount()">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Net Amount</label>

                                <div class="col-md-5">
                                    <input type="number" class="form-control" disabled="true" v-model="sale.net_amount">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-5 col-md-offset-2">
                                    <button type="button" class="btn btn-primary" v-on:click="saveSale()">Save</button>    
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="panel-footer">
                        <button type="button" class="btn btn-sm btn-default" @click="backToList()">Close</button>
                    </div>
                </div>
            </div> <!-- /.col-md-12 -->
        </div> <!-- /.row -->

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

        <!-- Show Detail Modal -->
        <div class="modal fade" id="detail_modal" tabindex="-1" role="dialog" v-if="showDetail">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button " class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

                        <h4 class="modal-title">Sale Detail</h4>
                    </div>

                    <div class="modal-body">
                        <h5>Customer: {{ sale.customer.name }}</h5>
                        <h5>Car: {{ sale.car.car_no }}</h5>
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
                                <tr v-for="product in sale.products">
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
    </div>
</template>

<script>
    export default {
        data() {
            return {
                sales: [],
                customers: [],
                cars: [],
                sale: {
                    errors: [],
                    products: [],
                    customer: {
                        name: ''
                    },
                    car: {
                        car_no: ''
                    }
                },
                products: [],
                product: {
                    errors: []
                },
                showDetail: true,
            }
        },

        mounted() {
            this.prepareComponent();
        },

        methods: {
             /**
             * Get All Sales
             */
            getSales() {
                this.$http.get('/sale/0').then(response => {
                    this.sales = response.data; 
                });
            },

            /**
             * Prepare Component
             */
            prepareComponent() {
                this.getSales();

                $('.sale_form').hide();

                $('#product_form').on('shown.bs.modal', () => {
                    $('#product_id').focus();
                });
            },

            /**
             * Create New Sale Form
             */
            createNew() {
                var vm = this;

                this.showDetail = false;

                this.sale = {
                    errors: [],
                    products: [],
                    received_amount: 0,
                    total_amount: 0,
                    discount_amount: 0,
                    net_amount: 0,
                };

                this.$http.get('customer/1').then(response => {
                    this.customers = response.data;

                    this.$http.get('car/1').then(response => {
                        this.cars = response.data;

                        $('.sale_list').fadeOut('', function () {
                            $('.sale_form').fadeIn();
                            $('#customer_id').focus();

                            $('.sale_date').datepicker({
                                format: 'yyyy-mm-dd',
                                todayHighlight: true,
                                autoclose: true
                            }).on('changeDate', function (e) {
                                vm.sale.sale_date = e.delegateTarget.value;
                            });
                        });   
                    });
                });
            },

            /**
             * Back To Purchase List
             */
            backToList() {
                var vm = this;

                $('.sale_form').fadeOut('', function () {
                    $('.sale_list').fadeIn();
                    vm.showDetail = true;
                    vm.sale = {
                        errors: [],
                        products: [],
                        customer: {
                            name: ''
                        },
                        car: {
                            car_no: ''
                        }
                    };
                });
            },

            /**
             * Show Product Model
             */
            showProductForm() {
                this.product = {
                    quantity: 0,
                    price: 0,
                    id: '',
                    errors: []
                };

                this.$http.get('/product/1').then(response => {
                    this.products = response.data;

                    $('#product_form').modal('show');
                });
            },

            /**
             * Add Product To Purchase Products
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
                    this.sale.products.push(this.product);
                    this.calc_amount;
                    $('#product_form').modal('hide');
                }
            },

            /**
             * Update the Product Name
             */
            getProductName(id) {
                var vm = this;

                this.products.find(function (obj) {
                    if (obj.id == id) {
                        vm.product.name = obj.name;
                        vm.product.price = obj.price;
                    }
                });
            },

            /**
             * Save the Sale
             */
            saveSale() {
                this.sale.errors = [];

                if (this.sale.customer_id == '' || this.sale.customer_id == null) {
                    this.sale.errors.push('Customer is required.');
                    $('#customer_id').focus();

                } else if (this.sale.sale_date == '') {
                    this.sale.errors.push('Sale Date is required.');
                    $('.sale_date').focus();

                } else if (this.sale.products.length == 0) {
                    this.sale.errors.push('At least One Product needed.');

                } else {
                    this.persistClient('post', '/sale', this.sale, '#sale_form');
                }
            },

            /**
             * Persist the client to storage using the given form.
             */
            persistClient(method, uri, form, modal) {
                form.errors = [];

                this.$http[method](uri, form)
                    .then(response => {
                        this.getSales();
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
             * Calc Amount
             */
            calcAmount() {
                this.calc_amount;
            },

             /**
             * Custom Date Format
             */
            getCustomDateFormat(date) {
                return moment(date, 'YYYY-MM-DD HH:mm:ss').format('DD-MM-YYYY');
            },

             /**
             * Show Sale Detail
             */
            showDetailProducts(sale) {
                this.sale = sale;
                this.sale.errors = [];

                $('#detail_modal').modal('show');
            },

            destroy(sale) {
                var vm = this;

                swal({
                    title: 'Are you sure?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!'
                }).then(function () {
                    vm.$http.delete('/sale/' + sale.id).then(response => {
                        vm.getSales();
                    });
                }, function () {});
            }
        },

        computed: {
            calc_amount() {
                var vm = this;
                this.sale.total_amount = 0;

                if (this.sale.products.length > 0 || this.sale.products != null) {
                    this.sale.products.forEach(function (obj) {
                        vm.sale.total_amount += (obj.price * obj.quantity);
                    });    
                }
                
                this.sale.received_amount == '' ? this.sale.received_amount = 0 : '';
                this.sale.discount_amount == '' ? this.sale.discount_amount = 0 : '';

                this.sale.net_amount = parseInt(this.sale.total_amount) - (parseInt(this.sale.received_amount) + parseInt(this.sale.discount_amount));
            }
        }  
    }
</script>