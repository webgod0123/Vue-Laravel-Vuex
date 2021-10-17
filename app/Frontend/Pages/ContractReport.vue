<template>
    <div>
        <v-form>
            <v-row>
                <v-col
                    cols="12"
                    md="3"
                    sm="12">
                    <v-text-field
                        v-model="egn"
                        label="ЕГН/ЕИК"
                        required>
                    </v-text-field>
                </v-col>
                <v-col
                    cols="12"
                    md="3"
                    sm="12">
                    <v-text-field
                        v-model="caseNumber"
                        label="Номер дело"
                        required>
                    </v-text-field>
                </v-col>
                <v-col
                    cols="12"
                    md="3"
                    sm="12">
                    <v-checkbox
                        v-model="onlyActive"
                        label="Покажи само активни">
                    </v-checkbox>
                </v-col>
                <v-col
                    cols="12"
                    md="12"
                    sm="12">
                    <v-btn
                        v-on:click="onRealTimeReport"
                        small
                        color="success">
                        Еднократна проверка
                    </v-btn>

                    <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                v-on:click="onAddRegular" 
                                small 
                                outlined
                                color="info"
                                v-bind="attrs"
                                v-on="on">
                                Добави
                            </v-btn>
                        </template>
                        <span>
                            ЕГН ще се добави към списъка с регулярни проверки
                        </span>
                    </v-tooltip>

                    <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                small
                                color="primary"
                                outlined
                                v-on:click="onUploadButtonClick"
                                v-bind="attrs"
                                v-on="on">
                                <v-icon>mdi-cloud-upload-outline</v-icon>
                            </v-btn>
                        </template>
                        <span>
                            Ъплоуд ексел
                        </span>
                    </v-tooltip>

                    <input v-show="false"
                        ref="uploader"
                        type="file"
                        accept=".xls,.xlsx"
                        v-on:change="onFileChange">
                </v-col>
            </v-row>
        </v-form>
        
        
        <div class="pt-2">
            <ErrorView/>
        </div>

        <!-- report in real time -->
        <v-flex pt-2 v-if="fetchedContracts.length > 0">
            <div class="pl-0 py-2">
                <b>Трудови договори на {{ this.egn }}</b>
                <v-btn
                    small
                    text
                    color="success" 
                    v-on:click="onOpenPrintDialog">
                    <v-icon>mdi-file-pdf-box-outline</v-icon>
                    Генерирай PDF
                </v-btn>
                <v-btn
                    small
                    text
                    color="error" 
                    v-on:click="onCloseFetchedContracts">
                    <v-icon>mdi-close-box-outline</v-icon>
                    Скрий
                </v-btn>
            </div>
            <v-data-table class="elevation-1"
                hide-default-header
                hide-default-footer
                dense
                single-expand
                show-expand
                item-key="index"
                :mobile-breakpoint="0"
                :items="fetchedContracts">
                <template v-slot:header="">
                    <thead>
                        <tr>
                            <th class="text-left">
                                Работодател име
                            </th>
                            <th class="text-left">
                                Начална дата
                            </th>
                            <th class="text-left">
                                Крайна дата
                            </th>
                            <th class="text-left">
                                Тип договор
                            </th>
                            <th class="text-left">
                                Професия код
                            </th>
                            <th class="text-left">
                                Заплата
                            </th>
                            <th class="text-left">
                                Професия име
                            </th>
                            <th class="text-left">
                                ЕКАТТЕ код
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                </template>
                <template v-slot:item="{ index, item, expand, isExpanded }">
                    <tr :key="index">
                        <td v-for="field in mainFields" :key="field">
                            {{ parseItemField(item,field)}}
                        </td>
                        <td>
                            <v-btn 
                                icon
                                text
                                v-on:click="expand(!isExpanded)">
                                <v-icon>mdi-chevron-down</v-icon>
                            </v-btn>
                        </td>
                    </tr>
                </template>

                <template v-slot:expanded-item="{ item }">
                    <td :colspan="mainFields.length">
                        <v-row class="pa-2">
                            <v-col 
                                cols="12"
                                sm="12"
                                md="6">
                                <ContractDetailsTable :item="item" />
                            </v-col>
                        </v-row>
                    </td>
                </template>
            </v-data-table>
        </v-flex>

        <!-- report in real time no contracts found -->
        <v-row v-if="fetchedContracts.length === 0 && contractsLoaded === true">
            <div class="pa-3">
                <v-alert color="amber lighten-4">
                    <v-row align="center">
                        <v-col class="grow">
                            Няма намерените договори за ЕГН {{ this.egn }}
                        </v-col>
                        <v-col class="shrink">
                            <v-btn v-on:click="onOpenPrintDialog">
                                <v-icon>mdi-file-pdf-box-outline</v-icon>
                                Генерирай PDF
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-alert>
            </div>
        </v-row>
        
        <v-row class="pt-5">
            <v-col 
                cols="12"
                md="12">

                <v-badge
                    bordered
                    color="info"
                    :content="getТotalItems">
                    <label class="font-weight-bold">
                        Заредени ЕГН за проверка
                    </label>
                </v-badge>

               
               <v-menu offset-y 
                    v-if="$store.state.user.is_admin === 1">
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn  
                            class="float-right"
                            small
                            v-bind="attrs"
                            v-on="on"                 
                            color="error">
                            проверка сега
                        </v-btn>
                    </template>
                    <v-list>
                        <v-list-item
                            v-on:click="onForceScan(false)">
                            <v-list-item-title>
                                Проверка само не проверявани никога
                            </v-list-item-title>
                        </v-list-item>
                        
                        <v-list-item
                            v-on:click="onForceScan(true)">
                            <v-list-item-title>
                                Проверка на всички
                            </v-list-item-title>
                        </v-list-item>
                    </v-list>
            </v-menu>

               
               
                <v-data-table 
                    class="elevation-1"
                    hide-default-footer
                    dense
                    single-expand
                    :mobile-breakpoint="0"
                    :items="cronlist"
                    :loading="loading"
                    :headers="cronlistHeaders"
                    :options.sync="pagination"
                    @update:page="getDataFromServer"
                    @update:sort-by="getDataFromServer"
                    @update:sort-desc="getDataFromServer"
                    :server-items-length="totalItems">
                    
                    <template v-slot:top>
                        <v-text-field
                            v-model="searchString"
                            label="Търсене"
                            class="mx-4">
                        </v-text-field>
                    </template>
                    
                    <template v-slot:item="{ index, item, expand, isExpanded }">
                        <tr :key="index">
                            <td>
                                {{ item.value }}
                            </td>
                            <td>
                                {{ item.case_number }}
                            </td>
                            <td>
                                {{ item.created_at | humanDate }}
                            </td>
                            <td>
                                {{ item.created_by.name }}
                            </td>
                            <td>
                                <template v-if="item.last_check_on">
                                    {{ item.last_check_on | humanDate }}
                                    <v-btn 
                                        v-if="item.change && !item.acknowledged_by"
                                        icon
                                        text
                                        color="error"
                                        v-on="on"
                                        v-bind="attrs"
                                        v-on:click="onOpenCompareDialog(item)">
                                        <v-icon>mdi-delta</v-icon>
                                    </v-btn>
                                </template>
                            </td>
                            <td>
                                <template v-if="item.status">
                                    {{item.status.length}} 
                                    <template v-if="item.status.length === 1">
                                        договор
                                    </template>
                                    <template v-else>
                                        договора
                                    </template>
                                </template>
                            </td>
                            <td>
                                <v-layout>
                                     <v-tooltip bottom>
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-btn
                                                icon
                                                text
                                                v-bind="attrs"
                                                v-on="on"
                                                v-on:click="onRefreshItem(item)">
                                                <v-icon>mdi-refresh</v-icon>
                                            </v-btn>
                                        </template>
                                        <span>
                                            Обнови веднага
                                        </span>
                                    </v-tooltip>
                                    <v-tooltip bottom>
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-btn
                                                icon
                                                text
                                                v-bind="attrs"
                                                v-on="on"
                                                v-on:click="onPrintContract(item)">
                                                <v-icon>mdi-file-pdf-box-outline</v-icon>
                                            </v-btn>
                                        </template>
                                        <span>
                                            Генерирай PDF
                                        </span>
                                    </v-tooltip>
                                    <v-btn
                                        icon 
                                        text
                                        v-on:click="onDeleteItem(item)">
                                        <v-icon color="error">mdi-delete</v-icon>
                                    </v-btn>
                                </v-layout>
                            </td>
                            <td>
                                <v-btn 
                                    icon
                                    text
                                    v-on:click="expand(!isExpanded)">
                                    <v-icon>mdi-chevron-down</v-icon>
                                </v-btn>
                            </td>
                        </tr>
                    </template>

                    <template v-slot:expanded-item="{ item }">
                        <td :colspan="8">
                            <v-row class="pa-2">
                                <v-col 
                                    cols="12"
                                    sm="6">
                                    <div v-for="(contract,index) in item.status" :key="index">
                                        <label class="font-weight-bold">
                                            Договор {{ index + 1 }}
                                        </label>
                                        <ContractDetailsTable :item="contract" />
                                    </div>
                                </v-col>
                            </v-row>
                        </td>
                    </template>
                </v-data-table>
                <v-pagination
                    v-model="pagination.page"
                    :length="pageCount">
                </v-pagination>
            </v-col>
        </v-row>

        <v-dialog v-model="printDialog" max-width="500px">
            <v-card>
                <v-card-title class="text-body-1">
                    Сигурни ли сте, че искате да генерирате PDF?
                </v-card-title>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" 
                        v-on:click="onPrint(false)">
                        Генерирай
                    </v-btn>
                    <v-btn color="default" 
                        v-on:click="onClosePrintDialog">
                        Затвори
                    </v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog v-model="deleteDialog" max-width="500px">
            <v-card>
                <v-card-title class="text-body-1">
                    Сигурни ли сте, че искате да изтриете проверката на ЕГН/Булстат: {{ editedItem.value }}
                </v-card-title>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="error" 
                        v-on:click="deleteItem">
                        Изтрии
                    </v-btn>
                    <v-btn color="default" 
                        v-on:click="onCloseDeleteDialog">
                        Затвори
                    </v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog
            max-width="600"
            v-model="isCompareDialogOpen"
            hide-overlay
            transition="dialog-bottom-transition">

            <div>
                <v-toolbar
                    dark
                    color="primary">

                    <v-btn
                        icon
                        dark
                        v-on:click="isCompareDialogOpen = false">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                    
                    <v-toolbar-title>
                        {{ dialogTitle }}
                    </v-toolbar-title>

                </v-toolbar>

                <ContractCompare v-on:update-acknowledge="updateAcknowledged"
                    v-on:on-print-contract="onPrintContract"
                    v-bind:contract="selectedContract" />

            </div>
        </v-dialog>
        <v-overlay :value="$store.state.isLoading"></v-overlay>

        <ConfirmDialog ref="confirm" />
    </div>
