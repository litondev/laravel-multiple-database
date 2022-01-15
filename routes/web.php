<?php

use Illuminate\Support\Facades\Route;
use App\Models\{
    User,
    Product
};
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get("/user",function(){
    return response()->json(User::with("products")->get());
});

Route::get("/user/create",function(){
    User::create([
        "name" => "test".rand(0,100000)
    ]);
});

Route::get("/product",function(){
    return response()->json(Product::with("user")->get());
});

Route::get("/product/join",function(){
    return response()->json(
        \DB::table("multiple_product.products")
        ->join("multiple_user.users","multiple_product.products.user_id","=","multiple_user.users.id")
        ->select("multiple_user.users.name as user_name","multiple_product.products.name as product_name")
        ->get()
    );
});

Route::get("/product/create",function(){
    /*
     => Using With Simple Create
    Product::create([
        "user_id" => 1,
        "name" => "test".rand(0,10000)
    ]);
    */

    /*
     => Using With User

    $user = User::findOrFail(1);

    $user->products()->create([
        "name" => "hello"
    ]);
    */

    // Using With Transaction
    
    \DB::connection("db_user")->beginTransaction();
    \DB::connection("db_product")->beginTransaction();

    $user = User::create([
        "name" => "testing"
    ]);

    $user->products()->create([
        "name" => "hellos"
    ]);

    // \DB::connection("db_user")->rollBack();
    // \DB::connection("db_product")->rollBack();

    \DB::connection("db_user")->commit();
    \DB::connection("db_product")->commit();
});
