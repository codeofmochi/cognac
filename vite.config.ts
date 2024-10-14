import { defineConfig } from "vite"
import react from "@vitejs/plugin-react"
import symfonyPlugin from "vite-plugin-symfony"
import path from "path"

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    react(),
    symfonyPlugin({
      viteDevServerHostname: "localhost",
    }),
  ],
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "./assets"),
    },
  },
  build: {
    rollupOptions: {
      input: {
        app: "./assets/main.tsx",
      },
    },
  },
})
