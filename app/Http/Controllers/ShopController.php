<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
class ShopController extends Controller {
 public function index(Request $r) {
  $products=DB::table('products')->where('active',true)->orderBy('category')->orderBy('name')->get();
  return view('shop.catalog',compact('products'));
 }
 public function cart() {
  $cart=session('cart',[]);
  $products=DB::table('products')->whereIn('id',array_keys($cart))->get();
  $total=$products->sum(fn($p)=>$p->price_cents*($cart[$p->id]??0));
  return view('shop.cart',compact('products','cart','total'));
 }
 public function add(Request $r, int $id) {
  abort_unless(DB::table('products')->where('id',$id)->where('active',true)->exists(),404);
  $cart=$r->session()->get('cart',[]); $cart[$id]=min(20,($cart[$id]??0)+1);
  $r->session()->put('cart',$cart); return back()->with('success','Produto adicionado!');
 }
 public function update(Request $r, int $id) {
  $data=$r->validate(['quantity'=>'required|integer|min:0|max:20']);
  $cart=$r->session()->get('cart',[]);
  if($data['quantity']==0) unset($cart[$id]); else if(isset($cart[$id])) $cart[$id]=(int)$data['quantity'];
  $r->session()->put('cart',$cart); return back();
 }
 public function checkout(Request $r) {
  $data=$r->validate(['customer'=>'required|string|max:100','phone'=>['required','regex:/^[0-9 ()+\-]{10,25}$/'],'notes'=>'nullable|string|max:500']);
  $cart=$r->session()->get('cart',[]);
  if(!$cart) throw ValidationException::withMessages(['cart'=>'Adicione produtos ao carrinho.']);
  $token=(string)Str::uuid();
  DB::transaction(function() use($cart,$data,$token) {
   $products=DB::table('products')->whereIn('id',array_keys($cart))->lockForUpdate()->get();
   if($products->count()!==count($cart)||$products->contains(fn($p)=>!$p->active))
    throw ValidationException::withMessages(['cart'=>'Um produto está indisponível. Remova-o do carrinho.']);
   $total=$products->sum(fn($p)=>$p->price_cents*$cart[$p->id]);
   $id=DB::table('orders')->insertGetId([...$data,'token'=>$token,'total_cents'=>$total,'status'=>'Recebido','created_at'=>now(),'updated_at'=>now()]);
   foreach($products as $p) DB::table('order_items')->insert(['order_id'=>$id,'name'=>$p->name,'price_cents'=>$p->price_cents,'quantity'=>$cart[$p->id]]);
  });
  $r->session()->forget('cart'); return redirect('/pedido/'.$token);
 }
 public function order(string $token) {
  $order=DB::table('orders')->where('token',$token)->first(); abort_unless($order,404);
  $items=DB::table('order_items')->where('order_id',$order->id)->get();
  return response()->view('shop.order',compact('order','items'))->header('Cache-Control','private, no-store')->header('Referrer-Policy','no-referrer');
 }
}
