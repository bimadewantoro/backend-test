<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CurrencyController extends Controller
{
    public function index()
    {
        $currencies = Currency::all();
        return response()->json($currencies);
    }

    public function store (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_wallet' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $currency = Currency::create($request->all());
        return response()->json($currency, 201);
    }

    public function show($id)
    {
        $currency = Currency::find($id);
        if (is_null($currency)) {
            return response()->json(['message' => 'Record not found'], 404);
        }
        return response()->json($currency, 200);
    }

    public function update(Request $request, $id)
    {
        $currency = Currency::find($id);
        if (is_null($currency)) {
            return response()->json(['message' => 'Record not found'], 404);
        }
        $currency->update($request->all());
        return response()->json($currency, 200);
    }

    public function destroy($id)
    {
        $currency = Currency::find($id);
        if (is_null($currency)) {
            return response()->json(['message' => 'Record not found'], 404);
        }
        $currency->delete();
        return response()->json(null, 204);
    }
}
