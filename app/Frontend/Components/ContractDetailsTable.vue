<template>
    <v-simple-table dense>
        <tbody>
            <tr>
                <td width="40%" class="text-left">
                    Работодател булстат
                </td>
                <td width="60%" class="text-left">
                    {{ item.contractor_bulstat }}
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Работодател име
                </td>
                <td class="text-left">
                    {{ item.contractor_name }}
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    ЕИК на лицето
                </td>
                <td class="text-left">
                    {{ item.individual_EIK }}
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Имена на лицето
                </td>
                <td class="text-left">
                    {{ item.individual_names }}
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Начална дата
                </td>
                <td class="text-left">
                    {{ item.start_date | humanDate }}
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Дата последна промяна
                </td>
                <td class="text-left">
                    {{ item.last_amend_date | humanDate }}
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Крайна дата
                </td>
                <td class="text-left">
                    {{ item.end_date | humanDate }}
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Причина
                </td>
                <td class="text-left">
                    {{ item.reason }}
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Лимит
                </td>
                <td class="text-left">
                    {{ item.time_limit | humanDate }}
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Код на професията
                </td>
                <td class="text-left">
                    {{ item.profession_code }}
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Заплащане
                </td>
                <td class="text-left">
                    {{ item.remuneration }}
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    Име професия
                </td>
                <td class="text-left">
                    {{ item.profession_name }}
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    ЕКАТТЕ
                </td>
                <td class="text-left">
                    {{ item.ekatte_code }}
                </td>
            </tr>
        </tbody>
    </v-simple-table>
</template>
<script>
export default {
    props: [ 'item' ]
}
</script>