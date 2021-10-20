<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function authenticate_user_can_create_product_through_the_form()
    {
        $this->assertCount(0, Product::all());

        $this->actingAsAuthenticateUser();

        $response = $this->post('product', [
            'title' => 'Product Title',
            'sku' => 'pt-001',
        ]);

        $response->assertOk();
        $this->assertCount(1, Product::all());
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
    }

    /**
     * @test
     */
    public function authenticate_user_can_not_create_product_through_the_form_using_duplicate_sku()
    {
        $this->actingAsAuthenticateUser();

        factory(Product::class)->create(['sku' => 'p-test']);

        $response = $this->post('product', [
            'title' => 'Product Title',
            'sku' => 'p-test',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['sku']);
    }

    /**
     * @test
     */
    public function authenticate_user_can_not_create_product_through_the_form_without_title()
    {
        $this->actingAsAuthenticateUser();

        $response = $this->post('product', [
            'sku' => 'p-test',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);
    }

    /**
     * @test
     */
    public function authenticate_user_can_not_create_product_through_the_form_without_sku()
    {
        $this->actingAsAuthenticateUser();

        $response = $this->post('product', [
            'title' => 'Product Title',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['sku']);
    }

    /**
     * @test
     */
    public function authenticate_user_can_update_product_through_the_form()
    {
        $this->actingAsAuthenticateUser();

        $product = factory(Product::class)->create();

        $this->assertNotEquals('Product Title', $product->title);

        $response = $this->put('product/' . $product->id, [
            'title' => 'Product Title',
            'sku' => $product->sku,
        ]);

        $response->assertOk();
        $this->assertEquals('Product Title', Product::whereId($product->id)->first()->title);
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
    }

    /**
     * @test
     */
    public function authenticate_user_can_not_update_product_through_the_form_using_duplicate_sku()
    {
        $this->actingAsAuthenticateUser();

        factory(Product::class)->create(['sku' => 'p-test-01']);

        $product = factory(Product::class)->create(['sku' => 'p-test-02']);

        $response = $this->put('product/' . $product->id, [
            'title' => 'Product Title',
            'sku' => 'p-test-01', // this should fail, because this sku is already exists for another product
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['sku']);
    }
}
