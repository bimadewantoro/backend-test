<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    public function index()
    {
        $wallets = Wallet::all();
        return response()->json($wallets);
    }

    public function store (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'id_currency' => 'required',
            'balance' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $wallet = Wallet::create($request->all());
        return response()->json($wallet, 201);
    }

    public function show($id)
    {
        $wallet = Wallet::find($id);
        if (is_null($wallet)) {
            return response()->json(['message' => 'Record not found'], 404);
        }
        return response()->json($wallet, 200);
    }

    public function update(Request $request, $id)
    {
        $wallet = Wallet::find($id);
        if (is_null($wallet)) {
            return response()->json(['message' => 'Record not found'], 404);
        }
        $wallet->update($request->all());
        return response()->json($wallet, 200);
    }

    public function destroy($id)
    {
        $wallet = Wallet::find($id);
        if (is_null($wallet)) {
            return response()->json(['message' => 'Record not found'], 404);
        }
        $wallet->delete();
        return response()->json(null, 204);
    }

    public function getWalletsByUser($id)
    {
        $wallets = Wallet::where('id_user', $id)->get();
        return response()->json($wallets);
    }
}
