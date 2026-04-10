import { NavLink } from "react-router-dom";
import useAuth from "@/hooks/useAuth";
import { Network, List, ClipboardList, LogOut } from "lucide-react";
import { cn } from "@/lib/utils";

const navItems = [
  { to: "/dashboard", label: "IP Addresses", icon: Network },
  { to: "/audit", label: "Audit Log", icon: ClipboardList },
];

export default function AppLayout({ children }: { children: React.ReactNode }) {
  const { userEmail, logout } = useAuth();

  return (
    <div className="flex min-h-screen">
      {/* Sidebar */}
      <aside className="w-60 bg-nav text-nav-foreground flex flex-col shrink-0">
        <div className="p-5 border-b border-nav-foreground/10">
          <div className="flex items-center gap-2">
            <List className="h-6 w-6 text-primary" />
            <span className="font-bold text-lg tracking-tight">IPAM</span>
          </div>
          <p className="text-xs text-nav-foreground/60 mt-1">
            IP Address Manager
          </p>
        </div>
        <nav className="flex-1 p-3 space-y-1">
          {navItems.map(({ to, label, icon: Icon }) => (
            <NavLink
              key={to}
              to={to}
              className={({ isActive }) =>
                cn(
                  "flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-medium transition-colors",
                  isActive
                    ? "bg-primary/20 text-primary"
                    : "text-nav-foreground/70 hover:bg-nav-foreground/5 hover:text-nav-foreground"
                )
              }
            >
              <Icon className="h-4 w-4" />
              {label}
            </NavLink>
          ))}
        </nav>
        <div className="p-3 border-t border-nav-foreground/10">
          <p className="text-xs text-nav-foreground/50 px-3 mb-2 truncate">
            {userEmail}
          </p>
          <button
            onClick={logout}
            className="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-medium text-nav-foreground/70 hover:bg-nav-foreground/5 hover:text-nav-foreground transition-colors w-full"
          >
            <LogOut className="h-4 w-4" />
            Sign out
          </button>
        </div>
      </aside>

      {/* Main content */}
      <main className="flex-1 bg-background overflow-auto">
        <div className="max-w-5xl mx-auto p-6 md:p-8">{children}</div>
      </main>
    </div>
  );
}
