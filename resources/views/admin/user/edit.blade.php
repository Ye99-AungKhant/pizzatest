@extends('admin.layout.app')

@section('content')
<div class="content-wrapper">
    <section class="content">
      <div class="container-fluid">
        <div class="row mt-4">
          <div class="col-10 offset-2 mt-5">
            <div class="col-md-9">
                <a href="{{ route('admin#pizza') }}" class="text-decoration-none text-black"><div class="mb-4"><i class="fa-solid fa-arrow-left">Back</i></div></a>
              <div class="card">
                <div class="card-header p-2">
                  <legend class="text-center">Edit User</legend>
                </div>
                <div class="card-body">
                  <div class="tab-content">
                    <div class="active tab-pane" id="activity">
                      <form class="form-horizontal" method="POST" action="{{ route('admin#updateUser',$userData->id) }}">
                        @csrf
                        <div class="form-group row">
                          <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control" placeholder="Enter Name" name="name" value="{{ old('name',$userData->name) }}">
                            @if ($errors->has('name'))
                                <p class="text-danger">{{ $errors->first('name') }}</p>
                            @endif
                          </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputName" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" placeholder="Enter Email" name="email" value="{{ old('email',$userData->email) }}">
                                @if ($errors->has('email'))
                                    <p class="text-danger">{{ $errors->first('email') }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputName" class="col-sm-2 col-form-label">Phone</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" placeholder="Enter Phone" name="phone" value="{{ old('phone',$userData->phone) }}">
                                @if ($errors->has('phone'))
                                    <p class="text-danger">{{ $errors->first('phone') }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputName" class="col-sm-2 col-form-label">Address</label>
                            <div class="col-sm-10">
                                <textarea name="address" class="form-control" cols="30" rows="3" placeholder="Enter Address">{{ old('address',$userData->address) }}</textarea>
                                @if ($errors->has('address'))
                                    <p class="text-danger">{{ $errors->first('address') }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputName" class="col-sm-2 col-form-label">User Role</label>
                            <div class="col-sm-10">
                                <select name="role" class="form-control">
                                    <option value="userRole">{{ $userData->role }}</option>

                                    @if ($userData->role == 'user')
                                        <option value="admin">admin</option>
                                    @elseif($userData->role == 'admin')
                                        <option value="user">user</option>
                                    @endif
                                </select>
                                @if ($errors->has('userData'))
                                    <p class="text-danger">{{ $errors->first('userData') }}</p>
                                @endif
                            </div>
                        </div>

                       
                        <div class="form-group row">
                          <div class="offset-sm-2 col-sm-10">
                            <button type="submit" class="btn bg-dark text-white">Update</button>
                          </div>
                        </div>
                      </form>
                      
                    </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
@endsection