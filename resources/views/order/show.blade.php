@extends('layout.app')

@section('title', 'Bestellung bei '.optional($order->supplier)->name)

@section('title_extra')
    <a href="{{ route('order.create_delivery', $order) }}" class="btn btn-primary btn-sm pull-right">Wareneingang erfassen</a>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('order.index') }}">Übersicht</a>
    </li>
    <li class="active">
        <strong>Bestelldetails</strong>
    </li>
@endsection

@section('content')
<div class="w-full">
    <div class="row">
        <div class="card w-2/3">
            <div class="card-header">
                <div class="flex">
                    <div class="flex-1">Bestellung #{{ $order->internal_order_number }}</div>

                    <dot-menu class="ml-2">
                        <a href="{{ route('order.edit', $order) }}" class="btn-link">bearbeiten</a>
                    </dot-menu>
                </div>
            </div>
            <div class="card-content">
                <div class="row">
                    <div class="w-1/4">
                        <div class="form-group">
                            <label class="form-label">interne Bestellnummer</label>
                            <div class="form-control-static">{{ $order->internal_order_number }}</div>
                        </div>
    {{--                        <h2>{{ $order->total_cost }}</h2>--}}
                    </div>

                    <div class="w-1/4">
                        <div class="form-group">
                            <label class="form-label">Bestellnummer des Lieferanten</label>
                            <div class="form-control-static">{{ $order->external_order_number }}</div>
                        </div>
                    </div>

                    <div class="w-1/2">
                        <div class="form-group">
                            <label class="form-label">Lieferant</label>
                            <div class="form-control-static">
                                <a href="{{ route('supplier.show', $order->supplier) }}" target="_blank" title="Lieferant aufrufen">{{ optional($order->supplier)->name }}</a>
                                <a href="{{ route('article.index', ['supplier' => $order->supplier->id]) }}" title="Artikel des Lieferanten aufrufen"><i class="fa fa-filter"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-2 pt-4 border-t border-gray-300">
                    <div class="w-1/4">
                        <div class="form-group">
                            <label class="form-label">Bestelldatum</label>
                            <div class="form-control-static">{{ !empty($order->order_date) ? $order->order_date->format('d.m.Y') : '' }}</div>
                        </div>
                    </div>

                    <div class="w-1/4">

                    </div>

                    <div class="w-1/4">
                        <div class="form-group">
                            <label class="form-label">
                                Status
                                <dot-menu class="ml-2 normal-case">
                                    @foreach(\Mss\Models\Order::STATUS_TEXTS as $value => $name)
                                        <a href="{{ route('order.change_status', ['order' => $order, 'status' => $value]) }}">{{ $name }}</a>
                                    @endforeach
                                </dot-menu>
                            </label>
                            <div class="form-control-static">
                                @include('order.status', ['status' => $order->status])
                            </div>
                        </div>
                    </div>

                    <div class="w-1/4">
                        <div class="form-group">
                            <label class="form-label">
                                Bezahlmethode
                                <dot-menu class="ml-2 normal-case">
                                    <a href="{{ route('order.change_payment_status', ['order' => $order, 'payment_status' => \Mss\Models\Order::PAYMENT_STATUS_UNPAID]) }}">unbezahlt</a>
                                    <a href="{{ route('order.change_payment_status', ['order' => $order, 'payment_status' => \Mss\Models\Order::PAYMENT_STATUS_PAID_WITH_PAYPAL]) }}">Paypal</a>
                                    <a href="{{ route('order.change_payment_status', ['order' => $order, 'payment_status' => \Mss\Models\Order::PAYMENT_STATUS_PAID_WITH_CREDIT_CARD]) }}">Kreditkarte</a>
                                    <a href="{{ route('order.change_payment_status', ['order' => $order, 'payment_status' => \Mss\Models\Order::PAYMENT_STATUS_PAID_WITH_INVOICE]) }}">Rechnung</a>
                                    <a href="{{ route('order.change_payment_status', ['order' => $order, 'payment_status' => \Mss\Models\Order::PAYMENT_STATUS_PAID_WITH_AUTOMATIC_DEBIT_TRANSFER]) }}">Bankeinzug</a>
                                </dot-menu>
                            </label>
                            <div class="form-control-static">
                                @if($order->payment_status > 0)
                                    <span class="text-success">{{ \Mss\Models\Order::PAYMENT_STATUS_TEXT[$order->payment_status] }}</span>
                                @else
                                    <span class="text-danger">{{ \Mss\Models\Order::PAYMENT_STATUS_TEXT[$order->payment_status] }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-2 pt-4 border-t border-gray-300">
                    <div class="w-full">
                        <div class="row">
                            <div class="w-1/2">
                                <div class="form-group">
                                    <label class="form-label">Bemerkungen</label>
                                    <div class="form-control-static">{{ $order->notes ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="w-1/4">
                                {!! Form::open(['method' => 'post', 'route' => ['order.all_items_confirmation_received', $order]]) !!}
                                <button type="submit" class="btn  btn-secondary border-green-600 text-green-600"><z icon="checkmark" class="fill-current w-3 h-3 inline-block"></z> alle Auftragsbestätigungen erhalten</button>
                                {!! Form::close() !!}
                            </div>
                            <div class="w-1/4">
                                <invoice-status-change-all :order="{{ $order->id }}" :article-has-new-price="{{ $hasOneArticleWithNewPrice }}"></invoice-status-change-all>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-1/3 ml-4">
            <collapse title="Logbuch">
                @include('components.audit_list', $audits)
            </collapse>
        </div>
    </div>
    <div class="row mt-4">
        <div class="card w-2/3">
            <div class="card-header flex">
                <div class="w-5/12">Artikel</div>
            </div>
            <div class="card-content">
                @php ($total = 0)
                @foreach($order->items as $key => $item)
                    @php ($total += ($item->quantity * $item->price))
                    @php ($articleHasNewPrice = ($item->article->getCurrentSupplierArticle()->price / 100) != $item->price)
                    <div class="rounded border border-blue-700 p-4 mb-4">
                        <div class="row">
                            <div class="w-5/12">
                                <div class="form-group">
                                    <label class="form-label">Artikel {{ $key+1 }}</label>
                                    <div class="form-control-static">
                                        <a href="{{ route('article.show', $item->article) }}" target="_blank">{{ $item->article->name }}</a>
                                        <div class="text-xs my-2"># {{ $item->article->article_number }}</div>

                                        @if ($articleHasNewPrice)
                                            <span class="font-semibold text-red-500 text-sm">Achtung, aktueller Artikelpreis weicht von Preis aus dieser Bestellung ab!</span>
                                            <br>
                                        @endif
                                        @if ($item->article->getCurrentSupplierArticle()->supplier_id != $order->supplier_id)
                                            <span class="font-semibold text-red-500 text-sm">Der Artikel hat inzwischen einen anderen Lieferanten!</span>
                                            <br>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="w-7/12">
                                <div class="row">
                                    <div class="w-4/12">
                                        <div class="form-group">
                                            <label class="form-label">Preis netto je Einheit</label>
                                            <div class="form-control-static">
                                                {!! formatPrice($item->price)  !!}
                                                @if ($item->quantity > 1)
                                                    <div class="text-xs my-2">&sum; {!! formatPrice($item->price * $item->quantity) !!}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="w-4/12">
                                        <div class="form-group">
                                            <label class="form-label">bestellte Menge</label>
                                            <div class="form-control-static">
                                                {{ $item->quantity }}
                                                @if ($item->quantity > 1)
                                                    <br>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="w-4/12">
                                        <div class="form-group">
                                            <label class="form-label">gelieferte Menge</label>
                                            <div class="form-control-static flex @if($item->getQuantityDelivered() < $item->quantity) text-orange-500 @elseif($item->getQuantityDelivered() > $item->quantity) text-red-500 @else text-green-500 @endif">
                                                <div class="flex-1">{{ $item->getQuantityDelivered() }}</div>

                                                @if($item->getQuantityDelivered() == $item->quantity or 1)
                                                    <div><z icon="checkmark-outline" class="w-8 h-8 fill-current" title="komplett geliefert"></z></div>
                                                @elseif($item->getQuantityDelivered() > $item->quantity)
                                                    <div><z icon="exclamation-outline" class="w-8 h-8 fill-current" title="zu viel geliefert"></z></div>
                                                @endif

                                                @if ($item->quantity > 1)
                                                    <br>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="w-4/12">
                                        <div class="form-group">
                                            <label class="form-label">
                                                Auftragsbestätigung

                                                <dot-menu class="ml-2 normal-case">
                                                    <a href="{{ route('order.item_confirmation_received', ['orderitem' => $item, 'status' => 1]) }}">erhalten</a>
                                                    <a href="{{ route('order.item_confirmation_received', ['orderitem' => $item, 'status' => 0]) }}">nicht erhalten</a>
                                                </dot-menu>
                                            </label>
                                            <div class="form-control-static">
                                                @if($item->confirmation_received)
                                                    <span class="w-3 h-3 inline-block align-middle rounded-full bg-green-500"></span>
                                                    <span class="text-green-600 font-semibold align-top">erhalten</span>
                                                @else
                                                    <span class="w-3 h-3 inline-block align-middle rounded-full bg-red-500"></span>
                                                    <span class="text-red-600 font-semibold align-top">nicht erhalten</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="w-4/12">
                                        <div class="form-group">
                                            <label class="form-label">
                                                Rechnung

                                                <invoice-status-change :item="{{ $item }}" :article-has-new-price="{{ $articleHasNewPrice ? 1 : 0 }}"></invoice-status-change>
                                            </label>
                                            <div class="form-control-static">
                                                @if($item->invoice_received === \Mss\Models\OrderItem::INVOICE_STATUS_RECEIVED)
                                                    <span class="w-3 h-3 inline-block align-middle rounded-full bg-green-500"></span>
                                                    <span class="text-green-600 font-semibold align-top">erhalten</span>
                                                @elseif($item->invoice_received === \Mss\Models\OrderItem::INVOICE_STATUS_CHECK)
                                                    <span class="w-3 h-3 inline-block align-middle rounded-full bg-orange-500"></span>
                                                    <span class="text-orange-600 font-semibold align-top">in Prüfung</span>
                                                @else
                                                    <span class="w-3 h-3 inline-block align-middle rounded-full bg-red-500"></span>
                                                    <span class="text-red-600 font-semibold align-top">nicht erhalten</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="w-4/12">
                                        <div class="form-group">
                                            <label class="form-label">Liefertermin</label>
                                            <div class="form-control-static">
                                                {{ !empty($item->expected_delivery) ? $item->expected_delivery->format('d.m.Y') : '' }}
                                                @if($item->expected_delivery && $item->expected_delivery < today() && $item->getQuantityDelivered() < $item->quantity)
                                                    <span class="text-red-600 font-bold text-sm">überfällig!</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="row">
                    <div class="w-5/12"></div>
                    <div class="w-7/12">
                        <span class="border-t-2 border-gray-800 pt-1">&sum; {!! formatPrice($total) !!}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card w-1/3 ml-4">
            <div class="card-header flex">
                <div class="w-5/12">Lieferungen</div>
            </div>
            <div class="card-content">
                @foreach($order->deliveries->sortByDesc('delivery_date') as $delivery)
                    <div class="rounded border border-blue-700 p-4 mb-4">
                        <div class="row">
                            <div class="w-4/12">
                                <div class="form-group">
                                    <label class="form-label">Lieferdatum</label>
                                    <div class="form-control-static">{{ $delivery->delivery_date ? $delivery->delivery_date->format('d.m.Y') : '-' }}</div>
                                </div>
                            </div>
                            <div class="w-8/12">
                                <div class="form-group">
                                    <label class="form-label">Bemerkung</label>
                                    <div class="form-control-static">{{ $delivery->notes }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="w-full">
                                <table class="table table-condensed table-border">
                                    <thead>
                                    <tr>
                                        <th style="width: 40px">#</th>
                                        <th>Artikel</th>
                                        <th>Menge</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($delivery->items as $item)
                                        <tr>
                                            <td>{{ $item->article->article_number }}</td>
                                            <td>
                                                <a href="{{ route('article.show', $item->article) }}" target="_blank">{{ $item->article->name }}</a>
                                            </td>
                                            <td>{{ $item->quantity }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="card w-full">
            <div class="card-header flex">
                <div class="flex-1">Kommunikation</div>
                <a href="{{ route('order.message_new', $order) }}" class="btn btn-primary">Neue Nachricht</a>
            </div>
            <div class="card-content">
                @include('order.communications')
            </div>
        </div>
    </div>
</div>
@endsection