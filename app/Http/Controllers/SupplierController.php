<?php

namespace Mss\Http\Controllers;

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
        return $supplierDataTable->render('supplier/list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        // not needed!
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
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
