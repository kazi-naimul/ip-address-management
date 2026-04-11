# IP Address Management

A full-stack application for managing IP addresses with audit logging and token-based authentication.

## Structure

```
ip-address-management/
├── backend/    # Laravel 10 REST API (PHP 8.2, MySQL 8.0, Docker)
└── frontend/   # React 18 + TypeScript SPA (Vite, Redux Toolkit, Tailwind CSS)
```

## Quick Start

### 1. Start the backend

```bash
cd backend
cp docker/.envs/app.env.example docker/.envs/app.env
make setup
make up
make key-generate
make migrate
make seed
```

API will be available at **http://localhost:8081/api**

Default credentials: `test@example.com` / `password123`

### 2. Start the frontend

```bash
cd frontend
npm install
# Set VITE_API_BASE_URL=http://localhost:8081/api in .env
npm run dev
```

App will be available at **http://localhost:8080**

## Documentation

- [Backend README](backend/README.md) — API reference, Docker setup, environment variables, tests
- [Frontend README](frontend/README.md) — Pages, state management, build instructions
