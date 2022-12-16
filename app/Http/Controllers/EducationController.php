<?php

namespace App\Http\Controllers;

use App\Models\Education;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EducationController extends Controller
{
    public function educationCreate(Request $request){
        $validator = Validator::make($request->all(), [
            'education' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 400);
        }
        foreach($request->education as $edu){
            Education::create([
                'sekolah' => $edu['sekolah'],
                'deskripsi' => $edu['deskripsi'],
                'in' => $edu['in'],
                'out' => $edu['out'],
                'user_id' => Auth::user()->id
            ]);
        }
        return response()->json(['Message' => 'Data Education User Created!'],200);
    }
    public function educationUpdate(Request $request){
        $validator = Validator::make($request->all(), [
            'education' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 400);
        }

        // foreach($request->education as $new){
        //     $oldEdu = Education::where('id', $new['id']);
        //     // return $oldEdu;
        //     if(!$oldEdu->first()){
        //         // return 'halo';
        //         Education::create([
        //             'sekolah' => $new['sekolah'],
        //             'deskripsi' => $new['deskripsi'],
        //             'in' => $new['in'],
        //             'out' => $new['sekolah']
        //         ]);
        //     }
        //     foreach($oldEdu->get() as $a){
        //         if($a['sekolah'] != $new['sekolah']){
        //             $oldEdu->update([
        //                 'sekolah'=> $new['sekolah']
        //             ]);
        //         }
        //         if($a['deskripsi'] != $new['deskripsi']){
        //             $oldEdu->update([
        //                 'deskripsi'=> $new['deskripsi']
        //             ]);
        //         }
        //         if($a['in'] != $new['in']){
        //             $oldEdu->update([
        //                 'in'=> $new['in']
        //             ]);
        //         }
        //         if($a['out'] != $new['out']){
        //             $oldEdu->update([
        //                 'out'=> $new['out']
        //             ]);
        //         }
        //     }
        // }
        foreach($request->education as $new){
            if(isset($new['id'])) {
                $oldEdu = Education::findOrFail($new['id']);
                $oldEdu->update([
                    'sekolah'=> $new['sekolah'],
                    'in'=> $new['in'],
                    'out'=> $new['out']
                ]);
            } else {
                Education::create([
                'sekolah' => $new['sekolah'],
                'in' => $new['in'],
                'out' => $new['out'],
                'user_id' => Auth::user()->id
                ]);
            }
        }

    $dat = Education::where('user_id', Auth::user()->id)->get();
    return response()->json([
        'message' => 'Data Education User Updated !',
        'data' => $dat
    ],200);
    }
    public function educationUser(){
        $dataUser = Education::where('user_id', Auth::user()->id)->get();
        return response()->json($dataUser, 200);
    }
}
