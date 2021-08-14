<x-app-layout level="blue">
    <div x-data="xeleves" x-cloak>
        <x-slot name="header">
            <h2 class="font-semibold text-xl leading-tight">
                {{ __('Students manager') }}
            </h2>
        </x-slot>
        <!-- ########### PATIENCE ############# -->
        <div class="w-full h-full fixed block top-0 left-0 bg-white opacity-75 z-50" x-show="step === 'patience'">
            <span class="text-rougis opacity-75 top-1/2 my-0 mx-auto block relative w-32 h-32" style="
                top: 50%;
                ">
                <svg class="w-32 h-32 animate-spin" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 3v2a5 5 0 0 0-3.54 8.54l-1.41 1.41A7 7 0 0 1 10 3zm4.95 2.05A7 7 0 0 1 10 17v-2a5 5 0 0 0 3.54-8.54l1.41-1.41zM10 20l-4-4 4-4v8zm0-12V0l4 4-4 4z" />
                </svg>
            </span>
        </div>

        <div class="flex flex-wrap">
            <x-pave title="{{ __('Import students') }}" bg="bg-alert-200" colors="bg-alert text-white border-alert">
                <div class="">
                    <h2 class="p-3 text-rougis text-xl text-center">{{ __('Right now') }}: <b>{{ $stats['elevescount'] }}</b> élèves</h2>
                    <!-- ########### UPLOAD ############# -->
                    <form x-on:submit="uploadsiel()" enctype="multipart/form-data" x-show="step == 'upload'">
                        @csrf
                        <div class="flex w-full items-center justify-center">
                            <label
                                class="w-64 flex flex-col items-center px-4 py-6 bg-fantomis text-rougis rounded-lg shadow-lg tracking-wide uppercase border border-rougis cursor-pointer hover:bg-rougis hover:text-white">
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z" />
                                </svg>
                                <span class="mt-2 text-base leading-normal">{{ __('Upload file') }}</span>
                                <input type='file' class="hidden" x-on:change="uploadsiel()" name="sielpopulation" accept=".csv" x-ref="csvfile" />
                            </label>
                        </div>
                    </form>
                    <!-- ########### CLASSES ############# -->
                    <div x-show="step == 'classes'">
                        <div>
                            <a href="#nvcla" x-show="classes.nouvelles.length > 0" class="btn btn-rougis m-1"
                                x-on:click="datatables = datatables != false ? false : 'classes'">
                                <span x-text="classes.nouvelles.length"></span> {{ __('new classes to label') }}
                            </a>
                            <a href="#supcla" x-show='classes.anciennes.length > 0' class="btn btn-rougis m-1"
                                x-on:click="datatables = datatables != false ? false : 'classes'">
                                <span x-text="classes.anciennes.length"></span> {{ __('unused classes to delete') }}
                            </a>
                            <a href="#modcla" x-show='classes.stables.length > 0' class="btn btn-rougis m-1"
                                x-on:click="datatables = datatables != false ? false : 'classes'">
                                <span x-text="classes.stables.length"></span> {{ __('unchanged classes') }}
                            </a>
                        </div>
                        <div x-show="classes.nouvelles.length == 0">
                            <h2 x-show="eleves.nouveaux.length + eleves.modifs.length + eleves.deletes.length == 0"
                                class="p-3 text-shamrock text-xl font-bold text-center">{{ __('Students database is up to date') }}
                            </h2>
                            <button x-show="eleves.nouveaux.length > 0" class="btn btn-bleuis" x-on:click="ifelmod=true">
                                <span x-text="eleves.nouveaux.length"></span> {{ __('new students') }}
                            </button>
                            <button x-show="eleves.deletes.length > 0" class="btn btn-bleuis" x-on:click="ifelmod=true">
                                <span x-text="eleves.deletes.length"></span> {{ __('students to delete') }}
                            </button>
                            <button x-show="eleves.modifs.length > 0" class="btn btn-bleuis" x-on:click="ifelmod=true">
                                <span x-text="eleves.modifs.length"></span> {{ __('students to update') }}
                            </button>
                        </div>
                    </div>
                    <div x-show="step == 'journal'">
                        <p class="p-3 text-lg text-center text-rougis">{{ __('Operation log') }}</p>
                    </div>
                </div>
            </x-pave>
            <x-pave title="{{ __('Invitations / Reminders') }}" bg="bg-alert-200" colors="bg-alert text-white border-alert">
                <div class="text-center">
                    <h2 class="p-3 text-rougis text-xl text-center">{{ __('For :count students', ['count' => $stats['elevescount']]) }}</h2>
                    <a class="btn btn-rougis m-1" href="{{ route('downloadExcel', ['action' => 'elus']) }}">{{ $stats['eluscount'] }}
                        {{ __('confirmed') }}</a>
                    <a class="btn btn-rougis m-1" href="{{ route('downloadExcel', ['action' => 'elnus']) }}">{{ $stats['elnuscount'] }}
                        {{ __('pending') }}</a>
                    <a class="btn btn-rougis m-1" href="{{ route('downloadExcel', ['action' => 'orphelins']) }}">{{ $stats['orphelins'] }}
                        «{{ __('orphans') }}»</a>
                </div>
            </x-pave>
        </div>
        <!-- Tableaux larges -->
        <div x-show="datatables" class="w-full p-3 bg-rougis-200 m-3 border border-rougis shadow">
            <div x-show="datatables == 'classes'">
                <div x-show="classes.nouvelles.length > 0">
                    <h3 class="bg-rougis p-3 text-white text-lg font-bold text-center" id="nvcla">{{ ucfirst(__('new classes to label')) }}</h3>
                    <table class="table-auto w-full mb-3">
                        <tr class="bg-rougis-300 text-rougis-700">
                            <th class="px-3 py-1">&nbsp;</th>
                            <th class="px-3 py-1">{{ __('Official reference') }}</th>
                            <th class="px-3 py-1">{{ __('Local label') }}<br><span class="text-xs text-rougis-100">{{ __('for instance') }}: 3B</span>
                            </th>
                            <th class="px-3 py-1">{{ ucfirst(__('year')) }} / {{ ucfirst(__('titular')) }}<br><span class="text-xs text-rougis-100">{{ __('for instance') }}: 3 / M. Monty</span></th>
                        </tr>
                        <template x-for="(c, index) in classes.nouvelles" x-bind:key="c.ref">
                            <tr>
                                <td class="border px-3 py-1"><input type="checkbox" x-model="c.checked" class="form-checkbox">
                                </td>
                                <td class="border px-3 py-1" x-text="c.ref"></td>
                                <td class="border px-3 py-1"><input type="text" x-model="c.libelle" class="form-input w-full"
                                        x-bind:disabled="!c.checked"></td>
                                <td class="border px-3 py-1"><input type="text" x-model="c.titulaire" class="form-input w-full"
                                        x-bind:disabled="!c.checked"></td>
                            </tr>
                        </template>
                    </table>
                </div>
                <div x-show="classes.anciennes.length > 0">
                    <h3 class="bg-rougis p-3 text-white text-lg text-center" id="supcla">{{ __('Unused classes to delete') }}</h3>
                    <div class="mb-3">
                        <table class="table-auto w-full">
                            <tr class="bg-rougis-300 text-rougis-700">
                                <th class="px-3 py-1">&nbsp;</th>
                                <th class="px-3 py-1">{{ __('Official reference') }}</th>
                                <th class="px-3 py-1">{{ __('Local label') }}</th>
                                <th class="px-3 py-1">{{ ucfirst(__('year')) }} / {{ ucfirst(__('titular')) }}</th>
                            </tr>
                            <template x-for="(c, index) in classes.anciennes" x-bind:key="c.ref">
                                <tr>
                                    <td class="border px-3 py-1"><input type="checkbox" x-model="c.checked"
                                            class="form-checkbox"></td>
                                    <td class="border px-3 py-1" x-text="c.ref"></td>
                                    <td class="border px-3 py-1" x-text="c.libelle"></td>
                                    <td class="border px-3 py-1" x-text="c.titulaire"></td>
                                </tr>
                            </template>
                        </table>
                        <div x-show="(typeof(domcol) !== 'undefined') && domcol.length > 0"
                            class="bg-rougis text-white p-3 m-3 shadow">
                            <h3 class="p-1 text-lg font-bold">{{ strtoupper(__('Look out!')) }}</h3>
                            <p>{{ __("Deleting these classes might impact on linked tables (agenda, teacher...)") }}</p>
                        </div>
                    </div>
                </div>
                <div x-show="classes.stables.length > 0">
                    <h3 class="bg-rougis p-3 text-white text-lg font-bold text-center" id="modcla">{{ ucfirst(__('unchanged classes')) }}</h3>
                    <table class="table-auto w-full mb-3">
                        <tr class="bg-rougis-300 text-rougis-700">
                            <th class="px-3 py-1">&nbsp;</th>
                            <th class="px-3 py-1">{{ __('Official reference') }}</th>
                            <th class="px-3 py-1">{{ __('Local label') }}<br><span class="text-xs text-rougis-100">{{ __('for instance') }}: 3B</span>
                            </th>
                            <th class="px-3 py-1">{{ ucfirst(__('year')) }} / {{ ucfirst(__('titular')) }}<br><span class="text-xs text-rougis-100">{{ __('for instance') }}: 3 / M. Monty</span></th>
                        </tr>
                        <template x-for="(c, index) in classes.stables" x-bind:key="c.ref">
                            <tr>
                                <td class="border px-3 py-1"><input type="checkbox" x-model="c.checked" class="form-checkbox">
                                </td>
                                <td class="border px-3 py-1" x-text="c.ref"></td>
                                <td class="border px-3 py-1"><input type="text" x-model="c.libelle" class="form-input w-full"
                                        x-bind:disabled="!c.checked"></td>
                                <td class="border px-3 py-1"><input type="text" x-model="c.titulaire" class="form-input w-full"
                                        x-bind:disabled="!c.checked"></td>
                            </tr>
                        </template>
                    </table>
                </div>
                <div class="w-full text-center p-3">
                    <button class="btn btn-rougis" x-on:click="sauveclasses()">Valider</button>
                </div>
            </div>
        </div> <!-- /Tableaux larges -->

        <!-- Modals -->
        <x-alpine-modal trigger="ifelmod" title="{{ __('Students manager') }}">
            <div>
                <div class="p-3 m-1 shadow bg-rougis-200 border rounded">
                    <h2 class="p-3 my-1 text-xl text-center font-bold">
                        <span x-text="eleves.nouveaux.length"></span>
                        {{ __('new students') }}
                    </h2>
                    <h2 class="p-3 my-1 text-xl text-center font-bold">
                        <span x-text="eleves.deletes.length"></span>
                        {{ __('students to delete') }}
                    </h2>
                    <h2 class="p-3 my-1 text-xl text-center font-bold">
                        <span x-text="eleves.modifs.length"></span>
                        {{ __('students to update') }}
                    </h2>
                </div>
                <div class="p-3 m-1 bg-rougis-200 shadow border rounded text-center">
                    <button type="button" class="btn btn-rougis m-1" x-on:click="downloadrapport()">
                        <svg class="h-4 w-4 inline-block" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 18h12V6h-4V2H4v16zm-2 1V0h12l4 4v16H2v-1z" /></svg>
                        {{ ucfirst(__('report')) }}
                    </button>
                    <button type="button" class="btn btn-rougis m-1" x-on:click="sauveeleves()">
                        <svg class="h-4 w-4 inline-block" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM6.7 9.29L9 11.6l4.3-4.3 1.4 1.42L9 14.4l-3.7-3.7 1.4-1.42z" />
                        </svg>
                        {{ __('Confirm') }}
                    </button>
                </div>
            </div>
        </x-alpine-modal>
    </div>
    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('xeleves', () => ({
                fichiercsv: '',
                step: 'upload',
                datatables: false,
                classes: {'nouvelles':{}, 'anciennes':{}, 'stables':{}},
                domcol: {},
                dbclasses: {},
                eleves: {'nouveaux':{}, 'modifs':{}, 'deletes':{}},
                tableau: {},
                ifelmod: false,
                ac: @json($classes),
                rc: @json($refclasses),
                acco: {
                    'nouveaux': false,
                    'modifs': false,
                    'deletes': false
                },
                supprime: function() {
                    that = this;
                    Swconfirme.fire({
                        title: 'Attention!',
                        text: "Confirmez-vous la suppression de votre adresse électronique?",
                    }).then((result) => {
                        if (result.value) {
                            that.type = "aucun";
                            document.getElementById("mailtype").checked = true;
                            document.getElementById("mailtype").value="aucun";
                            document.getElementById("usermail").submit();
                        }
                    });
                },
                uploadsiel: function(e) {
                    that = this;
                    this.step = "patience";
                    /* e.preventDefault(); */
                    this.fichiercsv = this.$refs.csvfile.files[0];
                    const config = {
                        headers: {
                            'content-type': 'multipart/form-data'
                        }
                    }
                    let formData = new FormData();
                    formData.append('sielpopulation', this.fichiercsv);
                    this.step = 'patience';
                    axios.post("{{ route('population.postcsv') }}", formData, config)
                        .then(function(response) {
                            that.step = 'classes';
                            that.tableau = response.data.tableau;
                            that.eleves = response.data.eleves;
                            response.data.classes.nouvelles.forEach(function(element) {
                                element.checked = true;
                            });
                            that.classes = response.data.classes;
                            that.domcol = response.data.domcol;
                            that.dbclasses = response.data.dbclasses;
                            that.stockelocal();
                        })
                        .catch(function(error) {
                            that.step = "erreur";
                            console.log(error);
                        });
                },
                sauveclasses: function() {
                    that = this;
                    this.step = "patience";
                    axios.post("{{ route('population.postclasses') }}", {
                            classes: JSON.stringify(this.classes),
                            tableau: JSON.stringify(this.tableau),
                        })
                        .then(function(response) {
                            that.eleves = response.data.eleves;
                            response.data.classes.nouvelles.forEach(function(element) {
                                element.checked = true;
                            });
                            that.classes = response.data.classes;
                            that.dbclasses = response.data.dbclasses;
                            that.step = 'classes';
                            that.stockelocal();
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                },
                sauveeleves: function() {
                    Swconfirme.fire({
                        title: 'Patience',
                        text: "Cette opération peut prendre plusieurs minutes, et va s'exécuter en arrière-plan.",
                    }).then((result) => {
                        if (result.value) {
                            that = this;
                            this.step = 'patience';
                            axios.post("{{ route('population.posteleves') }}", {
                                    eleves: JSON.stringify(this.eleves),
                                    tableau: JSON.stringify(this.tableau),
                                })
                                .then(function(response) {
                                    //that.eleves = response.data.eleves;
                                    that.step = 'journal';
                                    that.ifelmod = false;
                                })
                                .catch(function(error) {
                                    console.log(error);
                                });
                        }
                    });
                },
                rapporteleves: function() {
                    //
                },
                stockelocal: function() {
                    localStorage.setItem('tableau', JSON.stringify(that.tableau));
                    localStorage.setItem('eleves', JSON.stringify(that.eleves));
                    localStorage.setItem('classes', JSON.stringify(that.classes));
                    localStorage.setItem('domcol', JSON.stringify(that.domcol));
                    localStorage.setItem('dbclasses', JSON.stringify(that.dbclasses));
                },
                fauxupload: function() {
                    this.tableau = JSON.parse(localStorage.getItem('tableau'));
                    this.eleves = JSON.parse(localStorage.getItem('eleves'));
                    this.classes = JSON.parse(localStorage.getItem('classes'));
                    this.domcol = JSON.parse(localStorage.getItem('domcol'));
                    this.dbclasses = JSON.parse(localStorage.getItem('dbclasses'));
                    this.etape = "classes";
                },
                fabriquerapport: function() {
                    var csv = [
                        ["Action", "Prénom", "Nom", "Classe"]
                    ];
                    if (this.eleves.nouveaux.length > 0) {
                        this.eleves.nouveaux.forEach(function(e) {
                            csv.push(['ajout', e.prenom, e.nom, that.ac[that.rc[e.classe_ref]]
                                .libelle
                            ]);
                        });
                    }
                    if (this.eleves.deletes.length > 0) {
                        this.eleves.deletes.forEach(function(e) {
                            csv.push(['suppression', e.prenom, e.nom, that.ac[e.classe_id].libelle]);
                        });
                    }
                    if (this.eleves.modifs.length > 0) {
                        this.eleves.modifs.forEach(function(e) {
                            csv.push(['modification', e.prenom, e.nom, that.ac[that.rc[e.classe_ref]]
                                .libelle
                            ]);
                        });
                    }
                    let csvContent = csv.map(e => e.join(";")).join("\n");
                    return csvContent;
                },
                downloadrapport: function(dateid) {
                    let csvContent = this.fabriquerapport();
                    let blob = new Blob([csvContent], {
                        type: "text/csv"
                    });
                    let href = window.URL.createObjectURL(blob);
                    let link = document.createElement("a");
                    link.setAttribute("href", href);
                    link.setAttribute("download", "rapport_import_siel.csv");
                    document.body.appendChild(link);
                    link.click();
                },
            }))
        })
    </script>
    @endpush
</x-app-layout>
