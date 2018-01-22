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
     */
    public function index(SupplierDataTable $supplierDataTable)
    {
        return $supplierDataTable->render('supplier.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $supplier = new Supplier();

        return view('supplier.create', compact('supplier'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\SupplierRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SupplierRequest $request) {
        Supplier::create($request->all());

        flash('Lieferant angelegt')->success();

        return redirect()->route('supplier.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
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
     */
    public function update(SupplierRequest $request, $id) {
        $supplier = Supplier::findOrFail($id);

        // save data
        $supplier->update($request->all());

        flash('Lieferant gespeichert')->success();

        return redirect()->route('supplier.show', $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        Supplier::findOrFail($id)->delete();

        flash('Lieferant gelöscht')->success();

        return redirect()->route('supplier.index');
    }
}
