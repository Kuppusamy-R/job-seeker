@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">{{ __('Admin Dashboard') }}</div>

                <div class="card-body">
                    @if(Session::has('message'))
                    <div class="alert alert-primary" role="alert">
                        {{Session::get('message')}}
                    </div>
                    @else
                    <div class="alert alert-primary" role="alert">
                        Hi {{ Auth::user()->name }}!
                    </div>
                    @endif
                    <div class="row">
                        <form class="form-inline" method="post" action="{{ route('admin.filter') }}">
                            @csrf
                            @php
                                $old_search_type = old('search_type',isset($requestData['search_type']) ? $requestData['search_type'] : null);
                                $old_search_keyword = old('search_type',isset($requestData['search_keyword']) ? $requestData['search_keyword'] : null);
                            @endphp
                            <input type="hidden" name="filter_type" value="search">
                            <div class="form-group">
                                <select title="Search By" class="selectpicker @error('search_type') is-invalid @enderror" name="search_type" required>
                                    <option value="name" @if ($old_search_type == 'name') selected @endif>Search By Name</option>
                                    <option value="email" @if ($old_search_type == 'email') selected @endif>Search By Email</option>
                                    <option value="phone_number" @if ($old_search_type == 'phone_number') selected @endif>Search By Phone Number</option>
                                </select>
                                @error('search_type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <input type="text" value="{{ $old_search_keyword }}" name="search_keyword" class="form-control @error('search_keyword') is-invalid @enderror" placeholder="Enter Keyword" required>
                                @error('search_keyword')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                            @if (isset($requestData['search_type']))
                            <div class="form-group">
                                <a href="{{ route('admin.home') }}"><button type="button" class="btn btn-primary">Clear</button></a>
                            </div>
                            @endif
                        </form>
                    </div>
                    <div class="row">
                        <form class="form-inline" method="post" action="{{ route('admin.filter') }}">
                            @csrf
                            @php
                                $min_experience = old('min_experience',isset($requestData['min_experience']) ? $requestData['min_experience'] : null);
                                $max_experience = old('max_experience',isset($requestData['max_experience']) ? $requestData['max_experience'] : null);
                                $min_notice_period = old('min_notice_period',isset($requestData['min_notice_period']) ? $requestData['min_notice_period'] : null);
                                $max_notice_period = old('max_notice_period',isset($requestData['max_notice_period']) ? $requestData['max_notice_period'] : null);
                                $skill_values = old('skills',isset($requestData['skills']) ? $requestData['skills'] : []);
                                $job_location_values = old('job_locations',isset($requestData['job_locations']) ? $requestData['job_locations'] : []);
                            @endphp
                            <input type="hidden" name="filter_type" value="filter">
                            <div class="form-inline">
                                <div class="form-group">
                                    <label for="min_experience" class="col-form-label text-md-end">{{ __('Minimum Experience') }}</label>
                                    <input id="min_experience" type="number" value="{{ $min_experience }}" class="form-control @error('min_experience') is-invalid @enderror" name="min_experience" value="{{ old('min_experience') }}" min="0" max="30" oninput="this.value=(parseInt(this.value)||0)">
                                    @error('min_experience')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="max_experience" class="col-form-label text-md-end">{{ __('Maximum Experience') }}</label>
                                    <input id="max_experience" type="number" value="{{ $max_experience }}" class="form-control @error('max_experience') is-invalid @enderror" name="max_experience" value="{{ old('max_experience') }}" min="0" max="30" oninput="this.value=(parseInt(this.value)||0)">
                                    @error('max_experience')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="min_notice_period" class="col-form-label text-md-end">{{ __('Minimum Notice Period') }}</label>
                                    <input id="min_notice_period" type="number" value="{{ $min_notice_period }}" class="form-control @error('min_notice_period') is-invalid @enderror" name="min_notice_period" value="{{ old('min_notice_period') }}" min="0" max="30" oninput="this.value=(parseInt(this.value)||0)">
                                    @error('min_notice_period')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="max_notice_period" class="col-form-label text-md-end">{{ __('Maximum Notice Period') }}</label>
                                    <input id="max_notice_period" type="number" value="{{ $max_notice_period }}" class="form-control @error('max_notice_period') is-invalid @enderror" name="max_notice_period" value="{{ old('max_notice_period') }}" min="0" max="30" oninput="this.value=(parseInt(this.value)||0)">
                                    @error('max_notice_period')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-inline">
                                <div class="form-group">
                                    <select id="skills" title="Select Skills" class="selectpicker @error('skills') is-invalid @enderror" multiple data-live-search="true" name="skills[]">
                                        @foreach ($skills as $skill)
                                        <option value="{{ $skill->id }}" @if (in_array($skill->id,$skill_values)) selected @endif>{{ $skill->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('skills')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <select id="job_locations" title="Select Locations" class="selectpicker @error('job_locations') is-invalid @enderror" multiple data-live-search="true" name="job_locations[]">
                                        @foreach ($jobLocations as $location)
                                        <option value="{{ $location->id }}" @if (in_array($location->id,$job_location_values)) selected @endif>{{ $location->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('job_locations')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-primary" value="Filter">
                                </div>
                                @if (isset($requestData['search_type']) ||
                                    isset($requestData['min_experience']) ||
                                    isset($requestData['max_experience']) ||
                                    isset($requestData['min_notice_period']) ||
                                    isset($requestData['max_notice_period']) ||
                                    isset($requestData['skills'])||
                                    isset($requestData['job_locations']))
                                <div class="form-group">
                                    <a href="{{ route('admin.home') }}"><button type="button" class="btn btn-primary">Clear</button></a>
                                </div>
                                @endif
                            </div>
                        </form>
                    </div>
                    @error('filter_type')
                        <script>alert('Invalid filter_type')</script>
                    @enderror
                    <table class="table">
                        <thead>
                          <tr>
                            <th scope="col"></th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col">Experience in Years</th>
                            <th scope="col">Notice Period in Days</th>
                            <th scope="col">Skills</th>
                            <th scope="col">Job Location</th>
                            <th scope="col">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                            @foreach ( $users as $user)
                                <tr>
                                    <td scope="col"><img style="width: 30px; height: 30px; border-radius:15px" 
                                        src="@if ($user->profile_image_file_name != '')    
                                        {{ url(env('USER_STORAGE_FOLDER_NAME','user_files').DIRECTORY_SEPARATOR.$user->profile_image_file_name) }}
                                        @else
                                        {{ url('default-user.png') }}
                                        @endif"
                                        alt="profile-image"
                                        ></td>
                                <td scope="col">{{ $user->name }}</td>
                                <td scope="col">{{ $user->email }}</td>
                                <td scope="col">{{ $user->phone_number }}</td>
                                <td scope="col">{{ $user->experience_in_years }}</td>
                                <td scope="col">{{ $user->notice_period_in_days }}</td>
                                <td scope="col">{{ $user->skills }}</td>
                                <td scope="col">{{ $user->lob_location}}</td>
                                <td scope="col">
                                    <div class="form-inline">
                                        <div class="form-group">
                                            <a download="{{ url(env('USER_STORAGE_FOLDER_NAME','user_files').DIRECTORY_SEPARATOR.$user->profile_image_file_name) }}" href="{{ url(env('USER_STORAGE_FOLDER_NAME','user_files').DIRECTORY_SEPARATOR.$user->resume_file_name) }}">
                                                <img src="../download.svg" alt="Download Resume" title="Download Resume" class="bi bi-download">
                                            </a>
                                        </div>
                                        <div class="form-group">
                                            <form method="POST" action="{{ route('admin.delete',['id' => $user->id]) }}">
                                                @csrf
                                                @method("DELETE")
                                                <input type="image" name="id" src="../trash.svg">
                                            </form>
                                        </div>
                                    </div>
                                </td>
                              </tr>
                            @endforeach
                        </tbody>
                      </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
