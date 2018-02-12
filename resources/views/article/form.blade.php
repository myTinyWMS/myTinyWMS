@extends('layout.app')

@section('content')
    @if (count($errors) > 0)
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-4">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Details</h5>
                </div>
                <div class="ibox-content">
                    @yield('form_start')

                    {{ Form::bsTextarea('name', null, ['rows' => 2] , 'Name') }}
                    {{ Form::bsSelect('tags', $article->tags->pluck('id'), \Mss\Models\Tag::orderedByName()->pluck('name', 'id'), 'Tags', ['multiple' => 'multiple', 'name' => 'tags[]']) }}

                    <div class="form-group">
                        {!! Form::label('category', 'Kategorie', ['class' => 'control-label']) !!}
                        {!! Form::select('category', \Mss\Models\Category::orderedByName()->pluck('name', 'id'), $article->category->id, ['class' => 'form-control', 'name' => 'category', 'disabled' => 'disabled']) !!}
                        <div class="checkbox checkbox-danger">
                            <input type="checkbox" id="enableChangeCategory" name="changeCategory" value="1" />
                            <label for="enableChangeCategory">
                                 Kategorie ändern
                            </label>
                        </div>
                        <span class="help-block m-b-none text-danger hidden" id="changeCategoryWarning">Beim Ändern der Kategorie wird eine neue Artikel Nummer vergeben!</span>
                    </div>

                    {{ Form::bsSelect('unit', $article->unit_id, \Mss\Models\Unit::pluck('name', 'id'),  'Einheit') }}
                    {{ Form::bsText('sort_id', null, [], 'Sortierung') }}
                    {{ Form::bsText('quantity', null, [], 'Bestand') }}
                    {{ Form::bsText('min_quantity', null, [], 'Mindestbestand') }}
                    {{ Form::bsText('usage_quantity', null, [], 'Verbrauch') }}
                    {{ Form::bsText('issue_quantity', null, [], 'Entnahmemenge') }}
                    {{ Form::bsCheckbox('inventory', null, 'Inventur', $article->inventory, []) }}
                    {{ Form::bsTextarea('notes', null, [], 'Bemerkungen') }}


                    <div class="form-group">
                        @yield('submit')
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        @yield('secondCol')
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#enableChangeCategory').click(function () {
            if($(this).is(':checked')) {
                $('#changeCategoryWarning').removeClass('hidden');
                $("#category").prop('disabled', false);
            } else {
                $('#changeCategoryWarning').addClass('hidden');
                $("#category").prop('disabled', true);
            }
        });
        // changeCategoryWarning
        $("#tags").select2({
            tags: true,
            tokenSeparators: [',', ' '],
            theme: "bootstrap",
            createTag: function (params) {
                var term = $.trim(params.term);

                if (term === '') {
                    return null;
                }

                return {
                    id: 'newTag_'+term,
                    text: term
                }
            }
        });

        $("#category").select2({
            theme: "bootstrap"
        });
    });
</script>
@endpush
