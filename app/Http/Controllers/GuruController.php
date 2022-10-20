<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GuruController extends Controller
{
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);
        $data = User::role('guru')->where('nama', 'like', '%' . $request->name . '%')->simplePaginate(10);
        return response()->json($data, 200);
    }
}
