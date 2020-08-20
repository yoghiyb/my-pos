import ProductIndex from './views/Index'
import ProductCreate from './views/Create'
import ProductUpdate from './views/Update'

const ProductRoutes = [
    { path: '/product', component: ProductIndex, name: 'ProductIndex' },
    { path: '/product/create', component: ProductCreate, name: 'ProductCreate' },
    { path: '/product/:id', component: ProductUpdate, name: 'ProductUpdate' },
]

export default ProductRoutes
