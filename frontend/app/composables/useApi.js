// frontend/app/composables/useApi.js

export const useApi = () => {
  const config = useRuntimeConfig();

  const $fetchApi = (url, options = {}) => {
    return $fetch(url, {
      baseURL: config.public.apiBase,
      ...options,
    });
  };

  return { $fetchApi };
};