</template>

<script>
    import Network from '../Common/Network'
    import ErrorView from '../Components/ErrorView.vue'
    import ContractDetailsTable from '../Components/ContractDetailsTable.vue'
    import ContractCompare from '../Components/ContractCompare.vue'
    import ConfirmDialog from '../Components/ConfirmDialog.vue'

    export default {
        components: {
            ErrorView,
            ContractDetailsTable,
            ContractCompare,
            ConfirmDialog
        },
        mixins: [ Network ],
        computed: {
            mainFields() {
                return [ 'contractor_name', 'start_date', 'end_date', 'reason', 'profession_code', 'remuneration', 'profession_name', 'ekatte_code' ];
            },
            pageCount() {
                if(this.totalItems <= this.pagination.itemsPerPage) {
                    return 1;
                }
                return Math.floor(this.totalItems / this.pagination.itemsPerPage) + 1;
            },
            getТotalItems() {
                return this.totalItems.toString();
            }
        },
        watch: {
            searchString(newValue,oldValue) {
                if(newValue.length < oldValue.length || newValue.length > 3) {
                    if(this.timer) {
                        clearTimeout(this.timer);
                        this.timer = null;
                    }
                    this.timer = setTimeout(() => {
                        this.getDataFromServer();
                    }, 300);
                }
            }
        },
        data() {
            return {
                uploadUrl: '/contract/upload-excel',
                timer: null,
                searchString: '',
                pagination: {
                    page: 1,
                    itemsPerPage: 10,
                    sortBy: [ 'last_check_on' ],
                    sortByDesc: [ true ]
                },                
                totalItems: 0,
                loading: false,
                selectedContract: null,
                dialogTitle: '',
                isCompareDialogOpen: false,
                editedItem: {
                    id: 0,
                    value: '',
                    case_number: ''
                },
                defaultItem: {
                    id: 0,
                    value: '',
                    case_number: ''
                },
                contractsLoaded: false,
                expanded: [],
                egn: '',
                caseNumber: '',
                cronlist: [],
                fetchedContracts: [],
                onlyActive: true,
                printDialog: false,
                deleteDialog: false,
                printingContract: null,
                dateFields: [ 'start_date', 'last_amend_date', 'end_date', 'time_limit', 'created_at' ],
                cronlistHeaders: [
                    {
                        text: 'ЕГН/Булстат',
                        align: 'start',
                        sortable: true,
                        value: 'value',
                    },
                    {
                        text: 'Дело',
                        align: 'start',
                        sortable: true,
                        value: 'case_number',
                    },
                    {
                        text: 'Добавено на',
                        align: 'start',
                        sortable: true,
                        value: 'created_at',
                    },
                    {
                        text: 'Добавено от',
                        align: 'start',
                        sortable: true,
                        value: 'created_by',
                    },
                    {
                        text: 'Последна проверка',
                        align: 'start',
                        sortable: true,
                        value: 'last_check_on',
                    },
                    {
                        text: 'Статус',
                        align: 'start',
                        sortable: true,
                        value: 'status',
                    },
                    {
                        text: 'Действия',
                        align: 'actions',
                        sortable: false,
                    },
                    {
                        text: '',
                    }
                ]
            }
        },
        mounted() {
            this.getDataFromServer();
        },
        methods: {
            async onForceScan(checkAll) {
                let isConfirm  = await this.$refs.confirm.open(
                    'Моля потвърдете',
                    'Сигурни ли сте, че искате да стартирате проверка сега? Сканирането ще започне на заден план, като ще се проверяват само тези, които не са проверявани вече днес.')
                
                if(isConfirm) {
                    this.forceScan(checkAll);
                }
            },
            forceScan(checkAll) {
                this.request({
                    url: '/contract/force-scan',
                    method: 'POST',
                    data: {
                        check_all: checkAll
                    }
                }).then(response => {
                    this.$root.toast.show({
                        message: 'Успешно стартирахте проверка',
                        timer: 5000
                    });
                });
            },
            onUploadButtonClick() {
                this.$refs.uploader.click()
            },
            onFileChange(event) {
                let files = event.target.files;
                if(!files) {
                    return;
                }

                let file = files[0];
                
                let formData = new FormData();
                formData.append('file', file);
                formData.append('file_name', file.name);

                // let formData = new FormData();
                // for(let i = 0; i < files.length; i++){
                //     let file = this.files[i];
                //     formData.append('files[' + i + ']', file);
                // }
        
                this.onUploadFile(formData, this.getDataFromServer);
            },
            getDataFromServer() {
                this.loading = true;
                this.request({
                    url: '/contract/get-regular',
                    method: 'POST',
                    data: {
                        pagination: this.pagination,
                        filter: this.searchString
                    }
                }).then((response) => {
                    this.cronlist = response.items;
                    this.totalItems  = response.total;
                    this.loading = false;
                })
            },
            onOpenCompareDialog(item) {
                this.selectedContract = item;
                this.dialogTitle = this.parseTitleText(item);
                this.isCompareDialogOpen = true;
            },
            parseTitleText(item) {
                if(item.change === 'add_contract') {
                    return 'Нов договор'
                } else if(item.change === 'remove_contract') {
                    return 'Прекратяване на договор'
                } else {
                    return 'Промяна в параметрите на договора'
                }
            },
            onDeleteItem(item) {
                this.editedItem = Object.assign({}, item)
                this.deleteDialog = true
            },
            onCloseFetchedContracts() {
                this.contractsLoaded = false;
                this.fetchedContracts = [];
            },
            deleteItem() {
                this.request({
                    url: '/contract/delete-regular',
                    method: 'POST',
                    data: {
                        id: this.editedItem.id
                    }
                }).then(() => {
                    let index = this.cronlist.findIndex(item => item.id === this.editedItem.id);
                    this.cronlist.splice(index, 1);
                    this.$root.toast.show({message: 'Успешно изтрихте договор'})

                    this.onCloseDeleteDialog();
                })
            },
            onCloseDeleteDialog() {
                this.deleteDialog = false
                this.editedItem = Object.assign({},this.defaultItem);
            },
            onAddRegular() {
                this.request({
                    url: '/contract/add-regular',
                    method: 'POST',
                    data: {
                        egn: this.egn,
                        case_number: this.caseNumber
                    }
                }).then((response) => {
                    this.cronlist.push(response);
                    this.$root.toast.show({message: 'Успешно добавихте договор'})
                    this.onRefreshItem(response);
                });
            },
            onRealTimeReport() {
                this.contractsLoaded = false;
                this.request({
                    url: '/report/contract?egn='+this.egn+'&case_number='+this.caseNumber+'&only_active='+this.onlyActive
                }).then((response) => {
                    this.fetchedContracts = response.contracts.map((item,index) => {
                        return {
                            index: index,
                            ...item
                        };
                    });
                    this.contractsLoaded = true;
                })
            },
            onOpenPrintDialog() {                
                this.printDialog = true;
            },
            onClosePrintDialog() {
                this.printDialog = false;
                this.isCompareDialogOpen = false;
            },
            onPrintContract(item) {
                if(!item.last_check_on) {
                    this.$store.commit('setError','Преди да се принтират резултатите трябва да има поне една проверка.');
                    return;
                }
                
                let sendData = {
                    egn: item.value,
                    case_number: item.case_number,
                    contracts: item.status ? item.status : []
                }
                
                if(item.last_check_on) {
                    sendData.date = item.last_check_on
                } else {
                    sendData.date = item.created_at
                }

                this.onPrint(sendData)
            },
            updateAcknowledged(item) {                
                this.request({
                    url: '/contract/ack-change',
                    method: 'POST',
                    data: {
                        contract_id: item.id
                    }
                }).then((response) => {
                    this.$root.toast.show({message: 'Успешно потвърдихте промяната'});
                    Object.assign(item,response);
                    this.onClosePrintDialog();
                })
            },
            onPrint(args) {
                let sendData = {}

                if(args) {
                    sendData = args
                } else {
                    sendData = {
                        contracts: this.fetchedContracts,
                        egn: this.egn,
                        case_number: this.caseNumber,
                        only_active: this.onlyActive
                    }
                }

                this.request({
                    url: '/export/contract',
                    method: 'POST',
                    data: sendData
                }).then((fileName) => {
                    this.downloadFileStream('/export/fetch-file/'+fileName, fileName+'_'+this.egn);
                    this.onClosePrintDialog(); 
                });
            },
            parseItemField(item,field) {
                if(this.dateFields.includes(field)) {
                    return this.$options.filters.humanDate(item[field]);
                }
                return item[field];
            },
            onRefreshItem(item) {
                this.request({
                    url: '/report/contract?egn='+item.value+'&case_number='+item.case_number+'&contract_id='+item.id+'&only_active=true'
                }).then((response) => {
                    Object.assign(item,response);
                    this.$root.toast.show({ message: 'Статусът беше успешно обновен' });
                })
            }
        }
    }
</script>