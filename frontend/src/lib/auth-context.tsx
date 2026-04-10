// Re-export from the canonical hook so existing imports keep working.
// All state now lives in Redux — this file is just a compatibility shim.
export { default as useAuth } from "@/hooks/useAuth";

// The AuthProvider is no longer needed (Redux Provider in main.tsx handles this),
// but we keep it here as a no-op so any leftover usage doesn't break.
export function AuthProvider({ children }: { children: React.ReactNode }) {
  return <>{children}</>;
}
