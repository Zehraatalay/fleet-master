// frontend/app/composables/useApi.js

export const useApi = () => {
  const config = useRuntimeConfig(); //Nuxt'ın runtime ayarlari(config) frontend tarafindan okunmasini saglar 
  // global auth token state
  // Nuxt'ta global reactive state olusturdum
  // uygulamanın her yerinden bu state'e erisilebilir 
  const authToken = useState('authToken', () => null);

  const $fetchApi = (url, options = {}) => {
    const headers = options.headers || {};

    // eger token varsa Authorization headerini ekle
    // boylece logout haric tum korumali endpointler otomatik tokeni kullanir 
    if (authToken.value) {
      headers.Authorization = `Bearer ${authToken.value}`;
    }

    return $fetch(url, {
      baseURL: config.public.apiBase, //base URL 
      ...options,
      headers,
    });
  };

  return { $fetchApi, authToken };
};


//backend'e request atmak icin kullanilan composable fonk 