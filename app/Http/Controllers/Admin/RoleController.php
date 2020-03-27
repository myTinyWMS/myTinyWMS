<?php

namespace Mss\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Mss\DataTables\RoleDataTable;
use Mss\Http\Controllers\Controller;
use Mss\Http\Requests\RoleRequest;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param RoleDataTable $roleDataTable
     * @return \Illuminate\Http\Response
     */
    public function index(RoleDataTable $roleDataTable) {
        return $roleDataTable->render('role.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $role = new Role();

        return view('role.create', compact('role'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param RoleRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request) {
        $role = Role::create(['name' => $request->get('name')]);
        $role->syncPermissions($request->get('permissions'));

        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        flash(__('Rolle angelegt'))->success();

        return redirect()->route('role.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $role = Role::findOrFail($id);

        return view('role.show', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param RoleRequest $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, $id) {
        /** @var oOle $role */
        $role = Role::findOrFail($id);
        $role->update(['name' => $request->get('name')]);
        $role->syncPermissions($request->get('permissions'));

        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        flash(__('Rolle gespeichert'))->success();

        return redirect()->route('role.show', $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        Role::findOrFail($id)->delete();

        flash(__('Rolle gelöscht'))->success();

        return redirect()->route('role.index');
    }
}
