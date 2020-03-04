<?php

namespace Mss\Http\Controllers;

use Illuminate\Http\Response;
use Mss\Http\Requests\UnitRequest;
use Illuminate\Http\Request;
use Mss\DataTables\UnitDataTable;
use Mss\Models\Unit;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UnitDataTable $unitDataTable)
    {
        return $unitDataTable->render('unit.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $unit = new Unit();

        return view('unit.create', compact('unit'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  UnitRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UnitRequest $request) {
        Unit::create($request->all());

        flash(__('Einheit angelegt'))->success();

        return redirect()->route('unit.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $unit = Unit::findOrFail($id);
        $context = [
            "unit" => $unit,
            "audits" => $unit->getAudits()
        ];

        return view('unit.show', $context);
    }

    /**
     * @param UnitRequest $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update(UnitRequest $request, $id) {
        $unit = Unit::findOrFail($id);

        // save data
        $unit->update($request->all());

        flash(__('Einheit gespeichert'))->success();

        return redirect()->route('unit.show', $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        Unit::findOrFail($id)->delete();

        flash(__('Einheit gelöscht'))->success();

        return redirect()->route('unit.index');
    }
}
