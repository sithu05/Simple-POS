<?php

namespace App\Http\Controllers;

use App\Purchase;
use App\PurchaseDetail;
use App\Stock;
use App\Supplier;
use Illuminate\Http\Request;

class PurchaseController extends Controller
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
        return view('purchase.index');
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
        $purchase                  = new Purchase();
        $purchase->supplier_id     = $request->input('supplier_id');
        $purchase->voucher_no      = uniqid();
        $purchase->purchased_date  = $request->input('purchased_date');
        $purchase->total_amount    = $request->input('total_amount');
        $purchase->received_amount = $request->input('received_amount');
        $purchase->discount_amount = $request->input('discount_amount');
        $purchase->net_amount      = $request->input('net_amount');
        $purchase->save();

        foreach ($request->input('products') as $product) {
            $detail               = new PurchaseDetail();
            $detail->purchase_id  = $purchase->id;
            $detail->product_id   = $product['id'];
            $detail->quantity     = $product['quantity'];
            $detail->unique_price = $product['price'];
            $detail->total_amount = $product['price'] * $product['quantity'];
            $detail->save();

            // Update The Product Stock
            if ($stock = Stock::where('product_id', $product['id'])->first()) {
                $stock->purchased += $product['quantity'];
                $stock->balance += $product['quantity'];
                $stock->save();
            } else {
                $stock             = new Stock();
                $stock->product_id = $product['id'];
                $stock->balance    = $product['quantity'];
                $stock->purchased  = $product['quantity'];
                $stock->save();
            }
        }

        // update the supplier amount
        $supplier = Supplier::find($request->input('supplier_id'));
        $supplier->payable_amount += $request->input('net_amount');
        $supplier->save();

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

        return Purchase::with('products.product')->get();
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
        //
    }
}
