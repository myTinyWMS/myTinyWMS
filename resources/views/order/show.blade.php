@extends('layout.app')

@section('title', __('Bestellung bei ').optional($order->supplier)->name)

@section('title_extra')
    @can('order.add.delivery')
    <a href="{{ route('order.create_delivery', $order) }}" class="btn btn-secondary">@lang('Wareneingang erfassen')</a>
    @endcan
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('order.index') }}">@lang('Übersicht')</a>
    </li>
    <li class="active">
        <strong>@lang('Bestelldetails')</strong>
    </li>
@endsection

@section('content')
<div class="w-full">
    <div class="row">
        <div class="card w-3/5">
            <div class="card-header">
                <div class="flex">
                    <div>@lang('Bestellung') #{{ $order->internal_order_number }}</div>

                    @can('order.edit')
                    <dot-menu class="ml-2 pt-1">
                        <a href="{{ route('order.edit', $order) }}">@lang('Bestellung bearbeiten')</a>
                    </dot-menu>
                    @endcan
                </div>
            </div>
            <div class="card-content">
                <div class="row">
                    <div class="w-1/4">
                        <div class="form-group">
                            <label class="form-label">@lang('interne Bestellnummer')</label>
                            <div class="form-control-static">{{ $order->internal_order_number }}</div>
                        </div>
                    </div>

                    <div class="w-1/4">
                        <div class="form-group">
                            <label class="form-label">@lang('Bestellnummer des Lieferanten')</label>
                            <div class="form-control-static">{{ $order->external_order_number }}</div>
                        </div>
                    </div>

                    <div class="w-1/2">
                        <div class="form-group">
                            <label class="form-label">@lang('Lieferant')</label>
                            <div class="form-control-static">
                                <a href="{{ route('supplier.show', $order->supplier) }}" target="_blank" title="@lang('Lieferant aufrufen')">{{ optional($order->supplier)->name }}</a>
                                <a href="{{ route('article.index', ['supplier' => $order->supplier->id]) }}" title="@lang('Artikel des Lieferanten aufrufen')"><i class="fa fa-filter"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-2 pt-4 border-t border-gray-300">
                    <div class="w-1/4">
                        <div class="form-group">
                            <label class="form-label">@lang('Bestelldatum')</label>
                            <div class="form-control-static">{{ !empty($order->order_date) ? $order->order_date->format('d.m.Y') : '' }}</div>
                        </div>
                    </div>

                    <div class="w-1/4">

                    </div>

                    <div class="w-1/4">
                        <div class="form-group order-status">
                            <label class="form-label">
                                @lang('Status')

                                @can('order.edit')
                                <dot-menu class="ml-2 normal-case order-change-status">
                                    @foreach(\Mss\Models\Order::getStatusTexts() as $value => $name)
                                        <a href="{{ route('order.change_status', ['order' => $order, 'status' => $value]) }}">{{ $name }}</a>
                                    @endforeach
                                </dot-menu>
                                @endcan
                            </label>
                            <div class="form-control-static">
                                @include('order.status', ['status' => $order->status])
                            </div>
                        </div>
                    </div>

                    <div class="w-1/4">
                        <div class="form-group payment-method">
                            <label class="form-label">
                                @lang('Bezahlmethode')

                                @can('order.edit')
                                <dot-menu class="ml-2 normal-case order-change-payment-method">
                                    <a href="{{ route('order.change_payment_status', ['order' => $order, 'payment_status' => \Mss\Models\Order::PAYMENT_STATUS_UNPAID]) }}">@lang('unbezahlt')</a>
                                    <a href="{{ route('order.change_payment_status', ['order' => $order, 'payment_status' => \Mss\Models\Order::PAYMENT_STATUS_PAID_WITH_PAYPAL]) }}">@lang('Paypal')</a>
                                    <a href="{{ route('order.change_payment_status', ['order' => $order, 'payment_status' => \Mss\Models\Order::PAYMENT_STATUS_PAID_WITH_CREDIT_CARD]) }}">@lang('Kreditkarte')</a>
                                    <a href="{{ route('order.change_payment_status', ['order' => $order, 'payment_status' => \Mss\Models\Order::PAYMENT_STATUS_PAID_WITH_INVOICE]) }}">@lang('Rechnung')</a>
                                    <a href="{{ route('order.change_payment_status', ['order' => $order, 'payment_status' => \Mss\Models\Order::PAYMENT_STATUS_PAID_WITH_AUTOMATIC_DEBIT_TRANSFER]) }}">@lang('Bankeinzug')</a>
                                    <a href="{{ route('order.change_payment_status', ['order' => $order, 'payment_status' => \Mss\Models\Order::PAYMENT_STATUS_PAID_WITH_PRE_PAYMENT]) }}">@lang('Vorkasse')</a>
                                </dot-menu>
                                @endcan
                            </label>
                            <div class="form-control-static">
                                @if($order->payment_status > 0)
                                    <span class="text-success">{{ \Mss\Models\Order::getPaymentStatusText()[$order->payment_status] }}</span>
                                @else
                                    <span class="text-danger">{{ \Mss\Models\Order::getPaymentStatusText()[$order->payment_status] }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-2 pt-4 border-t border-gray-300">
                    <div class="w-full">
                        <div class="row">
                            <div class="w-full">
                                <div class="form-group">
                                    <label class="form-label">@lang('Bemerkungen')</label>
                                    <div class="form-control-static">{{ $order->notes ?: '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-2/5 ml-4">
            <collapse title="@lang('Logbuch')">
                @include('components.audit_list', $audits)
            </collapse>
        </div>
    </div>
    <div class="row mt-4">
        <div class="card w-3/5">
            <div class="card-header flex">
                <div class="w-5/12">@lang('Artikel')</div>
                <div class="w-7/12 flex justify-end">
                    @can('order.edit')
                    <div class="mr-2">
                        {!! Form::open(['method' => 'post', 'route' => ['order.all_items_confirmation_received', $order]]) !!}
                        <button type="submit" class="btn  btn-secondary border-green-600 text-green-600"><z icon="checkmark" class="fill-current w-3 h-3 inline-block"></z> @lang('alle Auftragsbestätigungen erhalten')</button>
                        {!! Form::close() !!}
                    </div>
                    <div>
                        <invoice-status-change-all :order="{{ $order->id }}" :article-has-new-price="{{ $hasOneArticleWithNewPrice ? 'true' : 'false' }}"></invoice-status-change-all>
                    </div>
                    @endcan
                </div>
            </div>
            <div class="card-content">
                @php ($total = 0)
                @foreach($order->items as $key => $item)
                    @php ($total += ($item->quantity * $item->price))
                    @php ($articleHasNewPrice = ($item->article->getCurrentSupplierArticle()->price / 100) != $item->price)
                    <div class="rounded border border-blue-700 p-4 mb-4" id="order-article-{{ $item->id }}">
                        <div class="row">
                            <div class="w-5/12">
                                <div class="form-group">
                                    <label class="form-label">@lang('Artikel') {{ $key+1 }}</label>
                                    <div class="form-control-static">
                                        <a href="{{ route('article.show', $item->article) }}" target="_blank" class="text-sm">{{ $item->article->name }}</a>
                                        <div class="text-xs my-2"># {{ $item->article->article_number }}</div>

                                        @if ($articleHasNewPrice)
                                            <span class="font-semibold text-red-500 text-xs">@lang('Achtung, aktueller Artikelpreis weicht von Preis aus dieser Bestellung ab!')</span>
                                            <br>
                                        @endif
                                        @if ($item->article->getCurrentSupplierArticle()->supplier_id != $order->supplier_id)
                                            <span class="font-semibold text-red-500 text-xs">@lang('Der Artikel hat inzwischen einen anderen Lieferanten!')</span>
                                            <br>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="w-7/12">
                                <div class="row">
                                    <div class="w-4/12">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Preis netto je Einheit')</label>
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
                                            <label class="form-label">@lang('bestellte Menge')</label>
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
                                            <label class="form-label">@lang('gelieferte Menge')</label>
                                            <div class="form-control-static flex @if($item->getQuantityDelivered() < $item->quantity) text-orange-500 @elseif($item->getQuantityDelivered() > $item->quantity) text-red-500 @else text-green-500 @endif">
                                                <div class="flex-1">{{ $item->getQuantityDelivered() }}</div>

                                                @if($item->getQuantityDelivered() == $item->quantity)
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
                                        <div class="form-group confirmation-status">
                                            <label class="form-label">
                                                @lang('Auftragsbestätigung')

                                                @can('order.edit')
                                                <dot-menu class="ml-2 normal-case">
                                                    <a href="{{ route('order.item_confirmation_received', ['orderitem' => $item, 'status' => 1]) }}">@lang('erhalten')</a>
                                                    <a href="{{ route('order.item_confirmation_received', ['orderitem' => $item, 'status' => 0]) }}">@lang('nicht erhalten')</a>
                                                </dot-menu>
                                                @endcan
                                            </label>
                                            <div class="form-control-static">
                                                @if($item->confirmation_received)
                                                    <span class="w-3 h-3 inline-block align-middle rounded-full bg-green-500"></span>
                                                    <span class="text-green-600 font-semibold align-top">@lang('erhalten')</span>
                                                @else
                                                    <span class="w-3 h-3 inline-block align-middle rounded-full bg-red-500"></span>
                                                    <span class="text-red-600 font-semibold align-top">@lang('nicht erhalten')</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="w-4/12">
                                        <div class="form-group invoice-status">
                                            <div class="flex">
                                                <label class="form-label">@lang('Rechnung')</label>
                                                @can('order.edit')
                                                <invoice-status-change :item="{{ $item }}" :article-has-new-price="{{ $articleHasNewPrice ? 1 : 0 }}" invoice-notification-users-count="{{ $invoiceNotificationUsersCount }}"></invoice-status-change>
                                                @endcan
                                            </div>
                                            <div class="form-control-static">
                                                @if($item->invoice_received === \Mss\Models\OrderItem::INVOICE_STATUS_RECEIVED)
                                                    <span class="w-3 h-3 inline-block align-middle rounded-full bg-green-500"></span>
                                                    <span class="text-green-600 font-semibold align-top">@lang('erhalten')</span>
                                                @elseif($item->invoice_received === \Mss\Models\OrderItem::INVOICE_STATUS_CHECK)
                                                    <span class="w-3 h-3 inline-block align-middle rounded-full bg-orange-500"></span>
                                                    <span class="text-orange-600 font-semibold align-top">@lang('in Prüfung')</span>
                                                @else
                                                    <span class="w-3 h-3 inline-block align-middle rounded-full bg-red-500"></span>
                                                    <span class="text-red-600 font-semibold align-top">@lang('nicht erhalten')</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="w-4/12">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Liefertermin')</label>
                                            <div class="form-control-static">
                                                {{ !empty($item->expected_delivery) ? $item->expected_delivery->format('d.m.Y') : '' }}
                                                @if($item->expected_delivery && $item->expected_delivery < today() && $item->getQuantityDelivered() < $item->quantity)
                                                    <span class="text-red-600 font-bold text-sm">@lang('überfällig')!</span>
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
        <div class="card w-2/5 ml-4">
            <div class="card-header flex">
                <div class="w-5/12">@lang('Lieferungen')</div>
            </div>
            <div class="card-content">
                @foreach($order->deliveries->sortByDesc('delivery_date') as $delivery)
                    <div class="rounded border border-blue-700 p-4 mb-4">
                        <div class="row">
                            <div class="w-4/12">
                                <div class="form-group">
                                    <label class="form-label">@lang('Lieferdatum')</label>
                                    <div class="form-control-static">{{ $delivery->delivery_date ? $delivery->delivery_date->format('d.m.Y') : '-' }}</div>
                                </div>
                            </div>
                            <div class="w-8/12">
                                <div class="form-group">
                                    <label class="form-label">@lang('Bemerkung')</label>
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
                                        <th>@lang('Artikel')</th>
                                        <th>@lang('Menge')</th>
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
                <div class="flex-1">@lang('Kommunikation')</div>
                @can('ordermessage.create')
                <a href="{{ route('order.message_new', $order) }}" class="btn btn-secondary">@lang('Neue Nachricht')</a>
                @endcan
            </div>
            <div class="card-content">
                @can('ordermessage.view')
                @include('order.communications')
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection