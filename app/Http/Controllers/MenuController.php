<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $tableNumber = $request->query('meja');
        if ($tableNumber) {
            Session::put('table_number', $tableNumber);
        }

        $items = Item::where('is_active', 1)->orderBy('name', 'asc')->get();
        return view('customer.menu', compact('items', 'tableNumber'));
    }

    //cart
    public function cart()
    {
        $cart = Session::get('cart');
        return view('customer.cart', compact('cart'));
    }

    public function addToCart(Request $request)
    {
        $menuId = $request->input('id');
        $menu = Item::find($menuId);

        if (!$menu) {
            return response()->json(['status' => 'Error', 'message' => 'Menu tidak ditemukan.']);
        }

        $cart = Session::get('cart', []);

        if (isset($cart[$menuId])) {
            $cart[$menuId]['qty'] += 1;
        } else {
            $cart[$menuId] = [
                'id' => $menu->id,
                'name' => $menu->name,
                'price' => $menu->price,
                'image' => 'img_item_upload/' . $menu->img,
                'qty' => 1
            ];
        }

        Session::put('cart', $cart);

        return response()->json(['status' => 'Success.', 'message' => 'Berhasil ditambahkan ke keranjang.']);
    }

    public function updateCart(Request $request)
    {
        $itemId = $request->input('id');
        $newQty = $request->input('qty');

        if ($newQty <= 0) {
            return response()->json(['success' => false]);
        }

        $cart = Session::get('cart', []);
        if (isset($cart[$itemId])) {
            $cart[$itemId]['qty'] = $newQty;
            Session::put('cart', $cart);
            Session::flash('success', 'Jumlah item berhasil diperbarui.');
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }

    public function removeCart(Request $request) {
        $itemId = $request->input('id');

        $cart = Session::get('cart', []);

        if (isset($cart[$itemId])) {
            unset($cart[$itemId]);
            Session::put('cart', $cart);
            Session::flash('success', 'Item berhasil dihapus dari keranjang.');
            return response()->json(['success' => true]);
        }
    }

    public function clearCart() {
        Session::forget('cart');
        return redirect()->route('cart')->with('success', 'Keranjang berhasil dikosongkan.');
    }

    //checkout
    public function checkout()
    {
        $cart = Session::get('cart');
        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Keranjang kosong. Silakan tambahkan item sebelum checkout.');
        }

        $tableNumber = Session::get('table_number');

        return view('customer.checkout', compact('cart', 'tableNumber'));
    }

    public function storeOrder(Request $request)
    {
        $cart = Session::get('cart');
        $tableNumber = Session::get('table_number');

        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Keranjang kosong. Silakan tambahkan item sebelum checkout.');
        }

        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->route('checkout')->withErrors($validator)->withInput();
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['qty'];
        }

        $totalAmount = 0;
        foreach ($cart as $item) {
            $totalAmount += $item['price'] * $item['qty'];

            $itemDetails[] = [
                'id' => $item['id'],
                'name' => substr($item['name'], 0, 50),
                'price' => (int) $item['price'] + ($item['price'] * 0.1),
                'quantity' => $item['qty'],
            ];
        }

        $user = User::firstOrCreate([
            'full_name' => $request->input('fullname'),
            'phone' => $request->input('phone'),
            'role_id' => 4 ]
        );

        $order = Order::create([
            'order_code' => 'ORD-' . $tableNumber . '-' . time(),
            'user_id' => $user->id,
            'subtotal' => $totalAmount,
            'tax' => $totalAmount * 0.1,
            'grand_total' => $totalAmount + ($totalAmount * 0.1),
            'status' => 'pending',
            'table_number' => $tableNumber,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes ?? null
        ]);

        foreach($cart as $ItemId => $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'item_id' => $item['id'],
                'quantity' => $item['qty'],
                'price' => $item['price'] * $item['qty'],
                'tax' => 0.1 * $item['price'] * $item['qty'],
                'total_price' => $item['price'] * $item['qty'] + (0.1 * $item['price'] * $item['qty']),
            ]);
        }

        Session::forget('cart');
        Session::flash('success', 'Pesanan berhasil dibuat.');
        return redirect()->route('checkout.success', ['orderId' => $order->order_code])->with('success', 'Pesanan berhasil dibuat.');
    }

    public function checkoutSuccess($orderId)
    {
        $order = Order::where('order_code', $orderId)->first();
        if (!$order) {
            return redirect()->route('menu')->with('error', 'Pesanan tidak ditemukan.');
        }
        $orderItems = OrderItem::where('order_id', $order->id)->get();

        if ($order->payment_method == 'qris'){
            $order->status = 'settlement';
            $order->save();
        }

        return view('customer.success', compact('order', 'orderItems'));
        }
}
