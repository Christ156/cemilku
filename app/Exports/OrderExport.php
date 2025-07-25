<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Order::with('orderDetails.collection', 'orderDetails.customize', 'user')
            ->get()
            ->map(function ($order) {
                $products = $order->orderDetails->map(function ($detail) {
                    if ($detail->collection) {
                        return $detail->collection->name . ' (x' . $detail->quantity . ')';
                    } elseif ($detail->customize) {
                        return $detail->customize->name . ' (x' . $detail->quantity . ')';
                    }
                    return '-';
                })->implode(', ');

                return [
                    'Order ID' => $order->id,
                    'User' => $order->user?->name ?? '-',
                    'Payment Method' => $order->payment_method,
                    'Status' => $order->status,
                    'Total Price' => $order->total_price,
                    'Products' => $products,
                    'Created At' => $order->created_at->toDateTimeString(),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'User',
            'Payment Method',
            'Status',
            'Total Price',
            'Products',
            'Created At',
        ];
    }
}

