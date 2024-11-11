<?php
namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{/**
 * @OA\Get(
 *     path="/api/products",
 *     tags={"Products"},
 *     summary="Get list of products",
 *     description="Returns list of products",
 *     @OA\Response(response=200, description="Successful operation"),
 * )
 */
    public function index()
    {
        return Order::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'total_price' => 'required|numeric',
        ]);

        return Order::create($request->all());
    }


    public function show($id)
    {
        return Order::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update($request->all());
        return $order;
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return response()->json(null, 204);
    }
}
