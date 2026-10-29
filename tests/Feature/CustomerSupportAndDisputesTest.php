<?php

namespace Tests\Feature;

use App\Models\CashOnDeliveryReconciliation;
use App\Models\OrderItem;
use App\Models\ParentOrder;
use App\Models\Product;
use App\Models\ReturnItem;
use App\Models\ReturnRequest;
use App\Models\Seller;
use App\Models\SellerOffer;
use App\Models\SellerOrder;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerSupportAndDisputesTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_open_support_ticket(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->post(route('account.tickets.store'), [
            'subject' => 'استفسار بخصوص حالة الشحنة',
            'priority' => 'high',
            'message' => 'مرحباً، أود الاستفسار عن موعد وصول الشحنة إلى مدينة إربد.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('support_tickets', [
            'user_id' => $customer->id,
            'subject' => 'استفسار بخصوص حالة الشحنة',
            'priority' => 'high',
            'status' => 'open',
        ]);
        $this->assertDatabaseHas('support_ticket_messages', [
            'sender_id' => $customer->id,
            'message' => 'مرحباً، أود الاستفسار عن موعد وصول الشحنة إلى مدينة إربد.',
        ]);
    }

    public function test_customer_can_reply_to_ticket(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $ticket = SupportTicket::factory()->create([
            'user_id' => $customer->id,
            'status' => 'in_progress',
        ]);

        $response = $this->actingAs($customer)->post(route('account.tickets.reply', $ticket->id), [
            'message' => 'شكراً لكم على الرد السريع، تم استلام الطلب.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('support_ticket_messages', [
            'support_ticket_id' => $ticket->id,
            'sender_id' => $customer->id,
            'message' => 'شكراً لكم على الرد السريع، تم استلام الطلب.',
        ]);
    }

    public function test_admin_can_reply_and_change_ticket_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $ticket = SupportTicket::factory()->create(['status' => 'open']);

        $response = $this->actingAs($admin)->post(route('admin.support.reply', $ticket->id), [
            'message' => 'تم التواصل مع شركة التوصيل وسيصلك المندوب اليوم.',
            'status' => 'waiting_customer',
        ]);

        $response->assertRedirect();
        $this->assertEquals('waiting_customer', $ticket->fresh()->status);
        $this->assertDatabaseHas('support_ticket_messages', [
            'support_ticket_id' => $ticket->id,
            'sender_id' => $admin->id,
        ]);
    }

    public function test_customer_can_submit_return_request_for_delivered_order(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $seller = Seller::factory()->create();
        $product = Product::factory()->create();
        $offer = SellerOffer::factory()->create([
            'seller_id' => $seller->id,
            'product_id' => $product->id,
            'stock_quantity' => 10,
        ]);

        $parentOrder = ParentOrder::factory()->create([
            'user_id' => $customer->id,
            'status' => 'completed',
        ]);

        $sellerOrder = SellerOrder::factory()->create([
            'parent_order_id' => $parentOrder->id,
            'seller_id' => $seller->id,
            'status' => 'delivered',
            'total_amount' => 30.000,
        ]);

        $orderItem = OrderItem::factory()->create([
            'seller_order_id' => $sellerOrder->id,
            'seller_offer_id' => $offer->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 15.000,
        ]);

        $response = $this->actingAs($customer)->post(route('account.returns.store', $sellerOrder->id), [
            'reason' => 'المنتج به عيب مصنعي في الشاشة',
            'resolution_type' => 'refund',
            'items' => [
                [
                    'order_item_id' => $orderItem->id,
                    'quantity' => 1,
                    'condition_notes' => 'الشاشة مخدوشة',
                ],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('return_requests', [
            'seller_order_id' => $sellerOrder->id,
            'user_id' => $customer->id,
            'status' => 'pending',
            'resolution_type' => 'refund',
        ]);
        $this->assertDatabaseHas('return_items', [
            'order_item_id' => $orderItem->id,
            'quantity' => 1,
        ]);
        $this->assertEquals('return_requested', $sellerOrder->fresh()->status);
    }

    public function test_admin_can_process_return_with_item_restock_and_financial_debit(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);
        $seller = Seller::factory()->create();
        $product = Product::factory()->create();
        $offer = SellerOffer::factory()->create([
            'seller_id' => $seller->id,
            'product_id' => $product->id,
            'stock_quantity' => 5,
        ]);

        $parentOrder = ParentOrder::factory()->create(['user_id' => $customer->id]);
        $sellerOrder = SellerOrder::factory()->create([
            'parent_order_id' => $parentOrder->id,
            'seller_id' => $seller->id,
            'status' => 'delivered',
            'total_amount' => 40.000,
        ]);

        $orderItem = OrderItem::factory()->create([
            'seller_order_id' => $sellerOrder->id,
            'seller_offer_id' => $offer->id,
            'quantity' => 2,
            'unit_price' => 20.000,
        ]);

        $returnRequest = ReturnRequest::create([
            'seller_order_id' => $sellerOrder->id,
            'user_id' => $customer->id,
            'reason' => 'Not as described',
            'resolution_type' => 'refund',
            'status' => 'approved',
        ]);

        ReturnItem::create([
            'return_request_id' => $returnRequest->id,
            'order_item_id' => $orderItem->id,
            'quantity' => 2,
        ]);

        // 1. Mark Received (Restocks items)
        $this->actingAs($admin)->post(route('admin.disputes.process', $returnRequest->id), [
            'action' => 'mark_received',
            'admin_notes' => 'تم استلام المنتج بحالة ممتازة في المستودع',
        ]);

        $this->assertEquals(7, $offer->fresh()->stock_quantity); // 5 + 2 restocked
        $this->assertDatabaseHas('inventory_movements', [
            'seller_offer_id' => $offer->id,
            'type' => 'refund',
            'quantity' => 2,
            'balance_after' => 7,
        ]);

        // 2. Process Refund (Debits from seller and creates refund record)
        $this->actingAs($admin)->post(route('admin.disputes.process', $returnRequest->id), [
            'action' => 'process_refund',
            'refund_amount' => 40.000,
            'admin_notes' => 'تم إعادة المبلغ عبر كليك',
        ]);

        $this->assertEquals('refunded', $returnRequest->fresh()->status);
        $this->assertEquals('refunded', $sellerOrder->fresh()->status);

        $this->assertDatabaseHas('refunds', [
            'parent_order_id' => $parentOrder->id,
            'seller_order_id' => $sellerOrder->id,
            'amount' => 40.000,
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('financial_ledger_entries', [
            'seller_id' => $seller->id,
            'type' => 'refund_deduction',
            'entry_type' => 'debit',
            'amount' => 40.000,
        ]);
    }

    public function test_admin_can_update_cod_reconciliation_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $sellerOrder = SellerOrder::factory()->create();

        $reconciliation = CashOnDeliveryReconciliation::create([
            'seller_order_id' => $sellerOrder->id,
            'courier_name' => 'Aramex Jordan',
            'amount_collected' => 25.000,
            'status' => 'pending_collection',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.finance.cod.update', $reconciliation->id), [
            'status' => 'remitted_to_platform',
            'notes' => 'تم استلام الحوالة من أرامكس',
        ]);

        $response->assertRedirect();
        $this->assertEquals('remitted_to_platform', $reconciliation->fresh()->status);
    }
}
