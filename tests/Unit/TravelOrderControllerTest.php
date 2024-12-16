<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\TravelOrder;

class TravelOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate', ['--database' => 'second_connection','--path' => 'database/migrations/second_connection','--force' => true,]);

        $this->user = User::factory()->create();
    }

    public function test_store_creates_travel_order()
    {
        $this->actingAs($this->user, 'api');

        $data = [
            'requester_name' => 'Lucas',
            'destination' => 'São Paulo',
            'departure_date' => '2024-12-20',
            'return_date' => '2024-12-25',
        ];

        $response = $this->postJson('/api/travel-orders', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('orders', $data, 'second_connection');
    }

    public function test_update_changes_status()
    {
        $this->actingAs($this->user, 'api');

        $order = TravelOrder::factory()->create(['user_id' => $this->user->id,'status' => 'requested',])->setConnection('second_connection');

        $response = $this->putJson("/api/travel-orders/{$order->id}", [
            'status' => 'approved',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'approved',
        ], 'second_connection');
    }


    public function test_show_returns_travel_order()
    {
        $this->actingAs($this->user, 'api');

        $order = TravelOrder::factory()->create(['user_id' => $this->user->id])
            ->setConnection('second_connection');

        $response = $this->getJson("/api/travel-orders/{$order->id}");

        $response->assertStatus(200);
        $response->assertJson(['id' => $order->id]);
    }

    public function test_index_filters_travel_orders()
    {
        $this->actingAs($this->user, 'api');

        TravelOrder::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'requested',
        ])->setConnection('second_connection');

        TravelOrder::factory()->create(['user_id' => $this->user->id,'status' => 'approved',])->setConnection('second_connection');

        $response = $this->getJson('/api/travel-orders?status=requested');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
    }

    public function test_notify_sends_notification()
    {
        $this->actingAs($this->user, 'api');

        $order = TravelOrder::factory()->create(['user_id' => $this->user->id,'status' => 'approved',])->setConnection('second_connection');

        $response = $this->postJson("/api/travel-orders/{$order->id}/notify");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'notification' => [
                'message',
                'order_id',
                'status',
                'sent_at',
            ],
        ]);

        $response->assertJson([
            'success' => true,
            'notification' => [
                'message' => "The order #{$order->id} has been approved.",
                'order_id' => $order->id,
                'status' => 'approved',
            ],
        ]);
    }
}
