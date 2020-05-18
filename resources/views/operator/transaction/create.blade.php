@extends('layouts.app')

@section('content')
<section class="hero is-primary">
    <div class="hero-body">
        <div class="container">
            <h1 class="super-title">
                {{ __('transaction.TITLE') }}
            </h1>
            <h2 class="subtitle">
                {{ __('transaction.MESSAGE',[ 'app'=>config('app.name')]) }}
            </h2>
        </div>
    </div>
</section>
<section class="section">
    <transaction-create inline-template>
        <section>
            <b-steps
                size="is-small"
                v-model="actualStep"
                :has-navigation="false"
                :animated="true">
                <b-step-item label="{{__('transaction.CLIENT:TITLE')}}" icon="user-edit">
                    @component('operator.transaction.client')
                        @slot('properties')
                            @client-data-set="clientDataSet"
                            @next-step="nextStep"
                        @endslot
                    @endcomponent
                </b-step-item>
                <b-step-item :clickable="clientReady" label="{{__('transaction.TRANSACTION:TITLE')}}" icon="money-check-alt">
                    @component('operator.transaction.transaction')
                        @slot('properties')
                            @client-transaction-set="transactionDataSet"
                            @next-step="nextStep"
                        @endslot
                    @endcomponent
                </b-step-item>
                <b-step-item label="{{__('transaction.RECEIVER:TITLE')}}" icon="hand-holding-usd">
                    {{__('transaction.RECEIVER:TITLE')}}
                    @component('operator.transaction.receiver')
                    @slot('properties')
                    @client-receiver-set="clientReceiverSet"
                    @next-step="nextStep"
                    @endslot
                    @endcomponent
                </b-step-item>
                <b-step-item label="{{__('transaction.VENEZUELAN_OPERATOR:TITLE')}}" icon="comment-dollar">
                    {{__('transaction.VENEZUELAN_OPERATOR:TITLE')}}
                </b-step-item>
                <b-step-item label="{{__('transaction.REVIEW:TITLE')}}" icon="file-invoice-dollar">
                    {{__('transaction.REVIEW:TITLE')}}
                </b-step-item>

            </b-steps>
        </section>
    </transaction-create>
</section>
@endsection
