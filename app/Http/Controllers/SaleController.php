<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Sale;
use App\SaleDetail;
use App\Stock;
use Illuminate\Http\Request;

class SaleController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('sale.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'customer_id' => 'required',
            'car_id'      => 'required'
        ], [
            'customer_id.required' => 'Customer is required.',
            'car_id.required'      => 'Car is required.'
        ]);

        $sale                  = new Sale();
        $sale->customer_id     = $request->input('customer_id');
        $sale->car_id          = $request->input('car_id');
        $sale->voucher_no      = uniqid();
        $sale->sale_date       = $request->input('saled_date');
        $sale->total_amount    = $request->input('total_amount');
        $sale->received_amount = $request->input('received_amount');
        $sale->discount_amount = $request->input('discount_amount');
        $sale->net_amount      = $request->input('net_amount');
        $sale->save();

        foreach ($request->input('products') as $product) {

            $detail               = new SaleDetail();
            $detail->sale_id      = $sale->id;
            $detail->product_id   = $product['id'];
            $detail->quantity     = $product['quantity'];
            $detail->unique_price = $product['price'];
            $detail->total_amount = $product['price'] * $product['quantity'];
            $detail->save();

            // Update The Product Stock
            if ($stock = Stock::where('product_id', $product['id'])->first()) {
                $stock->sold += $product['quantity'];
                $stock->balance -= $product['quantity'];
                $stock->save();
            } else {
                $stock             = new Stock();
                $stock->product_id = $product['id'];
                $stock->balance    = (0 - $product['quantity']);
                $stock->sold       = $product['quantity'];
                $stock->save();
            }
        }

         // update the customer amount
        $customer = Customer::find($request->input('customer_id'));
        $customer->receivable_amount += $request->input('net_amount');
        $customer->save();

        return response()->json(['status' => true]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if ($id > 0) {

        }

        return Sale::with('car', 'customer', 'products.product')->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sale = Sale::find($id);

        foreach($sale->products as $detail) {
            $stock = Stock::where('product_id', $detail->product_id)->first();
            $stock->sold -= $detail->quantity;
            $stock->balance += $detail->quantity;
            $stock->save();

            $detail->delete();
        }

        $customer = Customer::find($sale->customer_id);
        $customer->receivable_amount -= $sale->net_amount;
        $customer->save();

        $sale->delete();

        return response()->json(['status' => true]);
    }
}
