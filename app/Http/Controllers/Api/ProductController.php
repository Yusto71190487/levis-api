<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(5);

        return new ProductResource(true, 'List Data Product', $products);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),  [
            'gambar'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'nama'     => 'required',
            'harga'   => 'required',
            'detail' => 'required',
            'kategori' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // upload image
        $image = $request->file('gambar');
        $image->storeAs('public/products', $image->hashName());

        // create product
        $product = Product::create([
            'gambar' => $image->hashName(),
            'nama' => $request->nama,
            'harga' => $request->harga,
            'detail' => $request->detail,
            'kategori' => $request->kategori
        ]);

        return new ProductResource(true , 'Data Product Berhasil ditambahkan!!', $product);
    }

    public function show($id)
    {
        $product = Product::find($id);

        return new ProductResource(true , 'Detail Data Product!', $product);
    }

    public function update(Request $request , $id)
    {
          $validator = Validator::make($request->all(),  [
            'gambar'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'nama'     => 'required',
            'harga'   => 'required',
            'detail' => 'required',
            'kategori' => 'required'
        ]);

         if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $product = Product::find($id);

        if ($request->hasFile('gambar')) {

            //upload image
            $image = $request->file('gambar');
            $image->storeAs('public/products', $image->hashName());

            //delete old image
            Storage::delete('public/products/'.basename($product->gambar));

            //update post with new image
            $product->update([
                'gambar'     => $image->hashName(),
                'nama'     => $request->nama,
                'harga'   => $request->harga,
                'detail' => $request->detail,
                'kategori' => $request->kategori
            ]);

        } else {

            //update post without image
            $product->update([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'detail' => $request->detail,
            'kategori' => $request->kategori
            ]);
        }

        //return response
        return new ProductResource(true, 'Data Product Berhasil Diubah!', $product);
    }

    public function destroy($id)
    {

        //find post by ID
        $product = Product::find($id);

        //delete image
        Storage::delete('public/products/'.basename($product->gambar));

        //delete post
        $product->delete();

        //return response
        return new ProductResource(true, 'Data Product Berhasil Dihapus!', null);
    }
}
