<div class="flex">
    <div>{{ $supplier_name }}</div>
    <div class="flex-1 text-right pr-4">
        <a href="{{ route('article.index', ['supplier' => $current_supplier_id]) }}"><i class="fa fa-filter"></i></a>
    </div>
</div>