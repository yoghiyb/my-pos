<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    /**
     * Menampilakn data produk
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            //ambil data produk dari table products dan panggil relasi kategori
            $products = Product::with('category')->orderBy('created_at', 'desc')->paginate(10);

            return response()->json(['status' => 'success', 'data' => $products]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e]);
        }
    }

    /**
     * Menampilkan list produk
     *
     * @return @return \Illuminate\Http\Response
     */
    public function products()
    {
        try {
            $products = Product::orderBy('created_at', 'desc')->get();

            return response()->json(['status' => 'success', 'data' => $products]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e]);
        }
    }

    /**
     * Menyimpan produk baru
     *
     * @request \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validasi request
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:10|unique:products',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:100',
            'stock' => 'required|integer',
            'price' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png'
        ]);

        // jika validasi gagal kirim pesan invalid
        if ($validator->fails()) {
            return response()->json(['status' => 'invalid', 'message' => $validator->errors()]);
        }

        try {
            // default photo adalah null
            $photo = null;
            $product = $request->all();
            // jika ada file foto/gambar yang dikirim
            if ($request->hasFile('photo')) {
                // maka simpan dan jalankan method saveFile
                $photo = $this->saveFile($request->name, $request->file('photo'));
                $product['photo'] = $photo;
            }
            //simpan data kedalam tabel products
            Product::create($product);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Fungsi untuk menyimpan file foto/gambar
     *
     * @param string $name
     * @param \Illuminate\Support\Facades\File $photo
     */
    private function saveFile($name, $photo)
    {
        // set nama file gabungan dari nama produk dan time().
        $image = Str::slug($name) . time() . '.' . $photo->getClientOriginalExtension();
        // set path untuk menyimpan file gambar
        $path = public_path('uploads/product');
        //cek jika uploads/product bukan directory / folder
        if (!File::isDirectory($path)) {
            // maka buat folder tersebut
            File::makeDirectory($path, 0777, true, true);
        }
        // simpan gambar yang diupload ke folder uploads/product
        Image::make($photo)->save($path . '/' . $image);
        // mengembalikan nama file
        return $image;
    }

    /**
     * Menampilakn produk berdasarkan id
     *
     * @param number id
     * @return
     */
    public function show($id)
    {
        try {
            $product = Product::findOrFail($id);

            return response()->json(['status' => 'success', 'data' => $product], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Menampilkan data yang akan di update berdasarkan id
     *
     * @param number $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            // ambil data produk berdasarkan id
            $product = Product::findOrFail($id);

            return response()->json(['status' => 'success', 'data' => $product]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Update produk berdasarkan id produk
     *
     * @param \Illuminate\Http\Request $request
     * @param number $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // validasi request
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:10|exists:products,code',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:100',
            'stock' => 'required|integer',
            'price' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'invalid', 'message' => $validator->errors()]);
        }

        try {
            $productUpdate = $request->all();
            // query select berdasarkan id
            $product = Product::findOrFail($id);
            $photo = $product->photo;

            // cek jika ada file foto/gambar yang dikirim
            if ($request->hasFile('photo')) {
                // cek, jika photo tidak kosong maka file yang ada di folder uploads/product akan dihapus
                !empty($photo) ? File::delete(public_path('uploads/product/' . $photo)) : null;
                // upload file dengan method saveFile
                $photo = $this->saveFile($request->name, $request->file('photo'));
            }

            $productUpdate['photo'] = $photo;

            $product->update($productUpdate);

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Menghapus data produk berdasarkan id
     *
     * @param $id
     */
    public function destroy($id)
    {
        try {
            // ambil data produk berdasarkan id
            $product = Product::findOrFail($id);
            // cek jika field photo tidak null/kosong
            if (!empty($product->photo)) {
                // hapus file photo dari folder uploads/product
                File::delete(public_path('uploads/product/' . $product->photo));
            }
            // hapus data produk dari tabel products
            $product->delete();

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
