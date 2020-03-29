@extends('layout.app')

@section('title', __('Artikelgruppe'))

@section('title_extra')
    @can('article.edit')
        <a href="{{ route('order.create_delivery', $articleGroup) }}" class="btn btn-secondary">@lang('Warenausgang erfassen')</a>
    @endcan
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('article-group.index') }}">@lang('Übersicht')</a>
    </li>
    <li class="active">
        <strong>@lang('Artikelgruppe')</strong>
    </li>
@endsection

@section('content')
<div class="w-full">
    <div class="row">
        <div class="card w-3/5">
            <div class="card-header">
                <div class="flex">
                    <div>@lang('Details')</div>

                    @can('article-group.edit')
                    <dot-menu class="ml-2 pt-1" direction="right">
                        <a href="{{ route('article-group.edit', $articleGroup) }}">@lang('Artikelgruppe bearbeiten')</a>
                    </dot-menu>
                    @endcan
                </div>
            </div>
            <div class="card-content">
                <div class="row">
                    <div class="w-1/4">
                        <div class="form-group">
                            <label class="form-label">@lang('Name')</label>
                            <div class="form-control-static">{{ $articleGroup->name }}</div>
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
                <div class="w-full">@lang('Artikel')</div>
            </div>
            <div class="card-content">
                @foreach($articleGroup->items as $key => $item)
                    <div class="rounded border border-blue-700 p-4 mb-4" id="order-article-{{ $item->id }}">
                        <div class="row">
                            <div class="w-5/12">
                                <div class="form-group">
                                    <label class="form-label">@lang('Artikel') {{ $key+1 }}</label>
                                    <div class="form-control-static">
                                        <a href="{{ route('article.show', $item->article) }}" target="_blank" class="text-sm">{{ $item->article->name }}</a>
                                        <div class="text-xs my-2"># {{ $item->article->article_number }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="w-7/12">
                                <div class="row">
                                    <div class="w-4/12">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Menge für diese Gruppe')</label>
                                            <div class="form-control-static">
                                                {{ $item->quantity }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-4/12">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Aktueller Bestand')</label>
                                            <div class="form-control-static">
                                                {{ $item->article->quantity }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection