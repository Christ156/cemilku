<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Collection;
use App\Models\Customize;
use App\Models\MysteryBox;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index($id_user, $slug)
    {
        $id_user = Auth::user()->id;
        $id_cart = Cart::where('user_id', $id_user)->where('is_active', 1);

        if($id_cart->count() == 0){
            Cart::insert(['user_id' => $id_user, 'is_active' => 1, 'created_at' => now()]);
        }

        $id_cart = $id_cart->first()->id;

        $carts = CartItem::where('cart_id', $id_cart)->get();
        $address_active = Address::where('user_id', $id_user)->where('is_primary', 1)->first();
        $count_address_active = Address::where('user_id', $id_user)->where('is_primary', 1)->count();
        $address = Address::where('user_id', $id_user)->where('is_primary', 0)->get();

        return view('cart', \compact(['carts', 'address_active', 'count_address_active', 'address']));
    }

    private function create_new_order($id_user, $total_price, $payment_method)
    {
        $address_id = Address::where('user_id', $id_user)->where('is_primary', 1)->first()->id;

        $order = new Order();
        $order->user_id = $id_user;
        $order->total_price = $total_price;
        $order->payment_method = $payment_method;
        $order->address_id = $address_id;
        $order->created_at = now();
        $order->save();
        return $order->id;
    }

    public function checkout(Request $request, $id_user, $slug)
    {
        $id_cart = Cart::where('user_id', $id_user)->where('is_active', 1)->first()->id;
        $cart_items = CartItem::where('cart_id', $id_cart);

        $id_order = $this->create_new_order($id_user, $request->input('total_price'), $request->input('payment_method'));

        $cart_items = $cart_items->get();
        for ($i = 0; $i < $cart_items->count(); $i++) {
            if ($request->input('item_cart_' . $cart_items[$i]->id) == "true") {
                $order_detail = new OrderDetail();
                $order_detail->order_id = $id_order;
                $order_detail->collection_id = $cart_items[$i]->collection_id;
                $order_detail->customize_id = $cart_items[$i]->customize_id;
                $order_detail->mysterybox_id = $cart_items[$i]->mysterybox_id;
                $order_detail->quantity = $cart_items[$i]->quantity;
                $order_detail->price = $cart_items[$i]->total_price;
                $order_detail->created_at = now();
                $order_detail->save();

                if($cart_items[$i]->collection_id != NULL){
                    $id = $cart_items[$i]->collection_id;
                    $coll = Collection::findOrFail($id);
                    $coll->stock = $coll->stock - $cart_items[$i]->quantity;
                    $coll->save();
                }else if($cart_items[$i]->customize_id != NULL){
                    $id = $cart_items[$i]->customize_id;
                    // Customize::findOrFail($id)->delete();
                }

                $cart_items[$i]->delete();
            }
        }

        return \redirect()->route('checkout.index', ['order_id'=>$id_order]);
    }

    public function destroy(Request $request, $id_user, $slug, $count_items)
    {
        $id_cart = Cart::where('user_id', $id_user)->where('is_active', 1)->first()->id;
        $carts = CartItem::where('cart_id', $id_cart)->get();

        for ($i = 0; $i < $count_items; $i++) {
            if ($request->input('cart_item_' . $carts[$i]->id)) {
                $delete_selected = CartItem::findOrFail($carts[$i]->id);
                $delete_selected->delete();
            }
        }

        return \redirect()->route('cart.index', ['id_user' => $id_user, 'slug' => Str::slug(Auth::user()->name)]);
    }

    private function checkPrimaryAddress($id_user){
        $count = Address::where('user_id', $id_user)->where('is_primary', 1)->count();
        $exist = false;

        if($count > 0){
            $exist = true;
        }

        return $exist;
    }

    public function store_address(Request $request, $id_user, $slug){
        $address = new Address();
        $address->user_id = $id_user;
        $address->receiver_name = $request->input('receiver_name');
        $address->phone_number = $request->input('receiver_phone');
        $address->label = $request->input('label_address');
        $address->address = $request->input('address');
        $address->rt = $request->input('rt');
        $address->rw = $request->input('rw');
        $address->kelurahan_desa = $request->input('kelurahan');
        $address->kecamatan = $request->input('kecamatan');
        $address->kota_kabupaten = $request->input('kabupaten');
        $address->provinsi = $request->input('province');
        $address->kode_pos = $request->input('pos_code');
        if($this->checkPrimaryAddress($id_user)){
            $address->is_primary = 0;
        }else{
            $address->is_primary = 1;
        }
        $address->created_at = now();
        $address->save();

        return \redirect()->route('cart.index', ['id_user' => $id_user, 'slug' => $slug]);
    }

    public function set_primary_address(Request $request, $id_user, $slug){
        $old_primary = Address::where('user_id', $id_user)->where('is_primary', 1)->first();
        $old_primary->is_primary = 0;
        $old_primary->save();

        $id_new_primary = $request->input('set-primary-address');
        $new_primary = Address::find($id_new_primary);
        $new_primary->is_primary = 1;
        $new_primary->save();

        return \redirect()->route('cart.index', ['id_user' => $id_user, 'slug' => $slug]);
    }
}
