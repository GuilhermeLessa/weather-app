import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue';
import ForecastView from '../views/ForecastView.vue'
import type CheckIsAuthenticated from '@/application/usecase/CheckIsAuthenticated';

const routerProvider = (
  checkIsAuthenticatedUseCase: CheckIsAuthenticated
) => {

  const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
      {
        path: '/',
        name: 'home',
        component: HomeView,
        beforeEnter: async (to, from, next) => {
          const isAuthenticated = await checkIsAuthenticatedUseCase.execute();
          if (isAuthenticated.isRight()) {
            return next('/forecast');
          }
          return next();
        }
      },
      {
        path: '/forecast',
        name: 'forecast',
        component: ForecastView,
        beforeEnter: async (to, from, next) => {
          const isAuthenticated = await checkIsAuthenticatedUseCase.execute();
          if (isAuthenticated.isLeft()) {
            return next('/');
          }
          return next();
        }
      }
    ]
  });

  return router;
}

export default routerProvider
