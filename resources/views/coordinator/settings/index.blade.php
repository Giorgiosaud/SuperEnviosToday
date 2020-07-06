@extends('layouts.app')

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <h1 class="super-title">
                    {{ __('settings.WELCOME') }}
                </h1>
                <h2 class="subtitle">
                    {{ __('settings.MESSAGE',[ 'app'=>config('app.name')]) }}
                </h2>
            </div>
        </div>
    </section>
    <section class="section">
        <settings-list :settings-query='@json($settings)' inline-template v-cloak>
            <div>
                <section class="section">

                    <div class="columns">
                        <div class="column">
                            <button class="button field is-info"
                                    @click="openAddSettingModal">
                                {{__('settings.ADD_SETTING')}}

                            </button>
                        </div>
                    </div>

                    <b-table
                        :data="settings"
                        :loading="loading"
                        :striped="true"
                        :total="query.total"
                        :opened-detailed="defaultOpenedDetails"
                        detailed
                        :current-page="query.current_page"
                        :per-page="query.per_page"
                        :show-detail-icon="true"
                        detail-key="id"
                        ref="settingsTable"
                        aria-next-label="Next page"
                        aria-previous-label="Previous page"
                        paginated
                        backend-paginatiopn
                        backend-filtering
                        @filters-change="changedFilter"
                        @page-change="changedPage">
                        <template slot-scope="props">
                            <b-table-column field="id" label="ID" width="40" numeric>
                                @{{ props.row.id }}
                            </b-table-column>

                            <b-table-column field="key"
                                            label="{{__('settings.KEY')}}"
                                            searchable>
                                @{{ props.row.key }}
                            </b-table-column>

                            <b-table-column field="value" label="{{__('settings.VALUE')}}">
                                @{{ props.row.value }}
                            </b-table-column>
                        </template>

                        <template slot="detail" slot-scope="props">
                            <section>
                                <b-field label="Key">
                                    <b-input v-model="props.row.key"></b-input>
                                </b-field>
                                <b-field label="Value">
                                    <b-input v-model="props.row.value"></b-input>
                                </b-field>
                                <div class="buttons">
                                    <b-button
                                        @click="changeSetting(props.row.id,props.row.key,props.row.value)"
                                        :loading="savingSetting"
                                        type="is-info">
                                        Guardar
                                    </b-button>
                                    <b-button
                                        @click="removeSetting(props.row.id)"
                                        :loading="removingSetting"
                                        type="is-danger">
                                        Borrar
                                    </b-button>
                                </div>
                            </section>

                        </template>

                        <template #empty>
                            <section class="section">
                                <div class="content has-text-grey has-text-centered">
                                    <font-awesome-icon class="is-size-1" icon="sad-tear"></font-awesome-icon>
                                    <p>No hay Configuraciones.</p>
                                </div>
                            </section>
                        </template>
                    </b-table>
                </section>
                <b-modal
                    :active.sync="isOpenModal"
                    has-modal-card
                    trap-focus
                    :destroy-on-hide="false"
                    aria-role="dialog"
                    aria-modal>
                    <add-setting @setting-created="loadAsyncData"></add-setting>
                </b-modal>
            </div>
        </settings-list>
    </section>
@endsection
