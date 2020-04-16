<?php

namespace Mss\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mss\Http\Controllers\Controller;
use Mss\Models\User;
use Illuminate\Http\Response;
use Mss\DataTables\UserDataTable;
use Mss\Http\Requests\UserRequest;
use Mss\Http\Requests\SupplierRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param UserDataTable $userDataTable
     * @return \Illuminate\Http\Response
     */
    public function index(UserDataTable $userDataTable) {
        return $userDataTable->render('user.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $user = new User();

        return view('user.create', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request) {
        $data = $request->except(['roles', 'password']);
        $data['password'] = Hash::make($request->get('password'));
        $user = User::create($data);

        $user->syncRoles($request->get('roles'));

        flash(__('Benutzer angelegt'))->success();

        return redirect()->route('user.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $user = User::findOrFail($id);

        return view('user.show', compact('user'));
    }

    /**
     * @param UserRequest $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update(UserRequest $request, $id) {
        /** @var User $user */
        $user = User::findOrFail($id);

        // save data
        if ($user->getSource() == User::SOURCE_LDAP) {
            $user->update($request->except(['roles', 'password', 'username', 'email']));
        } else {
            $user->update($request->except(['roles', 'password']));
        }

        if (!empty($request->get('password'))) {
            $user->password = Hash::make($request->get('password'));
            $user->save();
        }

        $user->syncRoles($request->get('roles'));

        flash(__('Benutzer gespeichert'))->success();

        return redirect()->route('user.show', $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        User::findOrFail($id)->delete();

        flash(__('Benutzer gelöscht'))->success();

        return redirect()->route('user.index');
    }
}
