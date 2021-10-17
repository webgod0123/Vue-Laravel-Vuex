<template>
    <div> 
        <!-- <v-card flat>
            <v-flex xs12 pa-2>   
                <span class="font-weight-bold">
                    Списък администратори
                </span>
                <v-btn elevation-2 small class="float-right">
                    <v-icon>mdi-plus</v-icon> Добави
                </v-btn>
            </v-flex>
        </v-card> -->
        <v-data-table
            dense
            :items="admins"
            hide-default-header
            hide-default-footer
            item-key="name"
            :mobile-breakpoint="0"
            :items-per-page="-1"
            class="elevation-1">
            <template v-slot:top>
                <v-toolbar flat>
                    <v-toolbar-title>
                       Списък администратори 
                    </v-toolbar-title>
                    
                    <v-spacer></v-spacer>

                    <v-dialog 
                        :fullscreen="$isMobile()"
                        v-model="dialog" 
                        persistent
                        max-width="500px">

                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                color="primary"
                                dark
                                class="mb-2"
                                v-bind="attrs"
                                v-on="on">
                                <v-icon>mdi-plus</v-icon> Добави
                            </v-btn>
                        </template>
                        <v-card>
                            <v-card-title>
                                <span class="text-h5">
                                    {{ formTitle }}
                                </span>
                            </v-card-title>

                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col
                                            cols="12"
                                            sm="12"
                                            md="8">
                                            <v-text-field
                                                v-model="editedAdmin.name"
                                                label="Име">
                                            </v-text-field>
                                        </v-col>
                                        <v-col
                                            v-if="$store.state.user.is_admin === 1"
                                            cols="12"
                                            sm="12"
                                            md="4">
                                            <v-checkbox 
                                                v-model="editedAdmin.is_admin"
                                                label="админ"></v-checkbox>
                                        </v-col>
                                        <v-col
                                            cols="12"
                                            sm="12"
                                            md="12">
                                            <v-text-field
                                                v-model="editedAdmin.email"
                                                label="Имейл">
                                            </v-text-field>
                                        </v-col>
                                        <v-col
                                            cols="12"
                                            sm="6"
                                            md="6">
                                            <v-text-field
                                                v-model="editedAdmin.position"
                                                label="Позиция">
                                            </v-text-field>
                                        </v-col>
                                        <v-col
                                            cols="12"
                                            sm="6"
                                            md="6">
                                            <v-text-field
                                                v-model="editedAdmin.additional_identifier"
                                                label="Допълнителен идентификатор">
                                            </v-text-field>
                                        </v-col>
                                    </v-row>

                                    <v-row>
                                        <div class="grey--text body-2" v-if="editedAdmin.id > 0">
                                            Полето за парола е задължително само, ако желаете да смените паролата
                                        </div>
                                        <v-col 
                                            cols="12"
                                            sm="12">
                                            <v-text-field
                                                v-model="editedAdmin.password"
                                                :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                                                :type="showPassword ? 'text' : 'password'"
                                                name="input-10-1"
                                                label="Парола"
                                                hint="Минимум 8 символа"
                                                counter
                                                v-on:click:append="showPassword = !showPassword">
                                            </v-text-field>
                                        </v-col>

                                        <v-col
                                            cols="12"
                                            sm="12">
                                            <v-text-field
                                                v-model="editedAdmin.repeatPassword"
                                                :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                                                :type="showPassword ? 'text' : 'password'"
                                                name="input-10-2"
                                                label="Повтори паролата"
                                                hint="Минимум 8 символа"
                                                class="input-group--focused"
                                                counter
                                                v-on:click:append="showPassword = !showPassword"
                                            ></v-text-field>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-card-text>

                            <ErrorView />

                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn
                                    color="primary"
                                    v-on:click="onSave">
                                    Запази
                                </v-btn>
                                <v-btn
                                    color="default darken-1"
                                    v-on:click="onClose">
                                    Затвори
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                    <v-dialog
                        v-model="dialogDelete" 
                        max-width="500px">
                        <v-card>
                            <v-card-title class="text-body-1">
                                Сигурни ли сте, че искате да изтриете {{ editedAdmin.name }}?
                            </v-card-title>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="error" 
                                    v-on:click="onDelete">
                                    Изтрии
                                </v-btn>
                                <v-btn color="default" 
                                    v-on:click="onClose">
                                    Затвори
                                </v-btn>
                                <v-spacer></v-spacer>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                </v-toolbar>
            </template>
            <template v-slot:header="">
                <thead>
                    <tr>
                        <th> Име </th>
                        <th> Имейл </th>
                        <th> Позиция </th>
                        <th> Допълнителен идентификатор </th>
                        <th> </th>
                    </tr>
                </thead>
            </template>
            <template v-slot:body="{ items }">
                <tbody>
                    <tr v-for="admin in items"
                        :key="admin.name">
                        <td>
                            {{ admin.name }}
                            <v-tooltip v-if="admin.is_admin" bottom>
                                <template v-slot:activator="{ on, attrs }">
                                    <v-icon
                                        v-bind="attrs"
                                        v-on="on">
                                        mdi-account-outline
                                    </v-icon>
                                </template>
                                <span>
                                    Администраторски права
                                </span>
                            </v-tooltip>
                        </td>
                        <td>{{ admin.email }}</td>
                        <td>{{ admin.position }}</td>
                        <td>{{ admin.additional_identifier }}</td>
                        <td class="text-xs-center">
                            <v-layout justify-center>
                                <v-btn 
                                    icon 
                                    text 
                                    v-on:click="editAdmin(admin)">
                                    <v-icon>mdi-account-edit-outline</v-icon>
                                </v-btn>
                                <v-btn
                                    icon 
                                    text
                                    v-on:click="deleteAdmin(admin)">
                                    <v-icon color="error">mdi-delete</v-icon>
                                </v-btn>
                            </v-layout>
                        </td>
                    </tr>
                </tbody>
            </template>
        </v-data-table>
    </div>
