<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(){
        $users = User::get();

        return view('modules.user.get', compact(['users']));
    }

    public function new(){
        return view('modules.user.add');
    }

    public function save(Request $request){
        dd($request->all());
        $request->validate([
            'role_id' => ['required'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('message', 'User has been created.');
    }

    public function edit($id){
        $user = User::find($id);

        return view('modules.user.add', compact(['id', 'user']));
    }

    public function update(Request $request, $id){
        dd($request->all());
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        User::where('id', $id)->update([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('message', 'User has been updated.');
    }
}
