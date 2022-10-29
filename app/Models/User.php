<?php

namespace App\Models;

use App\Models\divisi;
use App\Models\Speciality;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        // 'updated_at',
        'roles'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    // protected $appends = ['link'];
    protected $appends = ['skill', 'division', 'rank', 'speciality', 'profile', 'department'];
    // public function getLinkAttribute()
    // {
    //     // $this->roles();
    //     if ($this->hasRole('student')) {
    //         return  '/student/user/' . $this->id;
    //     }
    // }
    public function getSkillAttribute()
    {
        // $this->roles();
        // return $this->artskilu;
        if ($this->hasRole('student')) {
            return  'mentor/user/student/detail/' . $this->id;
        }
    }
    public function getDivisionAttribute()
    {
        $divisi = divisi::where('id', $this->divisi_id)->first();
        return $divisi;
    }
    public function userHistory()
    {
        return $this->hasMany(user_detail_history::class);
    }
    // public function divisi()
    // {
    //     return $this->belongsTo(divisi::class);
    // }

    public function divisi()
    {
        return $this->hasOne(divisi::class, 'id', 'divisi_id');
    }
    public function userDetail()
    {
        return $this->hasOne(UserDetail::class);
    }
    public function userSkill()
    {
        return $this->hasMany(UserSkill::class);
    }
    public function Average() {
        return $this->hasOne(Average::class, 'user_id', 'id');
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id', 'id');
    }
    public function speciality()
    {
        return $this->hasOne(Speciality::class, 'user_id', 'id');
    }

    public function getRankAttribute(){
        $overall = $this->average;
        if($overall){
            if ($overall >= 90.00){
                $rank = "Gold";
            }elseif($overall >= 70.00){
                $rank = "Silver";
            }else{
                $rank = "Bronze";
            }
            return $rank;
        }else{
            $rank = "bronze";
            return $rank;
        }
    }

    public function getSpecialityAttribute()
    {
        $specialities = SpecialityUser::where("user_id", $this->id)->get();
        $speciality_each = [];
        foreach ($specialities as $speciality) {
            $speciality_each[] = ["name" => $speciality->Speciality->nama];
        }

        return $speciality_each;
    }

    public function getProfileAttribute()
    {
        $profile = Profile::where("user_id", $this->id)->first();
        return $profile;
    }
    public function getDepartmentAttribute(){
        $divisi = divisi::where('id', $this->divisi_id)->first();
        $jurusan = department::where('id', $divisi->department_id)->first();
        return $jurusan;
    }
    public function divisisubskill(){
        return $this->hasMany(DivisiSkillSubskill::class, "divisi_id", "divisi_id");
    }

    public static function createuser($request, $role){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'nama' => 'required|string',
            'tanggal_lahir' => 'required|date',
            // 'password' => 'required',
            // 'nickname' => 'string',
            // 'bio' => 'text',
            'notelp' => 'required|string',
            'divisi_id' => 'required|integer',
            'image' => 'required|image' ,
            'provinsi_id' => 'required|integer',
            'kota_id' => 'required|integer',
            'speciality' => 'string',
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 400);
        }
        $department = department::where('id', $request->department_id)->first();
        $divisi = divisi::where('id', $request->divisi_id)->with('dataskill')->first();
        $user = User::create([
            'email' => $request->email,
            'nama' => $request->nama,
            'tanggal_lahir' => $request->tanggal_lahir,
            'password' => $request->password ? Hash::make($request->password) : Hash::make('smkrus'),
            'divisi_id' => $divisi->id,
            'UUID' => Str::orderedUuid(),
            'average' => 30,
        ]);
        $path = Storage::disk('public')->put('images/'. $user->UUID . $request->image->getClientOriginalName(), file_get_contents($request->image));
        $userDetail = Profile::create([
            'user_id' => $user->id,
            'nickname' => $request->nickname != null ? $request : '',
            'bio' => $request->bio != null ? $request : '',
            'notelp' => $request->notelp,
            // 'negara_id' => $request->negara_id,
            'gambar' => $user->UUID . $request->image->getClientOriginalName(),
            'provinsi_id' => $request->provinsi_id != null ? $request->provinsi_id : 1,
            'kota_id' => $request->kota_id != null ? $request->kota_id : 1,
        ]);
        $speciality = $request->speciality != null ? $request->speciality : '';
        Speciality::create([
            'name' => $speciality,
            'user_id' => $user->id,
        ]);
        $user->assignRole((string)$role);
        
        $userskillcreate =  [];
        foreach($user->divisisubskill as $divisisubskill){
            $userskillcreate[] = [
                'user_id' => $user->id,
                'sub_skill_id' => $divisisubskill->sub_skill_id,
                'skill_id' => $divisisubskill->skill_id,
                'nilai' => 30,
                'nilai_history' => 0
            ];
        }
        try {
            UserSkill::insert($userskillcreate);
            return response()->json(["message" => "data created"], 201);
        } catch (\Throwable $th) {
            return response()->json(["error" => "data not created"], 400);
        }

    }
}
