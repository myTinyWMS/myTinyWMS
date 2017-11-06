<?php

namespace Mss\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
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
        $data = $request->request->all();
        if (isset($data['date']) && is_array($data['date'])) $data['date'] = $data['date']['date'];
        $material->update($data);
        return Response::create(Json::encode(['result' => 'success']));
    }
}
