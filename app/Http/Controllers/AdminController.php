<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
class AdminController extends Controller {
 public const STATUSES=['Recebido','Em preparo','Pronto para retirada','Concluído','Cancelado'];
 public function login(Request $r) {
  $data=$r->validate(['email'=>'required|email','password'=>'required|string']);
  if(!Auth::attempt([...$data,'is_admin'=>true])) return back()->withErrors(['email'=>'E-mail ou senha incorretos.'])->onlyInput('email');
  $r->session()->regenerate(); return redirect('/admin');
 }
 public function logout(Request $r) {
  Auth::logout(); $r->session()->invalidate(); $r->session()->regenerateToken(); return redirect('/');
 }
 public function dashboard() {
  $orders=DB::table('orders')->latest()->paginate(20);
  $items=DB::table('order_items')->whereIn('order_id',$orders->pluck('id'))->get()->groupBy('order_id');
  $statuses=self::STATUSES; return view('shop.admin',compact('orders','items','statuses'));
 }
 public function status(Request $r,int $id) {
  $data=$r->validate(['status'=>['required',Rule::in(self::STATUSES)]]);
  abort_unless(DB::table('orders')->where('id',$id)->exists(),404);
  DB::table('orders')->where('id',$id)->update([...$data,'updated_at'=>now()]); return back()->with('success','Status atualizado.');
 }
 public function products() {
  return view('shop.products',['products'=>DB::table('products')->orderBy('name')->get()]);
 }
 public function save(Request $r, ?int $id=null) {
  if($id) abort_unless(DB::table('products')->where('id',$id)->exists(),404);
  $data=$r->validate(['name'=>'required|string|max:100','category'=>'required|string|max:60','description'=>'nullable|string|max:500','price'=>['required','regex:/^\d{1,4}([,.]\d{1,2})?$/'],'image'=>'nullable|image|mimes:jpg,jpeg,png,webp|max:2048','active'=>'nullable|boolean']);
  $parts=explode('.',str_replace(',','.',$data['price']));
  $cents=(int)$parts[0]*100+(int)str_pad($parts[1]??'',2,'0');
  if($cents<1) return back()->withErrors(['price'=>'Informe um preço maior que zero.'])->withInput();
  unset($data['price'],$data['image']); $data['price_cents']=$cents; $data['active']=$r->boolean('active'); $data['updated_at']=now();
  if($r->hasFile('image')) $data['image']=$r->file('image')->store('products','public');
  if($id) DB::table('products')->where('id',$id)->update($data);
  else DB::table('products')->insert([...$data,'created_at'=>now()]);
  return redirect('/admin/produtos')->with('success','Produto salvo.');
 }
}
