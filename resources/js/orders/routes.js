import OrderIndex from './views/Index'
import OrderManagement from './views/Management'

const OrderRoutes = [
    { path: '/order', component: OrderIndex, name: 'OrderIndex' },
    { path: '/management', component: OrderManagement, name: 'OrderManagement' },
]

export default OrderRoutes
