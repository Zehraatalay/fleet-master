// frontend/app/middleware/auth.global.js

export default defineNuxtRouteMiddleware((to, from) => {
  const { initAuth, isLoggedIn } = useAuth();

  // auth state'i localStorage'dan yukle
  initAuth();

  // login page public 
  if (to.path === '/login') {
    if (isLoggedIn.value) {
      // zaten login ise login page'e gitmesin
      return navigateTo('/');
    }
    return;
  }

  // other pages → login zorunlu olsun
  if (!isLoggedIn.value) {
    return navigateTo('/login');
  }
});
