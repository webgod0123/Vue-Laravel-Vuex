import ContractReport from './Pages/ContractReport.vue'
import ObligationReport from './Pages/ObligationReport.vue'
import Admins from './Pages/Admins.vue'
import Login from './Pages/Login.vue'

export default {
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'contracts',
            component: ContractReport,
        },
        {
            path: '/obligation',
            name: 'obligation',
            component: ObligationReport,
        },
        {
            path: '/admins',
            name: 'about',
            component: Admins,
        },
        {
            path: '/login',
            name: 'login',
            component: Login,
        }
    ]
}