@extends('customer.layouts.master')

@section('title', 'Pesanan Berhasil')

@section('content')
    <!-- Single Page Header start -->
    <div class="container-fluid py-5 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="receipt border p-4 bg-white shadow" style="width: 450px; margin-top: 5rem;">
            <h5 class="text-center mb-2">Pesanan berhasil dibuat!</h5>
            @if ($order->payment_method == 'tunai' && $order->status == 'pending')
                <p class="text-center"><span class="badge bg-danger">Menunggu Pembayaran</span></p>
            @elseif ($order->payment_method == 'qris' && $order->status == 'pending')
                <p class="text-center"><span class="badge bg-success">Menunggu konfirmasi pembayaran</span></p>
            @else
                <p class="text-center"><span class="badge bg-success">Pesanan berhasil, pesanan segera diproses</span></p>
            @endif
            <hr>
            <h4 class="fw-bold text-center">Kode bayar: <br> <span class="text-primary">{{ $order->order_code }}</span></h4>
            <hr>
            <h5 class="mb-3 text-center">Detai Pesanan</h5>
            <table class="table table-borderless">
                <tbody>
                    @foreach ($orderItems as $orderItem)
                        <tr>
                            <td>{{ Str::limit($orderItem->item->name, 25) }} ({{ $orderItem->quantity }})</td>
                            <td class="text-end">{{ 'Rp' . number_format($orderItem->price, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <table class="table table-borderless">
                <tbody>
                    <tr>
                        <th class="text-uppercase">Subtotal</th>
                        <td class="text-end">{{ 'Rp' . number_format($order->subtotal, 0, ',', '.') }}</td>
                    <tr>
                        <td>Pajak (10%)</td>
                        <td class="text-end">{{ 'Rp' . number_format($order->tax, 0, ',', '.') }}</td>
                    </tr>
                    </tr>
                    <tr>
                        <td class="">Total</td>
                        <td class="text-end fw-bold">{{ 'Rp' . number_format($order->grand_total, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
            @if ($order->payment_method == 'tunai')
                <p class="small text-center">Tunjukan kode bayar ini ke kasir untuk menyelesaikan pembayaran. Jangan lupa
                    senyum ya!</p>
            @elseif ($order->payment_method == 'qris')
                <p class="small text-center">Yeay! Pembayaran berhasil. Duduk manis ya, pesanan kamu lagi diproses</p>
            @endif
            <hr>
            <a href="{{ route('menu') }}" class="btn btn-primary w-100">Kembali ke menu</a>
        </div>
    </div>
    <!-- Single Page Header End -->
@endsection
