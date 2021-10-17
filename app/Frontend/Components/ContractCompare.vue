<template>
        <v-card>
            <div style="max-height: 400px; overflow-y: auto;">
                 <v-col
                    cols="12"
                    md="12">
                    <template v-if="contract.change === 'add_contract'">
                        <div v-for="(currentContract,index) in contract.status" :key="index">
                            <label class="font-weight-bold" v-if="contract.status.length > 1">
                                Договор {{ index + 1 }}
                            </label>
                            <ContractDetailsTable :item="currentContract" />
                        </div>
                    </template>
                    <template v-else-if="contract.change === 'remove_contract'">
                        <v-card-title>
                            <template v-if="hasRemovedContract">
                                Премахнат договор за {{ contract.value }}
                            </template>
                            <template v-else>
                                Не са намерени активни договори за {{ contract.value }}
                            </template>
                        </v-card-title>
                        <v-card-subtitle>
                            Последна проверка {{ contract.last_check_on | humanDate }}
                        </v-card-subtitle>

                        <h4 class="px-4">
                            Информация за премахнатия договор:
                        </h4>
                        <div class="px-4">
                            <div v-for="(lastContract,index) in removedContracts" :key="index">
                                <span v-if="removedContracts.length > 1">
                                    Договор {{ index + 1 }}
                                </span>
                                <ContractDetailsTable :item="lastContract" />
                            </div>
                        </div>
                    </template>
                    <template v-else-if="contract.change === 'change_contract'">
                        тук показвам само променените параметри между двата договора
                    </template>
                </v-col>
            </div>
            
            <v-divider></v-divider>
            
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    small
                    text
                    color="success"
                    v-on:click="updateAcknowledged">
                    <v-icon>mdi-check</v-icon>
                    Потвърди промяната
                </v-btn>
            </v-card-actions>
        </v-card>
</template>
<script>
    import Network from '../Common/Network';
    import ContractDetailsTable from './ContractDetailsTable.vue'

    export default {
        props: [ 'contract' ],
        mixins: [ Network ],
        components: {
            ContractDetailsTable
        },
        computed: {
            hasRemovedContract: {
                get() {
                    return this.contract.last.length > this.contract.status.length && this.contract.status.length > 0; 
                }
            },
            removedContracts: {
                get() {
                    return this.contract.last.filter((contract) => {
                        return this.contract.status.findIndex(c => c.contractor_bulstat == contract.contractor_bulstat) === -1;
                    });
                }
            }
        },
        methods: {
            onPrintPdf() {
                this.$emit('on-print-contract',this.contract);
            },
            updateAcknowledged() {
                this.$emit('update-acknowledge',this.contract);
            }
        }
    }
</script>