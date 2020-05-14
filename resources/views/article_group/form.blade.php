@extends('layout.app')

@section('content')
    @yield('form_start')
    <div class="w-full flex">
        <div class="w-1/2 mr-4">
            <div class="card">
                <div class="card-header">
                    <h5>@lang('Details')</h5>
                </div>
                <div class="card-content">
                    {{ Form::bsText('name', null, [], __('Name')) }}
                    {{ Form::bsText('external_article_number', null, [], __('Externe Artikelnummer')) }}
                </div>
            </div>
        </div>
        <div class="w-1/2">
            @yield('secondCol')
        </div>
    </div>

    <div class="w-full flex mt-4">
        <div class="w-1/2 mr-4">
            <div class="card">
                <div class="card-header">
                    <div>@lang('Artikel')</div>
                </div>
                <div class="card-content">
                    <article-group-article-list ref="articleList" id="articleList" :all-articles="{{ json_encode($allArticles) }}" :existing-articles="{{ json_encode($preSetArticles) }}"></article-group-article-list>

                    <select-order-article-modal>{!! $dataTable->table() !!}</select-order-article-modal>
                </div>
            </div>
        </div>
        <div class="w-1/2"></div>
    </div>

    <div class="w-full flex mt-4">
        <div class="w-full">
        <div class="card">
            <div class="card-content">
                <div class="form-group">
                    @yield('submit')
                </div>
            </div>
        </div>
        </div>
    </div>

    {!! Form::close() !!}
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
    <script>
        function selectArticle(id) {
            window.app.$refs.articleList.selectArticle(id);
        }
    </script>
@endpush
