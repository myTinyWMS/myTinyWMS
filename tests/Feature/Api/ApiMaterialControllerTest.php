<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mss\Model\ORM\Material;
use Mss\User;
use Tests\TestCase;

class ApiMaterialControllerTest extends TestCase {
    use DatabaseTransactions;

    public $connectionsToTransact = ['onp', 'primary'];

    public function __construct($name = null, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);

        $this->afterApplicationCreatedCallbacks = [function() {
            Material::where('id', 1)->delete();
            Material::where('id', 100)->delete();
            Material::create(['id' => 1, 'barcode' => 'abcdefg', 'entnahmemenge' => 100]);
            User::create(['name' => 'testuser', 'email' => 'mail@example.com', 'password' => bcrypt('testpass')]);
        }];
    }

    /*public function testShow() {
        $response = $this->get('/api/material/1');
        $response->assertStatus(401);
        $auth = ["Authorization" => "Basic " . base64_encode('mail@example.com:testpass')];
        $response = $this->get('/api/material/1', $auth);
        $response->assertStatus(200);
        $material = json_decode($response->getContent());
        $this->assertEquals('abcdefg', $material->barcode);
        $response = $this->get('/api/material/100', $auth);
        $response->assertStatus(404);
    }

    public function testUpdate() {
        $auth = ["Authorization" => "Basic " . base64_encode('mail@example.com:testpass')];
        $response = $this->patch('/api/material/1', ['barcode' => 'hijklmn']);
        $response->assertStatus(401);
        $response = $this->patch('/api/material/1', ['barcode' => 'hijklmn'], $auth);
        $response->assertStatus(200);
        $response = $this->get('/api/material/1', $auth);
        $response->assertStatus(200);
        $material = json_decode($response->getContent());
        $this->assertEquals('hijklmn', $material->barcode);
        $this->assertEquals(100, $material->entnahmemenge);
    }*/
}
