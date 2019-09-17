<div class="w-full fixed pin-l pin-y z-20">
    @if(env('APP_DEBUG'))
    <div class="fixed top-0 keft-0 m-2 p-3 text-xs font-mono text-white h-8 w-8 rounded-full flex items-center justify-center bg-gray-700 sm:bg-pink-500 md:bg-orange-500 lg:bg-green-500 xl:bg-blue-500 2xl:bg-red-500 3xl:bg-purple-500 4xl:bg-teal-500 print:hidden">
        <div class="block  sm:hidden md:hidden lg:hidden xl:hidden 2xl:hidden 3xl:hidden 4xl:hidden">al</div>
        <div class="hidden sm:block  md:hidden lg:hidden xl:hidden 2xl:hidden 3xl:hidden 4xl:hidden">sm</div>
        <div class="hidden sm:hidden md:block  lg:hidden xl:hidden 2xl:hidden 3xl:hidden 4xl:hidden">md</div>
        <div class="hidden sm:hidden md:hidden lg:block  xl:hidden 2xl:hidden 3xl:hidden 4xl:hidden">lg</div>
        <div class="hidden sm:hidden md:hidden lg:hidden xl:block 2xl:hidden 3xl:hidden 4xl:hidden">xl</div>
        <div class="hidden sm:hidden md:hidden lg:hidden xl:hidden 2xl:block 3xl:hidden 4xl:hidden">2xl</div>
        <div class="hidden sm:hidden md:hidden lg:hidden xl:hidden 2xl:hidden 3xl:block 4xl:hidden">3xl</div>
        <div class="hidden sm:hidden md:hidden lg:hidden xl:hidden 2xl:hidden 3xl:hidden 4xl:block">4xl</div>
    </div>
    @endif

    {{-- Row 1 --}}
    <div class="bg-white w-full h-header px-12">
        <div class="text-blue-700 relative pt-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-48 fill-current xl:mx-auto lg:left-0" viewBox="0 0 135.67 34.54"><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M44.86,13.37l.13,0v-1c0-.28,0-.49,0-.64a.91.91,0,0,1,.09-.37.42.42,0,0,1,.19-.22.3.3,0,0,1,.28,0,.75.75,0,0,1,.4.36,1.59,1.59,0,0,1,.15.45,5.7,5.7,0,0,1,0,.67v1.15l.42.14a.66.66,0,0,1,.38.29,1,1,0,0,1,.13.48c0,.23-.07.37-.19.42a.75.75,0,0,1-.52,0L46.13,15v3.12c0,.26,0,.47,0,.61a1.39,1.39,0,0,0,.1.39.49.49,0,0,0,.27.22l.28,0a.7.7,0,0,1,.27,0,.51.51,0,0,1,.25.24,1,1,0,0,1,.11.45.48.48,0,0,1-.31.52,1.3,1.3,0,0,1-.87-.07,1.69,1.69,0,0,1-.82-.54,2,2,0,0,1-.36-.86A8.94,8.94,0,0,1,45,17.87V14.63l-.15-.05a.61.61,0,0,1-.37-.3.86.86,0,0,1-.13-.48.49.49,0,0,1,.14-.39A.37.37,0,0,1,44.86,13.37Zm3.58.22a.78.78,0,0,1-.41-.36,1.17,1.17,0,0,1-.17-.67A.78.78,0,0,1,48,12a.4.4,0,0,1,.41-.1.75.75,0,0,1,.4.34,1.17,1.17,0,0,1,.18.67.76.76,0,0,1-.17.56A.39.39,0,0,1,48.44,13.59ZM49,15.53v4.92a1.18,1.18,0,0,1-.16.72.36.36,0,0,1-.43.12.74.74,0,0,1-.41-.41,1.9,1.9,0,0,1-.16-.82V15.2a1.26,1.26,0,0,1,.16-.71.37.37,0,0,1,.42-.12.79.79,0,0,1,.42.39A1.65,1.65,0,0,1,49,15.53Zm2.27.71v.21a1.69,1.69,0,0,1,.68-.66,1.24,1.24,0,0,1,.85,0,1.91,1.91,0,0,1,.84.57,2.66,2.66,0,0,1,.55,1,3.06,3.06,0,0,1,.15.73c0,.26,0,.57,0,.95v3.21a1.19,1.19,0,0,1-.17.72.36.36,0,0,1-.42.13.76.76,0,0,1-.43-.42,1.83,1.83,0,0,1-.16-.83V19A4.55,4.55,0,0,0,53,17.64a1,1,0,0,0-.65-.66.58.58,0,0,0-.57.09,1.09,1.09,0,0,0-.38.62,7,7,0,0,0-.09,1.4v2.15a1.15,1.15,0,0,1-.17.72.35.35,0,0,1-.43.12.72.72,0,0,1-.41-.41,1.78,1.78,0,0,1-.17-.82V15.91a1.16,1.16,0,0,1,.15-.68.32.32,0,0,1,.4-.11.64.64,0,0,1,.28.19,1.19,1.19,0,0,1,.2.39A1.81,1.81,0,0,1,51.28,16.24Zm5.39,8.05.11-.35L55.32,18.1a3.41,3.41,0,0,1-.14-.72.92.92,0,0,1,.08-.38.52.52,0,0,1,.22-.24.41.41,0,0,1,.29,0,.67.67,0,0,1,.39.37,4.25,4.25,0,0,1,.23.75l1,4.6,1-3.65q.12-.45.21-.69a.61.61,0,0,1,.2-.31.38.38,0,0,1,.3,0,.64.64,0,0,1,.26.19,1.33,1.33,0,0,1,.2.35,1.39,1.39,0,0,1,.06.4c0,.08,0,.18-.05.32s-.06.28-.1.43l-1.59,5.4a6.83,6.83,0,0,1-.4,1.1,1,1,0,0,1-.52.5,1.25,1.25,0,0,1-.86-.05,2.16,2.16,0,0,1-.79-.44.94.94,0,0,1-.26-.69.5.5,0,0,1,.13-.41.33.33,0,0,1,.37,0,1.18,1.18,0,0,1,.19.1l.19.11a.55.55,0,0,0,.33,0,.43.43,0,0,0,.21-.24A3,3,0,0,0,56.67,24.29Zm.67,10.25a3.94,3.94,0,0,1-1.59-.33l-12.7-5.47a3.68,3.68,0,0,1-2.28-3.37V8.29a3.72,3.72,0,0,1,2.5-3.46l4.5-1.63a1,1,0,0,1,.68,1.88L44,6.71a1.7,1.7,0,0,0-1.18,1.58V25.37a1.71,1.71,0,0,0,1.07,1.54l12.7,5.47a2.09,2.09,0,0,0,1.59,0l8-3.36a1,1,0,1,1,.78,1.84l-8,3.36A4,4,0,0,1,57.34,34.54Zm16.9-12.08V8.3a3.74,3.74,0,0,0-2.53-3.47l-5.38-1.9a1,1,0,1,0-.67,1.89l5.39,1.9A1.72,1.72,0,0,1,72.24,8.3V22.46a1,1,0,0,0,2,0ZM53,3.43l3.63-1.31a2,2,0,0,1,1.36,0l2.55.9a1,1,0,1,0,.67-1.89L58.68.23A4,4,0,0,0,56,.24L52.35,1.55a1,1,0,0,0,.33,1.94A1,1,0,0,0,53,3.43ZM3.19,9.28h0v2.1h.06A4.56,4.56,0,0,1,4.69,9.69a4.32,4.32,0,0,1,2.67-.75q3.17,0,4.2,2.5a4.81,4.81,0,0,1,1.82-1.89A5.25,5.25,0,0,1,16,8.94a5.19,5.19,0,0,1,2.27.45,4,4,0,0,1,1.49,1.23,5.07,5.07,0,0,1,.83,1.84,9.52,9.52,0,0,1,.25,2.25v8H17.47V15.1a6.67,6.67,0,0,0-.11-1.19,2.86,2.86,0,0,0-.39-1,2,2,0,0,0-.76-.69A2.61,2.61,0,0,0,15,12a2.84,2.84,0,0,0-1.33.29,2.74,2.74,0,0,0-.9.79,3.17,3.17,0,0,0-.5,1.12,5.44,5.44,0,0,0-.15,1.27v7.28H8.74v-8a3.32,3.32,0,0,0-.54-2A2,2,0,0,0,6.44,12,3.14,3.14,0,0,0,5,12.25a2.74,2.74,0,0,0-1,.75,3.06,3.06,0,0,0-.54,1.1,4.94,4.94,0,0,0-.17,1.28v7.34H0V12.47A3.19,3.19,0,0,1,3.19,9.28Zm22.19,0h0l3.75,9.32h.05l3.36-9.32H36L29.69,25.6a10.86,10.86,0,0,1-.8,1.67,4.63,4.63,0,0,1-1,1.2,3.85,3.85,0,0,1-1.39.73,6.7,6.7,0,0,1-1.92.24A9.7,9.7,0,0,1,22,29.1l.42-3c.26.09.53.17.82.24a4.41,4.41,0,0,0,.89.1,3.78,3.78,0,0,0,1-.12,1.86,1.86,0,0,0,.72-.35,1.88,1.88,0,0,0,.49-.61c.13-.25.27-.56.42-.91l.64-1.6-4.3-10.15A2.45,2.45,0,0,1,25.38,9.28Zm56.22,0h0l2.91,9.41h.06l2.71-9.41h3.64l2.91,9.41h.06l2.8-9.41h3.39L95.51,22.72H92.18L89,13.54h-.06l-2.82,9.18H82.63l-3.48-10A2.6,2.6,0,0,1,81.6,9.28Zm22.94,0h0v2.1h0A4.5,4.5,0,0,1,106,9.69a4.32,4.32,0,0,1,2.67-.75q3.17,0,4.2,2.5a4.68,4.68,0,0,1,1.82-1.89,5.23,5.23,0,0,1,2.6-.61,5.16,5.16,0,0,1,2.27.45,4,4,0,0,1,1.5,1.23,5.24,5.24,0,0,1,.83,1.84,10.07,10.07,0,0,1,.25,2.25v8h-3.36V15.1a6.67,6.67,0,0,0-.11-1.19,2.88,2.88,0,0,0-.4-1,2,2,0,0,0-.75-.69,2.61,2.61,0,0,0-1.23-.25,2.81,2.81,0,0,0-1.33.29,2.55,2.55,0,0,0-.9.79,3.17,3.17,0,0,0-.5,1.12,5,5,0,0,0-.16,1.27v7.28h-3.36v-8a3.39,3.39,0,0,0-.53-2,2,2,0,0,0-1.76-.74,3.18,3.18,0,0,0-1.42.28,2.71,2.71,0,0,0-1,.75,3.08,3.08,0,0,0-.55,1.1,4.93,4.93,0,0,0-.16,1.28v7.34h-3.36V12.47A3.19,3.19,0,0,1,104.54,9.28ZM133,13a3.08,3.08,0,0,0-2.63-1.37,2.7,2.7,0,0,0-1.31.34,1.14,1.14,0,0,0-.65,1.09,1,1,0,0,0,.53.9,6.38,6.38,0,0,0,1.35.47l1.75.41a6.91,6.91,0,0,1,1.75.64,3.83,3.83,0,0,1,1.34,1.19,3.53,3.53,0,0,1,.53,2.07,3.69,3.69,0,0,1-.52,2,4,4,0,0,1-1.34,1.33,5.88,5.88,0,0,1-1.86.73,9.73,9.73,0,0,1-2.07.23,8.89,8.89,0,0,1-2.89-.45A5.49,5.49,0,0,1,124.64,21l2.24-2.1a5.91,5.91,0,0,0,1.39,1.19,3.3,3.3,0,0,0,1.8.47,3.35,3.35,0,0,0,.76-.09,2.4,2.4,0,0,0,.73-.28,1.7,1.7,0,0,0,.54-.49,1.26,1.26,0,0,0-.32-1.72,4.79,4.79,0,0,0-1.34-.55c-.54-.14-1.13-.27-1.75-.39a6.68,6.68,0,0,1-1.75-.59,3.69,3.69,0,0,1-1.35-1.13,3.41,3.41,0,0,1-.53-2,3.89,3.89,0,0,1,.46-1.94A4.2,4.2,0,0,1,126.74,10a5.2,5.2,0,0,1,1.74-.78,7.8,7.8,0,0,1,2-.26,8,8,0,0,1,2.67.47A4.28,4.28,0,0,1,135.25,11Z"/></g></g></svg>
            <div class="absolute right-0 top-0 mt-3 print:hidden">
                <global-search></global-search>
            </div>
        </div>
    </div>

    {{-- Row 2 --}}
    <div class="flex relative shadow-md h-header bg-white px-1 lg:px-12 z-20 border-t border-gray-300 topbar-nav print:hidden">
        <div class="flex-1 flex items-center cursor-pointer">
            <h3 class="mr-4 lg:mr-6 h-full pt-5 {{ activeIfUri('/') }} {{ activeIfUri('dashboard') }}">
                <a href="{{ url('/dashboard') }}">
                    Dashboard
                </a>
            </h3>

            <h3 class="mr-4 lg:mr-6 h-full pt-5 {{ activeIfUri('article*') }}">
                <a href="{{ url('/article') }}">
                    Artikel
                </a>
            </h3>

            <h3 class="mr-4 lg:mr-6 h-full pt-5 {{ activeIfUri('order*') }}">
                <a href="{{ url('/order') }}" class="flex">
                    Bestellungen
                    @if($globalPageService->getUnreadMessageCount())
                        <div class="ml-1 lg:ml-2 inline-block bg-blue-700 text-white rounded-full text-center w-6 h-6 text-sm leading-none pt-1" title="{{ $globalPageService->getUnreadMessageCount() }} ungelesene {{ trans_choice('plural.message', $globalPageService->getUnreadMessageCount()) }}">{{ $globalPageService->getUnreadMessageCount() }}</div>
                    @endif
                </a>
            </h3>

            <h3 class="mr-4 lg:mr-6 h-full pt-5 {{ activeIfUri('report*') }}">
                <a href="{{ url('/reports') }}">
                    Reports
                </a>
            </h3>

            <h3 class="mr-4 lg:mr-6 h-full pt-5 {{ activeIfUri('inventory*') }}">
                <a href="{{ url('/inventory') }}">
                    Inventur
                </a>
            </h3>

            <h3 class="mr-4 lg:mr-6 h-full pt-5 {{ activeIfUri('supplier*') }}">
                <a href="{{ url('/supplier') }}">
                    Lieferanten
                </a>
            </h3>

            <h3 class="mr-4 lg:mr-6 h-full pt-5 {{ activeIfUri('category*') }}">
                <a href="{{ url('/category') }}">
                    Kategorien
                </a>
            </h3>

            <h3 class="mr-4 lg:mr-6 h-full pt-5 {{ activeIfUri('unit*') }}">
                <a href="{{ url('/unit') }}">
                    Einheiten
                </a>
            </h3>
        </div>
        <div class="flex items-center">

            <dropdown direction="right">
                <template v-slot:trigger>
                    <a class="count-info mr-8 relative" href="#">
                        <i class="fa fa-bell"></i>  <span class="ml-2 inline-block text-white rounded-full w-5 h-5 absolute @if(count($globalPageService->getNotifications())) bg-red-700 @else bg-blue-700 @endif notification-label">{{ count($globalPageService->getNotifications()) }}</span>
                    </a>
                </template>

                <template v-slot:content>
                    @if(count($globalPageService->getNotifications()))
                        @foreach($globalPageService->getNotifications() as $notification)
                            @include('notifications.'.last(explode('\\', $notification->type)), compact('notification'))
                        @endforeach
                    @else
                        <li>
                            <div>Keine Nachrichten vorhanden</div>
                        </li>
                    @endif
                </template>
            </dropdown>

            <a href="{{ route('settings.show') }}" class="dropdown-toggle mr-8" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-cogs"></i> <span class="caret"></span></a>

            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa fa-sign-out"></i> <span class="hidden lg:visible">Logout</span>
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function () {

        $('.delete-notification').click(function (event) {
            event.stopImmediatePropagation();
            var notificationItem = $(this).parent().parent();
            $.get('/notification/' + $(this).attr('data-id') + '/delete', function () {
                notificationItem.remove();

                var current = $('.notification-label').innerText;
                current--;
                $('.notification-label').innerText = current;

                if (current == 0) {
                    $('.notification-label').removeClass('bg-red-700');
                    $('.notification-label').addClass('bg-blue-700');
                }
            });
        });
    });
</script>
@endpush
