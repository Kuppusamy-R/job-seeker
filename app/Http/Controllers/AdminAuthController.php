<?php

namespace App\Http\Controllers;

use App\Models\JobLocation;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdminAuthController extends Controller
{
    
    public function index()
    {        
        return $this->dashboard(request());
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @param  string  $type
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data, string $type)
    {
        if($type == "filter_type"){
            return Validator::make($data, [
                'filter_type' => ['required', Rule::in(['search','filter'])],
            ]);
        }

        if($type == "search"){
            return Validator::make($data, [
                'search_type' => ['required', Rule::in(['name','email','phone_number'])],
                'search_keyword' => ['required']
            ]);
        }

        if($type == "filter"){
            return Validator::make($data, [
                'min_experience' => ['integer', 'gte:0', 'lte:30', 'required_without_all','nullable'],
                'max_experience' => ['integer', 'gte:0', 'lte:30', 'required_without_all','nullable'],
                'min_notice_period' => ['integer', 'gte:0', 'lte:90', 'required_without_all','nullable'],
                'max_notice_period' => ['integer', 'gte:0', 'lte:90', 'required_without_all','nullable'],
                'skills' => ['array','exists:skills,id', 'required_without_all','nullable'],
                'job_location_id' => ['array', 'exists:job_locations,id', 'required_without_all','nullable']
            ]);
        }

        if($type == "id"){
            return Validator::make($data, [
                'id' => ['required', 'exists:users,id'],
            ]);
        }
        
    }

    public function dashboard(Request $request){
        
        $requestData = $request->all();

        $userSchema = DB::table('users AS u')->whereNull('u.deleted_at')
                    ->join('job_locations AS jl','jl.id','u.job_location_id')
                    ->join('user_skills AS us','us.user_id','u.id')
                    ->join('skills AS s','s.id','us.skill_id')
                    ->select('u.id','u.name','u.email','u.phone_number','u.experience_in_years',
                    'u.notice_period_in_days','jl.name as lob_location',
                    'u.profile_image_file_name', 'u.resume_file_name',
                    DB::raw('GROUP_CONCAT(s.name) AS skills'))
                    ->whereNull('us.deleted_at');

        if($request->input('search_type') == 'name'){
            $userSchema->where('u.name', 'like', "%{$request->input('search_keyword')}%");
        }
        if($request->input('search_type') == 'email'){
            $userSchema->where('u.email', 'like', "%{$request->input('search_keyword')}%");
        }
        if($request->input('search_type') == 'phone_number'){
            $userSchema->where('u.phone_number', 'like', "%{$request->input('search_keyword')}%");
        }
        if($request->filled('min_experience')){
            $userSchema->where('u.experience_in_years', '>=', (int)$request->input('min_experience'));
        }
        if($request->filled('max_experience')){
            $userSchema->where('u.experience_in_years', '<=', (int)$request->input('max_experience'));
        }
        if($request->filled('min_notice_period')){
            $userSchema->where('u.notice_period_in_days', '>=', (int)$request->input('min_notice_period'));
        }
        if($request->filled('max_notice_period')){
            $userSchema->where('u.notice_period_in_days', '<=', (int)$request->input('max_notice_period'));
        }
        if($request->filled('skills')){
            $userSchema->whereIn('s.id',$request->input('skills'));
        }
        if($request->filled('job_locations')){
            $userSchema->whereIn('jl.id',$request->input('job_locations'));
        }

        $users = $userSchema->groupBy('u.id')->get();

        $skills = Skill::all();
        $jobLocations = JobLocation::all();

        return view('admin.home', compact('users','skills','jobLocations','requestData'));
    }

    public function filter(Request $request){

        $validated = $this->validator($request->all(),"filter_type")->validate();

        $this->validator($request->all(),$validated["filter_type"])->validate();
        return $this->dashboard($request);
    }

    public function login()
    {
        return view('admin.login');
    }

    public function handleLogin(Request $request)
    {

        if(Auth::guard('webadmin')->attempt($request->only(['email', 'password']))) 
        {
            return redirect()->route('admin.home');
        }

        return redirect()->back()->with('error', 'Invalid Credentials');
    }

    public function delete($id)
    {

        $user = User::findOrFail($id);

        $path = public_path().DIRECTORY_SEPARATOR.env('USER_STORAGE_FOLDER_NAME','user_files');

        if($user->profile_image_file_name != ''){
            File::delete($path.DIRECTORY_SEPARATOR.$user->profile_image_file_name);
        }
        if($user->resume_file_name != ''){
            File::delete($path.DIRECTORY_SEPARATOR.$user->resume_file_name);
        }

        $user->update(['profile_image_file_name' => null, 'resume_file_name' => null]);
        $user->delete();

        request()->session()->flash('message','Job Deleted Successfully!');

        return redirect()->route('admin.home');
    }

    public function logout()
    {
        Auth::guard('webadmin')->logout();

        return redirect()->route('admin.login');
    }
}
