export const useApi = () => {
  const config = useRuntimeConfig();

  const $fetchApi = <T>(url: string, options: any = {}) => {
    return $fetch<T>(url, {
      baseURL: config.public.apiBase,
      ...options,
    });
  };

  return { $fetchApi };
};
