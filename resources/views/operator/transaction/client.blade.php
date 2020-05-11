<client-data
    {{ $properties }}
    inline-template>
    <section class="section">
        <section class="section is-paddingless">
            <div class="columns">
                <div class="column is-narrow">
                    <validation-provider
                        rules="required"
                        v-slot="{ classes,errors,valid }"
                        name="{{__('auth.IDN_TYPE')}}"
                        tag="div"
                        class="control">
                        <label class="label">{{__('auth.IDN_TYPE')}}</label>
                        <div class="control has-icons-left has-icons-right">
                            <div class="select"
                                 :class="classes">
                                <select
                                    id="idn_type"
                                    name="idn_type"
                                    v-model="client.idn_type">
                                    <option value="">{{__('auth.DEFAULT:IDNTYPE')}}</option>
                                    <option value="CI">Cédula Venezolana</option>
                                    <option value="PASSPORT">Pasaporte</option>
                                    <option value="RUT">RUT</option>
                                    <option value="DNI">DNI</option>
                                    <option value="RIF">RIF</option>
                                </select>
                                <span class="icon is-small has-text-success is-right" v-if="valid">
                                    <font-awesome-icon icon="check"></font-awesome-icon>
                                </span>
                            </div>
                            <span class="icon is-small is-left">
                            <font-awesome-icon icon="passport"></font-awesome-icon>
                        </span>
                        </div>
                        <strong
                            v-if="errors[0]"
                            class="help is-danger">@{{errors[0]}}</strong>
                    </validation-provider>
                </div>
                <div class="column">
                    <validation-provider
                        rules="required"
                        name="{{__('auth.IDN')}}"
                        v-slot="{ classes,errors,valid}"
                        tag="div"
                        class="control">
                        <label class="label" for="idn">{{__('auth.IDN')}}</label>
                        <div class="control has-icons-right">
                            <input id="idn"
                                   v-model="client.idn"
                                   name="idn"
                                   class="input"
                                   @keydown.enter="searchClient"
                                   :class="classes"
                                   type="text"
                                   placeholder="{{__('auth.IDN')}}"
                                   value="123"
                                   autocomplete="idn" autofocus>
                            <span class="icon is-small has-text-warning	is-right"
                                  v-if="errors[0]">
                                <font-awesome-icon icon="exclamation-triangle"></font-awesome-icon>
                            </span>
                            <span class="icon is-small has-text-success is-right" v-if="valid">
                                <font-awesome-icon icon="check"></font-awesome-icon>
                            </span>
                        </div>
                        <strong v-if="errors[0]" class="help is-danger">@{{errors[0]}}</strong>
                    </validation-provider>
                </div>
                <div class="column is-narrow">
                    <div class="control">
                        <label for="idn" class="label">{{__('transaction.SEARCH:LABEL')}}</label>
                        <b-button size="is-big"
                                  type="is-info"
                                  :loading="searchingClient"
                                  :disabled="client.idn===''||client.idn_type===''"
                                  icon-left="search"
                                  @click="searchClient">
                            {{__('transaction.SEARCH:BUTTON')}}
                        </b-button>
                    </div>
                </div>
            </div>
        </section>
        <section class="section is-paddingless" v-if="searchExecuted">
            <validation-observer v-slot="{ invalid }">

                <validation-provider

                    rules="required"
                    tag="div"
                    class="field">
                    <input
                        v-model="client.idn_type"
                        id="idn_type"
                        type="hidden"
                        name="idn_type"></input>
                </validation-provider>
                <validation-provider

                    rules="required"
                    v-slot="{ classes,errors, valid }"
                    tag="div"
                    class="field">
                    <input
                        v-model="client.idn"
                        id="idn_type"
                        type="hidden"
                        name="idn_type"></input>
                </validation-provider>
                <div class="columns">
                    <div class="column">
                        <validation-provider

                            rules="required"
                            v-slot="{ classes,errors, valid }"
                            tag="div"
                            class="field">

                            <label class="label" for="name">{{__('auth.NAME')}}</label>
                            <div v-if="clientReady">
                                @{{client.name}}
                            </div>
                            <div class="control has-icons-right" v-else>
                                <input id="name"
                                       v-model="client.name"
                                       name="name"
                                       class="input"
                                       :class="classes"
                                       type="text"
                                       placeholder="{{__('auth.NAME')}}"
                                       autocomplete="name" autofocus>
                                <span class="icon is-small has-text-warning	is-right"
                                      v-if="errors[0]">
                                    <font-awesome-icon icon="exclamation-triangle"></font-awesome-icon>
                                </span>
                                <span class="icon is-small has-text-success is-right" v-if="valid">
                                    <font-awesome-icon icon="check"></font-awesome-icon>
                                </span>
                            </div>
                            <strong
                                v-if="errors[0]"
                                class="help is-danger">@{{errors[0]}}</strong>
                        </validation-provider>
                    </div>
                    <div class="column">
                        <validation-provider
                            name="{{__('auth.LAST_NAME')}}"
                            rules="required"
                            v-slot="{ classes,errors, valid }"
                            tag="div"
                            class="field">
                            <label
                                class="label"
                                for="last_name">{{__('auth.LAST_NAME')}}</label>
                            <div v-if="clientReady">
                                @{{client.last_name}}
                            </div>
                            <div class="control has-icons-right" v-else>
                                <input id="last_name"
                                       name="last_name"
                                       v-model="client.last_name"
                                       class="input"
                                       :class="classes"
                                       type="text"
                                       placeholder="{{__('auth.LAST_NAME')}}"
                                       autocomplete="last_name" autofocus>
                                <span class="icon is-small has-text-warning	is-right"
                                      v-if="errors[0]">
                                            <font-awesome-icon icon="exclamation-triangle"></font-awesome-icon>
                                        </span>
                                <span class="icon is-small has-text-success is-right" v-if="valid">
                                            <font-awesome-icon icon="check"></font-awesome-icon>
                                        </span>
                            </div>
                            <strong
                                v-if="errors[0]"
                                class="help is-danger">@{{errors[0]}}</strong>
                        </validation-provider>
                    </div>
                    <div class="column">
                        <validation-provider
                            name="{{__('auth.EMAIL')}}"
                            rules="required|email"
                            v-slot="{ classes,errors,valid }"
                            tag="div"
                            class="field">

                            <label class="label" for="email">{{__('auth.EMAIL')}}</label>
                            <div v-if="clientReady">
                                @{{client.email}}
                            </div>
                            <div class="control has-icons-left has-icons-right" v-else>
                                <input id="email" name="email"
                                       :class="classes"
                                       class="input"
                                       type="text"
                                       v-model="client.email"
                                       placeholder="{{__('auth.EMAIL')}}"
                                       autocomplete="email" autofocus>
                                <span class="icon is-small is-left">
                                <font-awesome-icon icon="envelope"></font-awesome-icon>
                            </span>
                                <span class="icon is-small has-text-warning	is-right" v-if="errors[0]">
                                <font-awesome-icon icon="exclamation-triangle"></font-awesome-icon>
                            </span>
                                <span class="icon is-small has-text-success is-right" v-if="valid">
                                <font-awesome-icon icon="check"></font-awesome-icon>
                            </span>
                            </div>
                            <strong v-if="errors[0]" class="help is-danger">@{{errors[0]}}</strong>
                        </validation-provider>
                    </div>
                    <div class="column">
                        <validation-provider
                            name="{{__('auth.PHONE')}}"
                            rules="alpha_dash"
                            v-slot="{ classes,errors,valid }"
                            tag="div"
                            class="field">

                            <label class="label" for="email">{{__('auth.PHONE')}}</label>
                            <div v-if="clientReady">
                                @{{client.phone}}
                            </div>
                            <div class="control has-icons-left has-icons-right" v-else>
                                <input id="phone" name="phone"
                                       :class="classes"
                                       class="input"
                                       type="text"
                                       v-model="client.phone"
                                       placeholder="{{__('auth.PHONE')}}"
                                       autocomplete="phone" autofocus>
                                <span class="icon is-small is-left">
                                <font-awesome-icon icon="phone"></font-awesome-icon>
                            </span>
                                <span class="icon is-small has-text-warning	is-right" v-if="errors[0]">
                                <font-awesome-icon icon="exclamation-triangle"></font-awesome-icon>
                            </span>
                                <span class="icon is-small has-text-success is-right" v-if="valid">
                            <font-awesome-icon icon="check"></font-awesome-icon>
                        </span>
                            </div>
                            <strong v-if="errors[0]" class="help is-danger">@{{errors[0]}}</strong>
                        </validation-provider>
                    </div>
                    <div class="column is-narrow" v-if="!clientReady">
                        <label for="idn" class="label">{{__('transaction.SAVE:LABEL')}}</label>
                        <b-button size="is-big"
                                  type="is-info"
                                  :loading="savingClient"
                                  :disabled="invalid"
                                  icon-left="save"
                                  @click="saveClient">
                            {{__('transaction.SAVE:BUTTON')}}
                        </b-button>
                    </div>
                </div>

            </validation-observer>
        </section>
        <b-modal :active.sync="isComponentModalActive"
                 has-modal-card
                 trap-focus
                 :destroy-on-hide="false"
                 aria-role="dialog"
                 aria-modal>
            <div class="card">
                <header class="card-header">
                    <p class="card-header-title">
                        {{__('transaction.CLIENT:DIFFERENT_IDN:EXIST')}}
                        {{__('transaction.CLIENT:DIFFERENT_IDN:EXIST:SUB')}}
                    </p>
                </header>
                <div class="card-content">
                    <div class="content">
                        @{{possibleClient.idn_type}}
                        @{{possibleClient.idn}}
                        @{{possibleClient.name}}
                        @{{possibleClient.last_name}}
                        @{{possibleClient.email}}
                        @{{possibleClient.phone}}
                        @{{possibleClient.id}}
                    </div>
                </div>
                <footer class="card-footer">
                    <a @click.prevent="usePossibleClient" class="card-footer-item">Utilizar el Sugerido</a>
                    <a @click.prevent="createNew" class="card-footer-item">Crear Nuevo</a>
                </footer>
            </div>
        </b-modal>
    </section>
</client-data>
