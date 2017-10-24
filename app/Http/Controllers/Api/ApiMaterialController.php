<?php

namespace Mss\Http\Controllers\Api;

use Illuminate\Http\Response;
use Mss\Http\Controllers\Controller;
use Mss\Model\ORM\Material;
use Illuminate\Http\Request;
use Psy\Util\Json;

class ApiMaterialController extends Controller {
    /**
     * Display the specified resource.
     *
     * @param  Material  $material
     * @return \Illuminate\Http\Response
     */
    public function show(Material $material) {
        return Response::create($material->toJson());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Material  $material
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Material $material) {
        $material->update($request->request->all());
        return Response::create(Json::encode(['result' => 'success']));
    }
}
