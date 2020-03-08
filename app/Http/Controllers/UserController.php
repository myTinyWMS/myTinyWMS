<?php

namespace Mss\Http\Controllers;

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
     * @return \Illuminate\Http\Response
     */
    public function index(UserDataTable $userDataTable)
    {
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
     * @param  SupplierRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SupplierRequest $request) {
        User::create($request->all());

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
        $supplier = User::findOrFail($id);
        $context = [
            "user" => $supplier
        ];

        return view('user.show', $context);
    }

    /**
     * @param UserRequest $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update(UserRequest $request, $id) {
        $user = User::findOrFail($id);

        // save data
        $user->update($request->all());

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