</template>

<script>
import Network from '../Common/Network'
import ErrorView from '../Components/ErrorView'

export default {
    mixins: [ Network ],
    components: {
        ErrorView,        
    },
    data() {
        return {
            showPassword: false,
            dialog: false,
            dialogDelete: false,
            editedIndex: -1,
            editedAdmin: {
                id: 0,
                name: '',
                email: '',
                position: '',
                additional_identifier: '',
                password: '',
                repeatPassword: ''
            },
            defaultAdmin: {
                id: 0,
                name: '',
                email: '',
                position: '',
                additional_identifier: '',
                password: '',
                repeatPassword: ''
            },
            admins: [],
            rules: {                
                min: v => v.length >= 8 || 'Минимум 8 символа',
            },
        }
    },
    computed: {
        formTitle () {
            return this.editedIndex === -1 ? 'Добави потребител' : 'Редактиране потребител'
        },
    },
    mounted() {
        this.request({
            url: '/admins/get'
        }).then(response => {
            this.admins = response;
        })
    },
    methods: {
        editAdmin(admin) {
            this.editedIndex = this.admins.indexOf(admin)
            this.editedAdmin = Object.assign({}, admin)
            this.dialog = true
        },
        deleteAdmin(admin) {
            this.editedIndex = this.admins.indexOf(admin)
            this.editedAdmin = Object.assign({}, admin)
            this.dialogDelete = true
        },
        onDelete() {
            this.admins.splice(this.editedIndex, 1);
            this.onClose();
        },
        onClose() {
            this.dialog = false
            this.dialogDelete = false
            this.$nextTick(() => {
                this.editedAdmin = Object.assign({}, this.defaultAdmin)
                this.editedIndex = -1
                this.$store.commit('clearError');
            })
        },
        onSave() {
            this.request({
                url: '/admins',
                method: 'POST',
                data: this.editedAdmin
            }).then(() => {
                if(this.editedIndex > -1) {
                    Object.assign(this.admins[this.editedIndex], this.editedAdmin)
                } else {
                    this.admins.push(this.editedAdmin)
                }
                this.$root.toast.show({message: 'Промените бяха успешно запазени'})
                this.onClose()
            });
        }
    }
}
</script>