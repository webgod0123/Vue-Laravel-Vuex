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
                    <v-text-field
                        outlined
                        suffix="лв."
                        v-model="threshold"
                        label="Задължения по-големи от"
                        required
                        :rules="thresholdRules">
                    </v-text-field>
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

                </v-col>
            </v-row>
        </v-form>
        
        
        <div class="pt-2">
            <ErrorView/>
        </div>

        <!-- report in real time has obligation -->        
        <v-flex pt-2 v-if="obligationsLoaded">
            <p>
                ЕГН/Булстат: {{this.fetchedObligation.identity}}
            </p>
            <p>
                Име: {{this.fetchedObligation.name}}
            </p>
            <p>
                Задължение: 
                <span class="red--text" v-if="this.fetchedObligation.hasObligations">
                    <b>Има</b>
                </span>
                <span v-else class="green--text">
                    Няма
                </span>
            </p>
            <div class="pl-0 py-2">
                <v-btn
                    small
                    outlined
                    color="success" 
                    v-on:click="onOpenPrintDialog">
                    <v-icon>mdi-file-pdf-box-outline</v-icon>
                    Генерирай PDF
                </v-btn>
                <v-btn
                    small
                    outlined
                    color="info" 
                    v-on:click="onClosefetchedObligations">
                    <v-icon>mdi-close-box-outline</v-icon>
                    Скрий
                </v-btn>
            </div>
        </v-flex>

        <v-dialog v-model="printDialog" max-width="500px">
            <v-card>
                <v-card-title class="text-body-1">
                    Сигурни ли сте, че искате да генерирате PDF?
                </v-card-title>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="primary"
                        disabled
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

        <v-overlay :value="$store.state.isLoading"></v-overlay>

        <ConfirmDialog ref="confirm" />
    </div>
</template>

<script>
    import Network from '../Common/Network'
    import ErrorView from '../Components/ErrorView.vue'
    import ConfirmDialog from '../Components/ConfirmDialog.vue'

    export default {
        components: {
            ErrorView,
            ConfirmDialog
        },
        mixins: [ Network ],
        computed: {
            hasObligation: {
                get() {
                    return this.fetchedObligation !== null && this.fetchedObligation.hasObligations;
                },
                cache: false
            }
        },
        watch: {
            
        },
        data() {
            return {
                threshold: "0",
                thresholdRules: [
                    v => (v % 1 === 0) || 'Прага за задължения трябва да бъде цяло число',
                    v => (v && v >= 0 ) || 'Прага за задължения трябва да бъде по-голям или равен на 0',
                    v => (v && v <= 1000 ) || 'Прага за задължения трябва да бъде по-малък или равен на 1000',  
                ],
                obligationsLoaded: false,
                egn: '',
                caseNumber: '',
                fetchedObligation: null,
                printDialog: false,
                deleteDialog: false,
                printingContract: null,
            }
        },
        mounted() {
           
        },
        methods: {
            onClosefetchedObligations() {
                this.obligationsLoaded = false;
                this.fetchedObligation = null;
            },
            onRealTimeReport() {
                this.obligationsLoaded = false;
                this.request({
                    url: '/report/obligation?egn='+this.egn+'&case_number='+this.caseNumber+'&threshold='+this.threshold
                }).then((response) => {
                    this.fetchedObligation = response;
                    this.obligationsLoaded = true;
                })
            },
            onOpenPrintDialog() {                
                this.printDialog = true;
            },
            onClosePrintDialog() {
                this.printDialog = false;
            },
            onPrintObligation(item) {
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
            onPrint(args) {
                let sendData = {}

                if(args) {
                    sendData = args
                } else {
                    sendData = {
                        obligation: this.fetchedObligation,
                        egn: this.egn,
                        case_number: this.caseNumber,
                    }
                }

                this.request({
                    url: '/export/obligation',
                    method: 'POST',
                    data: sendData
                }).then((fileName) => {
                    this.downloadFileStream('/export/fetch-file/'+fileName, fileName+'_'+this.egn);
                    this.onClosePrintDialog(); 
                });
            }
        }
    }
</script>