<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\User;
use App\Traits\PhotoTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    use PhotoTrait;
    public function index(request $request)
    {
        if($request->ajax()) {
            $user = User::latest()->get();
            return Datatables::of($user)
                ->addColumn('action', function ($user) {
                    return '
                            <button class="btn btn-pill btn-danger-light" data-toggle="modal" data-target="#delete_modal"
                                    data-id="' . $user->id . '" data-title="' . $user->user_name . '">
                                    <i class="fas fa-trash"></i>
                            </button>
                       ';
                })
                ->editColumn('balance', function ($user) {
                    if($user->balance != null)
                        return $user->balance.' نقطة ' ;
                    else
                        return '<span class="badge badge-danger">لا يوجد نقاط</span>';
                })
                ->editColumn('email', function ($user) {
                        return '<a href="mailto:'.$user->email.'">'.$user->email.'</a>';
                })

                ->editColumn('image', function ($user) {
                    $image = ($user->image);
                    return '
                    <img alt="image" onclick="window.open(this.src)" class="avatar avatar-md rounded-circle" src="'.$image.'">
                    ';
                })
                ->escapeColumns([])
                ->make(true);
        }else{
            return view('Admin/user/index');
        }
    }


    public function create()
    {
        return view('Admin.user.parts.create');
    }


    public function store(Request $request)
    {
        $valiadate = $request->validate([
           'user_name'     => 'required',
           'password' => 'required|min:6',
        ],[
            'user_name.required' => 'يرجي ادخال اسم المستخدم'
        ]);
        $data = $request->except('_token','image');
        if($request->has('image') && $request->image != null)
            $data['image'] = $this->saveImage($request->image,'assets/uploads/users','image','100');

        $data['password'] = Hash::make($request->password);
        User::create($data);
        return response()->json(['status' => 200]);
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        $user = User::find($id);
        return view('Admin.user.parts.edit',compact('user'));
    }



    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $data = $request->except('_token','_method','image');

        if($request->has('password') && $request->password != null)
            $data['password'] = Hash::make($request->password);
        else
            unset($data['password']);

        if($request->has('image') && $request->image != null){
            if (file_exists($user->getAttributes()['image'])) {
                unlink($user->getAttributes()['image']);
            }
            $data['image'] = $this->saveImage($request->image,'assets/uploads/users');
        }

        $user->update($data);
        return response()->json(['status' => 200]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function delete(request $request)
    {
        $user = User::findOrFail($request->id);
        if (file_exists($user->getAttributes()['image'])) {
            unlink($user->getAttributes()['image']);
        }
        $user->delete();
        return response(['message'=>'تم الحذف بنجاح','status'=>200],200);
    }
}
