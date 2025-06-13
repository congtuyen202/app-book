// plugins/aos.client.ts
import AOS from 'aos';
import 'aos/dist/aos.css'; // Import AOS styles

export default defineNuxtPlugin((nuxtApp) => {
  if (typeof window !== 'undefined') {
    // AOS.init should be called once on the client side.
    // Options can be passed globally here, e.g., duration, once.
    AOS.init({
      duration: 800, // values from 0 to 3000, with step 50ms
      once: true,    // whether animation should happen only once - while scrolling down
    });
    // Optional: make AOS instance available globally if needed for manual operations
    // nuxtApp.provide('aos', AOS);
  }
});
