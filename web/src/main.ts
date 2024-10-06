import "bootstrap/dist/css/bootstrap.min.css";
import { Toast, Modal } from "bootstrap";
const bootstrap = { Toast, Modal };
import "bootstrap-icons/font/bootstrap-icons.css";
import "./assets/styles/main.css";

import { createApp } from 'vue';
import { createPinia } from 'pinia';

import App from './App.vue';
import routerProvider from './router';

import type HttpClient from "./application/http/HttpClient";
import AxiosHttp from "./infra/http/AxiosHttp";

import type WeatherApiInterface from "./application/weather-api/WeatherApiInterface";
import WeatherApi from "./infra/weather-api/WeatherApi";

import CheckIsAuthenticated from "./application/usecase/CheckIsAuthenticated";
import Login from "./application/usecase/Login";
import Logout from "./application/usecase/Logout";

(async () => {
    const app = createApp(App);

    //INFRA

    const weatherHttpClient: HttpClient = new AxiosHttp(
        import.meta.env.VITE_WEATHER_API_URL,
        {
            withCredentials: true,
            withXSRFToken: true
        }
    );
    const weatherApi: WeatherApiInterface | void = await WeatherApi.create(weatherHttpClient);

    //USECASES 

    const checkIsAuthenticatedUseCase = new CheckIsAuthenticated(weatherApi!);
    app.provide("checkIsAuthenticatedUseCase", checkIsAuthenticatedUseCase);

    const loginUseCase = new Login(weatherApi!);
    app.provide("loginUseCase", loginUseCase);

    const logoutUseCase = new Logout(weatherApi!);
    app.provide("logoutUseCase", logoutUseCase);

    app.provide("bootstrap", bootstrap);
    app.use(createPinia());
    app.use(routerProvider(checkIsAuthenticatedUseCase));
    app.mount('#app');
})();