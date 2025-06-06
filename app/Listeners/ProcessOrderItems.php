<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Events\OrderUpdated;
use App\Models\Order;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ProcessOrderItems implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct(private ProductRepository $repo)
    {
        Log::info('ProcessOrderItems constructed');
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreated|OrderUpdated $event): void
    {
        Log::info('ProcessOrderItems handle', [
            'event' => get_class($event),
            'order_id' => $event->order->id,
        ]);

        $teamId = $event->order->team_id;

        if ($event instanceof OrderCreated) {
            $this->handleOrderCreation($event, $teamId);
        } else {
            $this->handleOrderUpdate($event, $teamId);
        }
    }

    private function handleOrderCreation(OrderCreated $event, int $teamId) : void
    {
        if ($event->order->items()->exists()) {
            Log::warning('Duplicate OrderCreated caught', [
                'order_id' => $event->order->id,
            ]);
            return;
        }

        $totalCost = 0;
        $totalPrice = 0;
        // batch inserting order items
        $orderItems = [];

        foreach ($event->items as $item) {
            $product = $this->repo->findByTeam($teamId, $item['product_id']);
            $product->decrement('quantity', $item['quantity']);

            $orderItems[] = [
                'order_id' => $event->order->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'unit_cost' => $product->cost,
                'unit_price' => $product->price,
            ];
            // calculate totals
            $totalCost += $item['quantity'] * ($product->cost ?? 0);
            $totalPrice += $item['quantity'] * ($product->price ?? 0);
        }

        $event->order->items()->createMany($orderItems);

        Log::info('Order items created', ['totalCost' => $totalCost, 'totalPrice' => $totalPrice, 'orderItems' => $orderItems]);
        $this->updateOrderTotals($event->order, $totalCost, $totalPrice);
    }

    private function handleOrderUpdate(OrderUpdated $event, int $teamId) : void
    {
        echo 'handleOrderUpdate';exit;
        $totalCost = 0;
        $totalPrice = 0;
        // batch inserting order items
        $orderItems = [];
        // get new and previous items
        $newItemsMap = collect($event->items)->keyBy('product_id');
        $previousItemsMap = collect($event->previousItems)->keyBy('product_id');
        // update order items
        foreach ($event->items as $item) {
            $productId = $item['product_id'];
            $newQuantity = $item['quantity'];
            $previousItem = $previousItemsMap->get($productId);

            $product = $this->repo->findByTeam($teamId, $productId);
            $totalCost += $newQuantity * ($product->cost ?? 0);
            $totalPrice += $newQuantity * ($product->price ?? 0);

            if ($previousItem) {
                $previousQuantity = $previousItem['quantity'];
                $quantityDiff = $newQuantity - $previousQuantity;
                // stock adjustment, if new quantity is greater than previous, decrement, if less, increment
                if ($quantityDiff > 0) {
                    $product->decrement('quantity', $quantityDiff);
                } else if ($quantityDiff < 0) {
                    $product->increment('quantity', abs($quantityDiff));
                }
                // update order item
                $event->order->items()->where('product_id', $productId)->update([
                    'quantity' => $newQuantity,
                    'unit_cost' => $product->cost,
                    'unit_price' => $product->price,
                ]);
            } else {
                $orderItems[] = [
                    'order_id' => $event->order->id,
                    'product_id' => $product->id,
                    'quantity' => $newQuantity,
                    'unit_cost' => $product->cost,
                    'unit_price' => $product->price,
                ];
            }
        }
        $event->order->items()->createMany($orderItems);
        // delete previous items
        foreach ($event->previousItems as $item) {
            $productId = $item['product_id'];

            if (!$newItemsMap->has($productId)) {
                // return stock to inventory
                $product = $this->repo->findByTeam($teamId, $productId);
                $product->increment('quantity', $item['quantity']);
                // delete order item
                $event->order->items()->where('product_id', $productId)->delete();
            }
        }

        $this->updateOrderTotals($event->order, $totalCost, $totalPrice);
    }

    private function updateOrderTotals(Order $order, int $totalCost, int $totalPrice) : void
    {
        $order->total_cost = $totalCost;
        $order->total_price = $totalPrice;
        $order->save();
    }

    public function failed($event, $exception): void
    {
        Log::error('Order processing failed', [
            'order_id' => $event->order->id,
            'error' => $exception->getMessage()
        ]);
    }
}
