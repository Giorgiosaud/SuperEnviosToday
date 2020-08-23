@extends('layouts.app')

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <h1 class="super-title">
                    {{ __('users.WELCOME') }}
                </h1>
                <h2 class="subtitle">
                    {{ __('users.MESSAGE',[ 'app'=>config('app.name')]) }}
                </h2>
            </div>
        </div>
    </section>
    <section class="section">
        <users-list :users-query='@json($users)' :all-roles='@json($roles)' inline-template v-cloak>
            <div>
                <section class="section">
                    <div class="columns">
                        <div class="column">
                            <button class="button field is-info"
                                    @click="goToDetails"
                                    :disabled="!selected.id">
                                <span v-if="!selected.id">{{__('users.SELECT_USER')}}</span>
                                <span
                                    v-else>{{__('users.GO_TO_SELECTED')}} @{{selected.name}} @{{selected.last_name}}</span>
                            </button>
                        </div>
                        <div class="column">
                            <b-taginput
                                v-model="selectedRoles"
                                :data="filteredRoles"
                                autocomplete
                                :allow-new="false"
                                :open-on-focus="true"
                                field="name"
                                icon="label"
                                placeholder="{{__('users.SELECT:ROLE')}}"
                                @typing="getFilteredTags">
                            </b-taginput>
                        </div>
                    </div>

                    <b-table
                        :data="users"
                        :loading="loading"
                        @click="userClicked"
                        paginated
                        backend-pagination
                        backend-filtering
                        :striped="true"
                        :total="query.total"
                        :current-page="query.current_page"
                        :per-page="query.per_page"
                        @page-change="changedPage"
                        @filters-change="changedFilter"
                        aria-next-label="Next page"
                        aria-previous-label="Previous page"
                        :selected.sync="selected">

                        <template slot-scope="props">
                            <b-table-column field="id"
                                            label="ID"
                                            width="40"
                                            numeric
                                            sticky>
                                @{{ props.row.id }}
                            </b-table-column>

                            <b-table-column field="name"
                                            label="{{__('auth.NAME')}}"
                                            searchable>
                                @{{ props.row.name }}
                            </b-table-column>

                            <b-table-column field="last_name" label="{{__('auth.LAST_NAME')}}" searchable>
                                @{{ props.row.last_name }}
                            </b-table-column>
                            <b-table-column field="email" label="{{__('auth.EMAIL')}}" searchable>
                                @{{ props.row.email }}
                            </b-table-column>
                            <b-table-column field="idn_type" label="{{__('auth.IDN_TYPE')}}" searchable>
                                @{{ props.row.idn_type }}
                            </b-table-column>
                            <b-table-column field="idn" label="{{__('auth.IDN')}}" searchable>
                                @{{ props.row.idn }}
                            </b-table-column>
                            <b-table-column field="role" label="{{__('users.ROLE')}}">
                                <span class="tag"
                                      v-for="role in props.row.roles"
                                      :class="{
                                      'is-danger':role.name_id==='coordinator',
                                      'is-warning':role.name_id==='foreign_operator',
                                      'is-success':role.name_id==='receiver',
                                      'is-info':role.name_id==='venezuelan_operator',
                                      'is-primary':role.name_id==='client'
                                      }">
                                   @{{ role.name}}
                                </span>

                            </b-table-column>
                        </template>
                        <template slot="empty">
                            <section class="section">
                                <div class="content has-text-grey has-text-centered">
                                        <font-awesome-icon class="is-size-1" icon="sad-tear"></font-awesome-icon>
                                    <p>No hay datos coincidentes.</p>
                                </div>
                            </section>
                        </template>
                    </b-table>
                </section>
            </div>
        </users-list>
    </section>
@endsection
