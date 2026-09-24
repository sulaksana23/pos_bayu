import type { NextConfig } from "next";

// Laravel backend. Requests to /laravel/* are proxied there, so the browser
// never talks cross-origin (no CORS setup needed) and /storage images work.
const BACKEND_URL = (process.env.BACKEND_URL ?? "http://127.0.0.1:8000").replace(/\/$/, "");

const nextConfig: NextConfig = {
  output: "standalone",
  // The repo root has its own package-lock.json (Laravel/Vite); pin the app root here.
  outputFileTracingRoot: __dirname,
  turbopack: { root: __dirname },
  poweredByHeader: false,
  async rewrites() {
    return [{ source: "/laravel/:path*", destination: `${BACKEND_URL}/:path*` }];
  },
  images: { unoptimized: true },
  async headers() {
    const frameAncestors = process.env.DEMO_FRAME_ANCESTORS ?? "'self' https://balitechsolution.com https://www.balitechsolution.com";
    return [
      { source: "/:path*", headers: [{ key: "X-Content-Type-Options", value: "nosniff" }] },
      // Only /demo may be embedded (iframe on the main website).
      { source: "/demo", headers: [{ key: "Content-Security-Policy", value: `frame-ancestors ${frameAncestors}` }] },
      { source: "/((?!demo$).*)", headers: [{ key: "X-Frame-Options", value: "SAMEORIGIN" }] },
    ];
  },
};

export default nextConfig;
