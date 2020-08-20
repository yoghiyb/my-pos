/**
 * Import Route Aplikasi
 */
import CategoryRoutes from './categories/routes'
import ProductRoutes from './products/routes'
import OrderRoutes from './orders/routes'
import DashboardRoutes from './dashboard/routes'

const routes = []

/**
 * Default case Pages
 */

let notFoundPage = { path: '*', component: require('./components/NotFound.vue').default }

/**
 * Push route aplikasi ke main route
 * (...AppRoute) untuk ekstrak array dari route aplikasi
 */
routes.push(
    ...CategoryRoutes,
    ...ProductRoutes,
    ...OrderRoutes,
    ...DashboardRoutes,

    notFoundPage,
)

/**
 * Export Main Route
 */

export default routes
