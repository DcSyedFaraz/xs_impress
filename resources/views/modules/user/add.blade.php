<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <div style="display: inline-block; margin-left: 10px">
                New User
            </div>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ isset($id)? URL::to('user/update/'.$id) : URL::to('user/save') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Role</label>
                            <select class="form-control @error('role_id') is-invalid @enderror" name="role_id">
                                <option value="">Select</option>
                                @foreach(\App\Models\Role::get() as $role)
                                    <option value="{{ $role->id }}" @if(isset($id) && $user->role_id == $role->id) selected @endif>{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('role_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-group mt-4">
                            <label>Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ isset($id)? $user->name : '' }}">
                        </div>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-group mt-4">
                            <label>Email</label>
                            <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ isset($id)? $user->email : '' }}" @if(isset($id)) readonly @endif>
                        </div>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if(!isset($id))
                            <div class="form-group mt-4">
                                <label>Password</label>
                                <input type="text" class="form-control @error('password') is-invalid @enderror" name="password" value="">
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                        <button type="submit" class="btn btn-primary mt-4">Save</button>
                    </form>
                </div>
            </div>
    </div>
</x-app-layout>

