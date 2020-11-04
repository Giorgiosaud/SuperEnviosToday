<b-menu class="is-custom-mobile">
    @can('manage-users')
        <b-menu-list label="{{__('users.MENU:TITLE')}}">
            <b-menu-item icon="users"
                         label="{{__('users.MENU:LIST')}}"
                         tag="a"
                         href="{{ route('users.index') }}"></b-menu-item>
        </b-menu-list>
    @endcan
    @can('manage-transactions')
        <b-menu-list label="{{__('transaction.MENU:TITLE')}}">
            @can('see-all-transactions')
                <b-menu-item icon="calendar-check"
                             label="{{__('transaction.MENU:LIST:ALL')}}"
                             tag="a"
                             href="{{ route('transaction.index') }}"></b-menu-item>
            @endcan
            @can('see-my-transactions')
                <b-menu-item icon="calendar-check"
                             label="{{__('transaction.MENU:MY:LIST:ALL')}}"
                             tag="a"
                             href="{{ route('transaction.my.index') }}"></b-menu-item>
            @endcan
            @can('operate-venezuelan-transactions')
                <b-menu-item icon="calendar-check"
                             label="{{__('transaction.MENU:VENEZUELAN:LIST')}}"
                             tag="a"
                             href="{{ route('venezuelan.transactions.my.index') }}"></b-menu-item>
            @endcan
            @can('approve-operations')
                <b-menu-item icon="calendar-check"
                             label="{{__('pendingTransactions.MENU:PENDING')}}"
                             tag="a"
                             href="{{ route('pending-transactions.index') }}"></b-menu-item>
            @endcan
            @can('review-pending-operations')
                <b-menu-item icon="calendar-check"
                             label="{{__('pendingTransactions.MENU:MY:PENDING')}}"
                             tag="a"
                             href="{{ route('my-pending-transactions.index') }}"></b-menu-item>
            @endcan
            @can('create-transaction')
                <b-menu-item icon="money-check-alt"
                             label="{{__('transaction.MENU:CREATE')}}"
                             tag="a"
                             href="{{ route('transaction.create') }}"></b-menu-item>
            @endcan
        </b-menu-list>
    @endcan
    @can('manage-currencies')
        <b-menu-list label="{{__('currencies.MENU:TITLE')}}">
            <b-menu-item icon="coins"
                         label="{{__('currencies.MENU:MANAGE')}}"
                         tag="a"
                         href="{{ route('currencies.index') }}"></b-menu-item>
        </b-menu-list>
    @endcan
    @can('manage-banks')
        <b-menu-list label="{{__('banks.MENU:TITLE')}}">
            <b-menu-item icon="university"
                         label="{{__('banks.MENU:MANAGE')}}"
                         tag="a"
                         href="{{ route('banks.index') }}"></b-menu-item>
        </b-menu-list>
    @endcan
    @can('manage-accounts')
        <b-menu-list label="{{__('accounts.MENU:TITLE')}}">
            <b-menu-item icon="file-invoice-dollar"
                         label="{{__('accounts.MENU:MANAGE')}}"
                         tag="a"
                         href="{{ route('accounts.index') }}"></b-menu-item>
        </b-menu-list>
    @endcan
    @can('manage-rates')
        <b-menu-list label="{{__('rates.MENU:TITLE')}}">
            <b-menu-item icon="chart-line"
                         label="{{__('rates.MENU:MANAGE')}}"
                         tag="a"
                         href="{{ route('rates.index') }}"></b-menu-item>
        </b-menu-list>
    @endcan
    @can('manage-settings')
        <b-menu-list label="{{__('settings.MENU:TITLE')}}">
            <b-menu-item icon="tools"
                         label="{{__('settings.MENU:MANAGE')}}"
                         tag="a"
                         href="{{ route('settings.index') }}"></b-menu-item>
        </b-menu-list>
    @endcan

    <b-menu-list label="Actions">
        @auth
            <form id="logout-form"
                  action="{{ route('logout') }}"
                  method="POST"
                  style="display: none;">
                @csrf
            </form>
            <b-menu-item icon="sign-out-alt"
                         label="Logout"
                         onclick="event.preventDefault();document.getElementById('logout-form').submit();"
            >
            </b-menu-item>
        @else
            <b-menu-item label="{{ __('auth.REGISTER') }}"
                         icon="file-signature"
                         tag="a"
                         href="{{ route('register') }}"></b-menu-item>
            <b-menu-item label="{{ __('auth.LOGIN') }}"
                         icon="sign-in-alt"
                         tag="a"
                         href="{{ route('login') }}"></b-menu-item>
        @endauth

    </b-menu-list>
</b-menu>
