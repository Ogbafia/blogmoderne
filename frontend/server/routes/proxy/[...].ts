import { joinURL } from "ufo";

/**
 * Proxy Nuxt → Laravel backend
 * Transfère méthode, headers, body et query string correctement.
 */
export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig();
  const backendUrl =
    (config.backendUrl as string) || "http://localhost:8000/api";

  // Chemin après /proxy/ (ex: auth/login, articles, etc.)
  const path = getRouterParam(event, "_") ?? "";

  // Query string
  const query = getQuery(event);
  const queryStr = new URLSearchParams(
    Object.entries(query)
      .filter(([, v]) => v !== undefined)
      .map(([k, v]) => [k, String(v)]),
  ).toString();

  const target = joinURL(backendUrl, path) + (queryStr ? `?${queryStr}` : "");

  // proxyRequest (h3 auto-importé) transfère method + body + headers natifs
  return proxyRequest(event, target, {
    headers: {
      Accept: "application/json",
    },
  });
});
