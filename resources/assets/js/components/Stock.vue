<template>
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Stock Management</div>

                    <div class="panel-body">
                        <table class="table table-striped m-b-none">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Balance</th>
                                    <th>Purchased</th>
                                    <th>Sold</th>
                                    <th>Demange</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="stock in stocks">
                                    <td>{{ stock.product.name }}</td>
                                    <td>{{ stock.balance }}</td>
                                    <td>{{ stock.purchased }}</td>
                                    <td>{{ stock.sold }}</td>
                                    <td>{{ stock.demage }}</td>
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
                stocks: []
            }
        },

        mounted() {
            this.getStockData();
        },

        methods: {
            getStockData() {
                this.$http.get('stock/data').then(response => {
                    this.stocks = response.data;
                });
            }
        }
    }
</script>
