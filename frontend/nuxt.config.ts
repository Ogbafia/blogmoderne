// nuxt.config.ts
export default defineNuxtConfig({
  compatibilityDate: "2024-11-01",
  devtools: { enabled: true },

  modules: ["@nuxtjs/tailwindcss", "@pinia/nuxt", "@vueuse/nuxt", "@nuxt/icon"],

  runtimeConfig: {
    // URL privée du backend (uniquement côté serveur, jamais exposée au client)
    backendUrl: process.env.BACKEND_URL || "http://localhost:8000/api",

    public: {
      // Le frontend appelle /proxy/* → route serveur Nuxt proxifie vers Laravel
      // Mettre NUXT_PUBLIC_API_BASE=http://localhost:8000/api pour bypass proxy
      apiBase: process.env.NUXT_PUBLIC_API_BASE || "/proxy",
      appName: process.env.NUXT_PUBLIC_APP_NAME || "BlogModerne",
    },
  },

  nitro: {
    compressPublicAssets: true,
  },

  app: {
    head: {
      titleTemplate: "%s | BlogModerne",
      meta: [
        { charset: "utf-8" },
        { name: "viewport", content: "width=device-width, initial-scale=1" },
        { name: "theme-color", content: "#b80035" },
      ],
      link: [
        { rel: "icon", type: "image/svg+xml", href: "/favicon.svg" },
        { rel: "preconnect", href: "https://fonts.googleapis.com" },
        {
          rel: "stylesheet",
          href: "https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap",
        },
        {
          rel: "stylesheet",
          href: "https://fonts.googleapis.com/css2?family=Geist:wght@100..900&display=swap",
        },
      ],
    },
  },

  tailwindcss: {
    config: {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "on-background": "#1a1c1d",
            error: "#ba1a1a",
            "surface-container-low": "#f3f3f4",
            "surface-container-high": "#e8e8e9",
            "surface-container-highest": "#e2e2e3",
            "on-secondary-fixed": "#1b1b1e",
            "secondary-fixed-dim": "#c8c5ca",
            "on-surface-variant": "#5c3f40",
            "on-tertiary-fixed-variant": "#005142",
            "on-error-container": "#93000a",
            "primary-container": "#e11d48",
            "on-primary-fixed-variant": "#920028",
            "on-tertiary": "#ffffff",
            "outline-variant": "#e5bdbe",
            "surface-container-lowest": "#ffffff",
            primary: "#b80035",
            "error-container": "#ffdad6",
            "on-tertiary-container": "#eefff7",
            "tertiary-fixed-dim": "#74d8bd",
            "on-primary": "#ffffff",
            "on-secondary-fixed-variant": "#47464a",
            "secondary-container": "#e4e1e6",
            tertiary: "#006855",
            "inverse-primary": "#ffb3b6",
            "inverse-on-surface": "#f0f1f2",
            "on-error": "#ffffff",
            background: "#f9f9fa",
            "surface-variant": "#e2e2e3",
            "surface-bright": "#f9f9fa",
            "primary-fixed-dim": "#ffb3b6",
            "on-primary-fixed": "#40000c",
            "secondary-fixed": "#e4e1e6",
            "on-secondary-container": "#656467",
            "primary-fixed": "#ffdada",
            "surface-dim": "#dadadb",
            "surface-tint": "#be0037",
            "surface-container": "#eeeeef",
            "on-surface": "#1a1c1d",
            "tertiary-fixed": "#90f5d9",
            surface: "#f9f9fa",
            outline: "#906f70",
            "on-tertiary-fixed": "#002019",
            "inverse-surface": "#2f3132",
            "on-secondary": "#ffffff",
            "tertiary-container": "#00836c",
            secondary: "#5f5e61",
            "on-primary-container": "#fffaf9",
          },
          borderRadius: {
            DEFAULT: "0.125rem",
            lg: "0.25rem",
            xl: "0.5rem",
            full: "0.75rem",
          },
          spacing: {
            gutter: "24px",
            "margin-desktop": "40px",
            "container-max": "1280px",
            "margin-mobile": "16px",
            unit: "8px",
          },
          fontFamily: {
            "headline-lg": ["Geist", "sans-serif"],
            "headline-md": ["Geist", "sans-serif"],
            "body-md": ["Geist", "sans-serif"],
            "label-sm": ["Geist", "sans-serif"],
            "label-md": ["Geist", "sans-serif"],
            display: ["Geist", "sans-serif"],
            "body-lg": ["Geist", "sans-serif"],
          },
          fontSize: {
            "headline-lg": [
              "32px",
              {
                lineHeight: "40px",
                letterSpacing: "-0.02em",
                fontWeight: "600",
              },
            ],
            "headline-md": ["24px", { lineHeight: "32px", fontWeight: "600" }],
            "body-md": ["16px", { lineHeight: "24px", fontWeight: "400" }],
            "label-sm": [
              "12px",
              {
                lineHeight: "16px",
                letterSpacing: "0.05em",
                fontWeight: "600",
              },
            ],
            "label-md": [
              "14px",
              {
                lineHeight: "20px",
                letterSpacing: "0.01em",
                fontWeight: "500",
              },
            ],
            display: [
              "48px",
              {
                lineHeight: "56px",
                letterSpacing: "-0.04em",
                fontWeight: "700",
              },
            ],
            "body-lg": ["18px", { lineHeight: "28px", fontWeight: "400" }],
          },
        },
      },
    },
  },

  ssr: true,
});
