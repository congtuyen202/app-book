import { createApp } from 'vue';
import { createPinia } from 'pinia';
import Home from './Home.vue';
import router from './routes';

const pinia = createPinia();

const app = createApp(Home);
app.use(pinia);
app.use(router);

// Thêm directive click-outside
app.directive('click-outside', {
    mounted(el, binding) {
        el.clickOutsideEvent = function(event) {
            if (!(el === event.target || el.contains(event.target))) {
                binding.value(event, el);
            }
        };
        document.body.addEventListener('click', el.clickOutsideEvent);
    },
    unmounted(el) {
        document.body.removeEventListener('click', el.clickOutsideEvent);
    }
});

app.mount('#app');
