<?php

namespace App\Http\Controllers\Api;

use App\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function search(Request $request)
    {
        // validasi request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'invalid', 'message' => $validator->errors()]);
        }

        try {
            $customer = Customer::where('email', $request->email)->first();
            if ($customer) {
                return response()->json(['status' => 'success', 'data' => $customer], 200);
            }

            return response()->json(['status' => 'failed', 'data' => []]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
