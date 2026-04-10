# IP Address Management — Frontend

A React + TypeScript single-page application for managing IP addresses. Features a login page, a dashboard for viewing/creating/editing IP records, and an audit log view.

---

## Tech Stack

- **React** 18 with TypeScript
- **Vite** (build tool, dev server on port 8080)
- **Redux Toolkit** + RTK Query (state management & API calls)
- **React Router** v6 (routing)
- **Tailwind CSS** + shadcn/ui (styling & component library)
- **React Hook Form** + Zod (form validation)
- **Vitest** + Testing Library (unit tests)
- **Playwright** (end-to-end tests)

---

## Project Structure

```
frontend/
├── src/
│   ├── components/         # Reusable UI components (AppLayout, IpDetailDialog, shadcn/ui)
│   ├── constants/          # App-wide constants (auth config, etc.)
│   ├── hooks/              # Custom hooks (useAuth, useIpAddress, useAuditLog)
│   ├── lib/                # Utility functions and auth context
│   ├── pages/              # Route-level pages (Login, Dashboard, AuditLog)
│   ├── store/              # Redux store, RTK Query API slices
│   │   ├── features/auth/  # Auth slice (credentials & session)
│   │   └── services/       # API slices (auth, ipAddress, auditLog)
│   ├── types/              # Shared TypeScript types (apiTypes.ts)
│   ├── App.tsx             # Route definitions
│   └── main.tsx            # App entry point
├── public/                 # Static assets
├── .env                    # Environment variables (git-ignored)
├── vite.config.ts          # Vite configuration
├── tailwind.config.ts      # Tailwind configuration
└── playwright.config.ts    # Playwright configuration
```

---

## Prerequisites

- **Node.js** 18+ and **npm** (or **bun**)
- The backend API running and accessible (see [backend README](../backend/README.md))

---

## Getting Started

### 1. Enter the frontend directory

```bash
cd frontend/
```

### 2. Install dependencies

```bash
npm install

# Or with bun
bun install
```

### 3. Set up the environment file

```bash
cp .env .env.local    # or create .env if it does not exist
```

Set the API base URL in `.env`:

```env
VITE_API_BASE_URL=http://localhost:8081/api
```

Adjust the port to match your backend's `WEB_PORT` setting.

### 4. Start the development server

```bash
npm run dev

# Or with bun
bun dev
```

The app will be available at **http://localhost:8080**.

---

## Available Scripts

| Command              | Description                                  |
|----------------------|----------------------------------------------|
| `npm run dev`        | Start the Vite dev server (hot reload)       |
| `npm run build`      | Production build (outputs to `dist/`)        |
| `npm run build:dev`  | Development build (with source maps)         |
| `npm run preview`    | Serve the production build locally           |
| `npm run lint`       | Run ESLint                                   |

---

## Pages & Routes

| Route        | Component     | Auth required | Description                    |
|--------------|---------------|---------------|--------------------------------|
| `/`          | `Index`       | No            | Redirects to login or dashboard|
| `/login`     | `Login`       | No            | Login form                     |
| `/dashboard` | `Dashboard`   | Yes           | IP address list & management   |
| `/audit-log` | `AuditLog`    | Yes           | Audit log viewer               |
| `*`          | `NotFound`    | No            | 404 page                       |

---

## Authentication Flow

1. User submits credentials on the `/login` page.
2. The app calls `POST /login` on the backend.
3. On success, the returned Bearer token is stored in `localStorage` and the Redux auth slice.
4. All subsequent API requests include the token via the `Authorization` header (configured in `store/services/baseQuery.ts`).
5. On logout, the token is cleared from both `localStorage` and the Redux store, and the user is redirected to `/login`.

---

## State Management

The app uses **Redux Toolkit** with **RTK Query** for all server state:

| Slice / Service     | Responsibility                         |
|---------------------|----------------------------------------|
| `authSlice`         | Stores the current user and token      |
| `authApiSlice`      | `POST /login` mutation                 |
| `ipAddressApiSlice` | CRUD operations for IP addresses       |
| `auditLogApiSlice`  | Fetching audit logs and IP history     |

Custom hooks (`useAuth`, `useIpAddress`, `useAuditLog`) wrap the RTK Query hooks to keep components clean.

---

## Environment Variables

| Variable            | Description                       | Default                       |
|---------------------|-----------------------------------|-------------------------------|
| `VITE_API_BASE_URL` | Base URL of the backend API       | `http://localhost:8081/api`   |

---

## Building for Production

```bash
npm run build
```

Output is placed in `dist/`. Serve it with any static file server:

```bash
npm run preview       # Vite's built-in preview server
npx serve dist        # or with the `serve` package
```

For containerised deployments, point Nginx (or equivalent) at the `dist/` directory and configure it to serve `index.html` for all routes (required for client-side routing).

---

## Connecting to the Backend

Make sure the backend is running before starting the frontend. See the [backend README](../backend/README.md) for full setup instructions. The default configuration expects the backend at `http://localhost:8081`.
