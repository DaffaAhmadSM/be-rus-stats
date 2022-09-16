<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $res = User::with(['divisi', 'profile' => function ($query) {
            $query->with(['province', 'city']);
        }])
            ->findOrFail($id);

        return response()->json($res);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'email' => 'required',
            'tanggal_lahir' => 'required|date',
            'profile.notelp' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 401);
        }
        $user = User::findOrFail($id);
        $user->fill($request->all());
        $user->update();

        $user->profile()->update([
            'notelp' => $request->profile['notelp'],
            'provinsi_id' => $request->profile['provinsi_id'],
            'kota_id' => $request->profile['kota_id']
        ]);
        return response()->json($user->load(['profile.province', 'profile.city', 'divisi']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getRoleById($id)
    {
        $res = User::with(['divisi', 'profile' => function ($query) {
            $query->with(['province', 'city']);
        }])
            ->findOrFail($id);

        return response()->json(
            $res->getRoleNames()->first()
        );
    }
}
