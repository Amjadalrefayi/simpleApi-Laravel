<?php

namespace Tests\Unit;

use App\Models\Product;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\UploadedFile;


class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_all_products_Successful(){
        Sanctum::actingAs(
            User::factory()->create()
        );
        $response = $this->get('api/products/index/3');
        $response->assertStatus(200);
    }

    public function test_show_specific_product_Successful(){
        $this->withoutExceptionHandling();
        Sanctum::actingAs(
            User::factory()->create()
        );
        $response = $this->post('api/products/store', [
            'name' => 'test',
            'description' => 'testtestest',
            'image' => UploadedFile::fake()->image('test.jpg')
        ]);
        $product=Product::all()->first();
        $response=$this->get('api/products/show/'.$product->id);
        $response->assertStatus(200);
    }

    public function test_add_product_Successful(){
        $this->withoutExceptionHandling();
        Sanctum::actingAs(
            User::factory()->create()
        );
        $response = $this->post('api/products/store', [
            'name' => 'test',
            'description' => 'testtestest',
            'image' => UploadedFile::fake()->image('test.jpg')
        ]);
        $response->assertStatus(200);
    }

    public function test_update_product_Successful(){

        $this->withoutExceptionHandling();
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->post('api/products/store', [
            'name' => 'test',
            'description' => 'testtestest',
            'image' => UploadedFile::fake()->image('test.jpg')
        ]);
        $product=Product::all()->first();

        $payload=[
            'name' => 'test2',
            'description' => 'testtestest2',
            'image' => UploadedFile::fake()->image('test2.jpg')
        ];

        $response=$this->put('api/products/update/'.$product->id,$payload);
        $response->assertStatus(200);
    }

    public function test_softdelete_product(){
        $this->withoutExceptionHandling();
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->post('api/products/store', [
            'name' => 'test',
            'description' => 'testtestest',
            'image' => UploadedFile::fake()->image('test.jpg')
        ]);
        $product=Product::all()->first();

        $response=$this->delete('api/products/softdelete/'.$product->id);
        $response->assertStatus(200);

    }

    public function test_forceDelete_product(){
        $this->withoutExceptionHandling();
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->post('api/products/store', [
            'name' => 'test',
            'description' => 'testtestest',
            'image' => UploadedFile::fake()->image('test.jpg')
        ]);
        $product=Product::all()->first();

        $this->delete('api/products/softdelete/'.$product->id);
        $response=$this->delete('api/products/forcedelete/'.$product->id);
        $response->assertStatus(200);
    }

    public function test_resrote_product_from_delete_(){

        $this->withoutExceptionHandling();
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->post('api/products/store', [
            'name' => 'test',
            'description' => 'testtestest',
            'image' => UploadedFile::fake()->image('test.jpg')
        ]);
        $product=Product::all()->first();

        $this->delete('api/products/softdelete/'.$product->id);
        $response=$this->get('api/products/restore/'.$product->id);
        $response->assertStatus(200);
    }

}
