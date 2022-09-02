<?php

namespace App\Http\Controllers;

use App\Models\JobLocation;
use App\Models\Skill;
use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data,$type)
    {
        if($type == 'user_update'){
            return Validator::make($data, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'phone_number' => ['required', 'integer', 'digits:10', Rule::unique('users')->ignore($data['phone_number'], 'phone_number')],
                'experience_in_years' => ['required', 'integer', 'gte:0', 'lte:30'],
                'notice_period_in_days' => ['required', 'integer', 'gte:0', 'lte:90'],
                'skills' => ['required','exists:skills,id'],
                'job_location_id' => ['required','integer'],
                'resume_file' => ['mimes:pdf','max:2048'],
                'profile_image_file' => ['mimes:jpeg,jpg,png','max:2048'],
            ]);
        }
        
        if($type == 'password_reset'){

            return Validator::make($data, [
                'old_password' => ['required', 
                function($attribute, $value, $fail) {
                    $user = User::find(auth()->id());
                    if(!Hash::check($value,$user->password)){
                        return $fail('The old password does not matched');
                    }
            }],
                'password' => ['required','confirmed', 
                    function($attribute, $value, $fail) {
                        $user = User::find(auth()->id());
                        if(Hash::check($value,$user->password)){
                            return $fail('The old password should not be your new password');
                        }
                }]
            ]);
        }
        
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $jobLocations = JobLocation::all();
        $skills = Skill::all();
        $user = User::findOrFail(auth()->id());
        $user_skills = UserSkill::where('user_id','=',auth()->id())->pluck('skill_id')->toArray();

        return view('home', compact('jobLocations','skills', 'user','user_skills'));
    }

    public function edit(){
        $jobLocations = JobLocation::all();
        $skills = Skill::all();
        $user = User::findOrFail(auth()->id());
        $user_skills = UserSkill::where('user_id','=',auth()->id())->pluck('skill_id')->toArray();

        return view('edit', compact('jobLocations','skills', 'user','user_skills'));
    }

    public function editPassword(){
        $jobLocations = JobLocation::all();
        $skills = Skill::all();
        $user = User::findOrFail(auth()->id());
        $user_skills = UserSkill::where('user_id','=',auth()->id())->pluck('skill_id')->toArray();

        return view('reset', compact('jobLocations','skills', 'user','user_skills'));
    }

    public function updatePassword(Request $request){

        
        $validated = $this->validator($request->all(),'password_reset')->validate();
        $user = User::find(auth()->id());
        $user->password = Hash::make($validated['password']);
        $user->save();

        $request->session()->flash('message', 'Password updated Successfully');

        return redirect()->route('home');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function updateUser(Request $request)
    {

        $validated = $this->validator($request->all(),'user_update')->validate();
        
        $data = [
            'name' => $validated['name'],
            'phone_number' => $validated['phone_number'],
            'experience_in_years' => $validated['experience_in_years'],
            'notice_period_in_days' => $validated['notice_period_in_days'],
            'job_location_id' => $validated['job_location_id'],
        ];

        $user = User::find(auth()->id());

        $resume_file = $request->file('resume_file');
        $profile_image_file = $request->file('profile_image_file');

        $path = public_path().DIRECTORY_SEPARATOR.env('USER_STORAGE_FOLDER_NAME','user_files');
        $this->createDirectoryIfNotExits($path);
        if($request->filled('resume_file')){

            //Delete old file if new file filled
            File::delete($path.DIRECTORY_SEPARATOR.$user->profile_image_file_name);

            //Generate Unique Filename
            $resume_file_name = $user->id
                .'_resume'.Str::uuid()->toString()
                .'.'.$resume_file->extension();
            $resume_file->move($path, $resume_file_name);
            
            //Add to dataset
            $data['resume_file_name'] = $resume_file_name;
        }
        if($request->filled('profile_image_file')){

            //Delete old file if new file filled
            File::delete($path.DIRECTORY_SEPARATOR.$user->resume_file_name);

            //Generate Unique Filename
            $profile_image_file_name = $user->id.'_profile'
                .Str::uuid()->toString()
                .'.'.$profile_image_file->extension();
            $profile_image_file->move($path, $profile_image_file_name);

            //Add to dataset
            $data['profile_image_file'] = $profile_image_file;
        }

        //Update user
        $user->update($data);

        UserSkill::where('user_id', '=', $user->id)->delete();

        foreach($validated['skills'] as $skillId){
            //Update user skills
            $userSkill = new UserSkill();
            $userSkill->user_id = $user->id;
            $userSkill->skill_id = $skillId;
            $userSkill->save();
        }

        $request->session()->flash("message","Profile Updated Successfully.");

        return redirect()->route('home');
    }
}
