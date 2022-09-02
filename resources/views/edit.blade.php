@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Profile Details') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('update') }}" enctype='multipart/form-data'>
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $user->name }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $user->email }}" required autocomplete="email" readonly>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="phone_number" class="col-md-4 col-form-label text-md-end">{{ __('Phone Number') }}</label>

                            <div class="col-md-6">
                                <input id="phone_number" type="tel" class="form-control @error('phone_number') is-invalid @enderror" name="phone_number" value="{{ $user->phone_number }}" maxlength="10" minlength="10" required>

                                @error('phone_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="experience_in_years" class="col-md-4 col-form-label text-md-end">{{ __('Experience in Years') }}</label>

                            <div class="col-md-6">
                                <input id="experience_in_years" type="number" class="form-control @error('experience_in_years') is-invalid @enderror" name="experience_in_years" value="{{ $user->experience_in_years }}" min="0" max="30" oninput="this.value=(parseInt(this.value)||0)" required>

                                @error('experience_in_years')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="notice_period_in_days" class="col-md-4 col-form-label text-md-end">{{ __('Notice period in Days') }}</label>

                            <div class="col-md-6">
                                <input id="notice_period_in_days" type="number" class="form-control @error('notice_period_in_days') is-invalid @enderror" name="notice_period_in_days" value="{{ $user->notice_period_in_days }}" min="0" max="90" oninput="this.value=(parseInt(this.value)||0)" required>

                                @error('notice_period_in_days')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="skills" class="col-md-4 col-form-label text-md-end">{{ __('Select Skills') }}</label>
                            <div class="col-md-6">
                                <select id="skills" title="Select Skills" class="selectpicker @error('skills') is-invalid @enderror" multiple data-live-search="true" name="skills[]">
                                    @foreach ($skills as $skill)
                                    <option value="{{ $skill->id }}" @if (in_array($skill->id, $user_skills)) selected @endif>{{ $skill->name }}</option>
                                    @endforeach
                                </select>
                                @error('skills')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="job_location_id" class="col-md-4 col-form-label text-md-end">{{ __('Job Location') }}</label>

                            <div class="col-md-6">
                                <select id="job_location_id" title="Select" name = "job_location_id" class="form-control @error('job_location_id') is-invalid @enderror" required>
                                    @foreach ($jobLocations as $location)
                                    <option value="{{ $location->id }}" @if ($user->job_location_id == $location->id) selected @endif>{{ $location->name }}</option>
                                    @endforeach
                                </select>
                                @error('job_location_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="resume_file" class="col-md-4 col-form-label text-md-end">{{ __('Upload Resume') }}</label>

                            <div class="col-md-6">
                                <input id="resume_file" type="file" class="form-control @error('resume_file') is-invalid @enderror" name="resume_file" accept="application/pdf">
                                @if ($user->profile_image_file_name !='')
                                    <div class="form-group">
                                        <a download="{{ $user->resume_file_name }}" href="{{ url(env('USER_STORAGE_FOLDER_NAME','user_files').DIRECTORY_SEPARATOR.$user->resume_file_name) }}">
                                            <img src="../download.svg" alt="Download Resume" title="Download Resume" class="bi bi-download">
                                        </a>
                                    </div> 
                                @endif
                                
                                @error('resume_file')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">

                            <label for="profile_image_file" class="col-md-4 col-form-label text-md-end">{{ __('Upload Profile Image') }}</label>

                            <div class="col-md-6">
                                <input id="profile_image_file" type="file" class="form-control @error('profile_image_file') is-invalid @enderror" name="profile_image_file" accept="image/png, image/gif, image/jpeg">
                                @if ($user->profile_image_file_name !='')
                                    <div class="form-group">
                                        <a download="{{ $user->profile_image_file_name }}" href="{{ url(env('USER_STORAGE_FOLDER_NAME','user_files').DIRECTORY_SEPARATOR.$user->profile_image_file_name) }}">
                                            <img src="../download.svg" alt="Download Profile Image" title="Download Profile Image" class="bi bi-download">
                                        </a>
                                    </div> 
                                @endif
                                @error('profile_image_file')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        
                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Update') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
