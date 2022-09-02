<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\UserRegistered;
use App\Models\JobLocation;
use App\Models\Skill;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'integer', 'digits:10', 'unique:users,phone_number'],
            'experience_in_years' => ['required', 'integer', 'gte:0', 'lte:30'],
            'notice_period_in_days' => ['required', 'integer', 'gte:0', 'lte:90'],
            'skills' => ['required','exists:skills,id'],
            'job_location_id' => ['required','integer'],
            'resume_file' => ['mimes:pdf','required','max:2048'],
            'profile_image_file' => ['mimes:jpeg,jpg,png','required','max:2048'],
        ]);
    }


    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        $jobLocations = JobLocation::all();
        $skills = Skill::all();
        return view('auth.register', compact('jobLocations','skills'));
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make("password"),
            'phone_number' => $data['phone_number'],
            'experience_in_years' => $data['experience_in_years'],
            'notice_period_in_days' => $data['notice_period_in_days'],
            'job_location_id' => $data['job_location_id']
        ]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        $resume_file = $request->file('resume_file');
        $profile_image_file = $request->file('profile_image_file');

        $path = public_path().DIRECTORY_SEPARATOR.env('USER_STORAGE_FOLDER_NAME','user_files');

        $this->createDirectoryIfNotExits($path);

        $resume_file_name = $user->id
                .'_resume'.Str::uuid()->toString()
                .'.'.$resume_file->extension();
        $resume_file->move($path, $resume_file_name);


        $profile_image_file_name = $user->id.'_profile'
                .Str::uuid()->toString()
                .'.'.$profile_image_file->extension();
        $profile_image_file->move($path, $profile_image_file_name);

        $user->update(
            compact(
                ['resume_file_name',
                'profile_image_file_name']
            )
        );

        $user->userskills()->sync($request->input('skills'));

        $this->guard()->login($user);

        Mail::to($user->email)->send(
            new UserRegistered($user)
        );

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
                    ? new JsonResponse([], 201)
                    : redirect($this->redirectPath());
    }
}
