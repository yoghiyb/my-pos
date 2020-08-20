<?php

namespace App\Http\Controllers\Api;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Menampilkan kategori berdasarkan yang paling baru.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::orderBy('created_at', 'desc')->paginate(10);
        return response()->json(['status' => 'success', 'data' => $categories], 200);
    }

    /**
     * Mengambil semua data kategori
     *
     * @return \Illuminate\Http\Response
     */
    public function categories()
    {
        $categories = Category::orderBy('created_at', 'desc')->get();
        return response()->json(['status' => 'success', 'data' => $categories], 200);
    }

    /**
     * Membuat kategori baru
     *
     * @request \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validasi request
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'invalid', 'message' => $validator->errors()]);
        }

        try {
            $categories = $request->all();
            // simpan kategori baru
            Category::create($categories);

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Memperbarui kategori
     *
     * @request \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @param $id
     */
    public function update(Request $request, $id)
    {
        // validasi request
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'invalid', 'message' => $validator->errors()]);
        }

        try {
            // cari kategori berdasarkan id
            $category = Category::findOrFail($id);
            // update kategori
            $category->update($request->except(['id']));

            return response()->json(['status' => 'success', 'message' => 'Kategori' . $category->name . 'diperbarui'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Menghapus kategori berdasarkan id
     *
     * @return \Illuminate\Http\Response
     * @param $id
     */
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
