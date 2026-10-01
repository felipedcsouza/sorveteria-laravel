<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::table('users', fn(Blueprint $t) => $t->boolean('is_admin')->default(false));
  Schema::create('products', function(Blueprint $t) {
   $t->id(); $t->string('name'); $t->string('category'); $t->text('description')->nullable();
   $t->unsignedInteger('price_cents'); $t->string('image')->nullable(); $t->boolean('active')->default(true); $t->timestamps();
  });
  Schema::create('orders', function(Blueprint $t) {
   $t->id(); $t->uuid('token')->unique(); $t->string('customer'); $t->string('phone',30);
   $t->text('notes')->nullable(); $t->string('status')->default('Recebido'); $t->unsignedInteger('total_cents'); $t->timestamps();
  });
  Schema::create('order_items', function(Blueprint $t) {
   $t->id(); $t->foreignId('order_id')->constrained()->cascadeOnDelete();
   $t->string('name'); $t->unsignedInteger('price_cents'); $t->unsignedInteger('quantity');
  });
 }
 public function down(): void {
  Schema::dropIfExists('order_items'); Schema::dropIfExists('orders'); Schema::dropIfExists('products');
  Schema::table('users', fn(Blueprint $t) => $t->dropColumn('is_admin'));
 }
};
