// frontend/app/composables/useAuth.js

export const useAuth = () => {
  const router = useRouter();
  const { $fetchApi, authToken } = useApi();

  const user = useState('authUser', () => null);
  const loading = useState('authLoading', () => false);
  const error = useState('authError', () => null);

  ///api/login cagirir token + user'i state localStorage'a yazar, dashboarda yollar 
  const login = async (email, password) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await $fetchApi('/login', {
        method: 'POST',
        body: { email, password },
      });

      // Backend login response:
      // { user: {...}, token: 'xxx' }

      authToken.value = response.token;
      user.value = response.user;

      // optional sessionStorage'a da yaz (sayfa yenilendiginde kalmasi icin) 
      if (process.client) {
        sessionStorage.setItem('authToken', response.token);
        sessionStorage.setItem('authUser', JSON.stringify(response.user));
      }

      // login success → dashboard'a yonlendir 
      router.push('/');
    } catch (err) {
      console.error(err);
      error.value =
        err?.data?.message || 'Giriş yapılırken bir hata oluştu.';
    } finally {
      loading.value = false;
    }
  };
  ///api/logout cagirir token ve useri temizler /login'e yollar 
  const logout = async () => {
    try {
      await $fetchApi('/logout', {
        method: 'POST',
      });
    } catch (err) {
      console.error('Logout error (yine de token silinecek):', err);
    }

    authToken.value = null;
    user.value = null;

    if (process.client) {
      sessionStorage.removeItem('authToken');
      sessionStorage.removeItem('authUser');
    }

    router.push('/login');
  };

  // page yenilenince sessionStorage'dan token + user'i geri yukle 
  const initAuth = () => {
    if (!process.client) return;

    const storedToken = sessionStorage.getItem('authToken');
    const storedUser = sessionStorage.getItem('authUser');

    if (storedToken) {
      authToken.value = storedToken;
    }

    if (storedUser) {
      try {
        user.value = JSON.parse(storedUser);
      } catch (e) {
        console.error('Stored user parse error:', e);
      }
    }
  };
  // token var mi yok mu bakmak icin computed flag 
  const isLoggedIn = computed(() => !!authToken.value);

  return {
    user,
    loading,
    error,
    isLoggedIn,
    login,
    logout,
    initAuth,
  };
};
