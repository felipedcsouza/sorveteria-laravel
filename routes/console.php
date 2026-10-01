<?php
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
Artisan::command('loja:admin',function(){
 $name=$this->ask('Nome do administrador'); $email=$this->ask('E-mail'); $password=$this->secret('Senha (mínimo de 12 caracteres)');
 $validator=Validator::make(compact('name','email','password'),['name'=>'required|string|max:100','email'=>'required|email|unique:users,email','password'=>'required|string|min:12']);
 if($validator->fails()){ foreach($validator->errors()->all() as $error) $this->error($error); return 1; }
 DB::table('users')->insert(['name'=>$name,'email'=>$email,'password'=>Hash::make($password),'is_admin'=>true,'created_at'=>now(),'updated_at'=>now()]);
 $this->info('Administrador criado. Acesse /admin/login.');
})->purpose('Criar administrador sem senha padrão');
