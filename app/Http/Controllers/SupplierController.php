<?php

namespace Mss\Http\Controllers;

use Illuminate\Http\Response;
use Mss\Http\Requests\SupplierRequest;
use Illuminate\Http\Request;
use Mss\DataTables\SupplierDataTable;
use Mss\Models\Supplier;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(SupplierDataTable $supplierDataTable)
    {
        $this->authorize('supplier.view', Supplier::class);

        return $supplierDataTable->render('supplier.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create() {
        $this->authorize('supplier.create', Supplier::class);

        $supplier = new Supplier();

        return view('supplier.create', compact('supplier'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SupplierRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(SupplierRequest $request) {
        $this->authorize('supplier.create', Supplier::class);

        Supplier::create($request->all());

        flash(__('Lieferant angelegt'))->success();

        return redirect()->route('supplier.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($id) {
        $this->authorize('supplier.view', Supplier::class);

        $supplier = Supplier::findOrFail($id);
        $context = [
            "supplier" => $supplier,
            "audits" => $supplier->getAudits()
        ];

        return view('supplier.show', $context);
    }

    /**
     * @param SupplierRequest $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(SupplierRequest $request, $id) {
        $this->authorize('supplier.edit', Supplier::class);

        $supplier = Supplier::findOrFail($id);

        // save data
        $supplier->update($request->all());

        flash(__('Lieferant gespeichert'))->success();

        return redirect()->route('supplier.show', $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id) {
        $this->authorize('supplier.delete', Supplier::class);

        Supplier::findOrFail($id)->delete();

        flash(__('Lieferant gelöscht'))->success();

        return redirect()->route('supplier.index');
    }
}
