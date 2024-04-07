<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Order;

class OrderController extends Controller
{


    public function index()
    {
        $orders = Order::orderBy('created_at', 'desc')->get();

        return view('orders', compact('orders'));
    }


    public function store(Request $request)
    {
        try {

            $rules = [
                'phone' => 'required',
                'payment_method' => 'required|string',
                'products' => 'required|array',
                'products.*.name' => 'required|string',
                'products.*.quantity' => 'required|integer|min:1',
                'products.*.price' => 'required|numeric|min:0',
                'user.first_name' => 'required|string',
            ];

            if ($request->has('email')) {
                $rules['email'] = 'email';
            }

            if ($request->has('products.*.image')) {
                $rules['products.*.image'] = 'string';
            }

            if ($request->input('payment_method') === 'card') {
                // $rules['user.first_name'] = 'required|string';
                $rules['user.surname'] = 'required|string';
                $rules['user.last_name'] = 'required|string';
            }


            $request->validate($rules);

            $order = new Order();
            // $order->email = $request->email;
            if ($request->has('email')) {
                $order->email = $request->email;
            }else{
                $order->email = '';
            }
            $order->phone = $request->phone;
            $order->created_at = now();


            $productsData = [];
            foreach ($request->input('products') as $product) {
                $productData = [
                    'name' => $product['name'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price']
                ];

                if (isset($product['image'])) {
                    $productData['image'] = $product['image'];
                }

                $productsData[] = $productData;
            }

            $orderData = [
                'user' => [
                    'first_name' => $request->input('user.first_name'),
                    'surname' => $request->input('user.surname'),
                    'last_name' => $request->input('user.last_name'),
                ],
                'payment_method' => $request->input('payment_method'),
                'products' => $productsData
            ];


            $order->data = json_encode($orderData);
            $order->save();


            return response()->json(['message' => 'Order created successfully'], 201);
        } catch (ValidationException $e) {

            return response()->json(['message' => $e->validator->errors()], 422);
        }
    }



    public function update(Request $request, Order $order)
    {

        $validatedData = $request->validate([
            'status' => 'required|in:pending,processing,completed', // Проверка, что статус находится в допустимом списке
        ]);

        $order->status = $validatedData['status'];
        $order->save();

        return response()->json(['message' => 'Order status updated successfully'], 200);
    }
}
