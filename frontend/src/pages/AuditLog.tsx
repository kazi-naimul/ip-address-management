import { useAuditLog } from "@/hooks/useAuditLog";
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { Badge } from "@/components/ui/badge";
import { Loader2 } from "lucide-react";

type BadgeVariant = "default" | "secondary" | "outline";

const actionVariantMap: Record<string, BadgeVariant> = {
  user_login: "secondary",
  ip_created: "default",
  ip_updated: "outline",
};

function formatAction(action: string) {
  switch (action) {
    case "user_login": return "login";
    case "ip_created": return "add";
    case "ip_updated": return "modify";
    default: return action;
  }
}

export default function AuditLog() {
  const { entries, isLoading, isError } = useAuditLog();

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-foreground">Audit Log</h1>
        <p className="text-sm text-muted-foreground">
          History of all logins, additions, and changes
        </p>
      </div>

      <Card>
        <CardHeader className="pb-3">
          <CardTitle className="text-base">Recent Activity</CardTitle>
        </CardHeader>
        <CardContent className="p-0">
          {isLoading ? (
            <div className="flex items-center justify-center py-12">
              <Loader2 className="h-6 w-6 animate-spin text-muted-foreground" />
            </div>
          ) : isError ? (
            <p className="text-center py-8 text-sm text-destructive">
              Failed to load audit logs
            </p>
          ) : (
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead className="w-44">Timestamp</TableHead>
                  <TableHead className="w-24">Action</TableHead>
                  <TableHead className="w-48">User</TableHead>
                  <TableHead>Details</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {entries.map((entry) => (
                  <TableRow key={entry.id}>
                    <TableCell className="text-sm text-muted-foreground">
                      {new Date(entry.created_at).toLocaleString()}
                    </TableCell>
                    <TableCell>
                      <Badge
                        variant={
                          actionVariantMap[entry.action] ?? "secondary"
                        }
                      >
                        {formatAction(entry.action)}
                      </Badge>
                    </TableCell>
                    <TableCell className="text-sm">
                      {entry.user?.email ?? "—"}
                    </TableCell>
                    <TableCell className="text-sm">
                      {formatDetails(entry.action, entry.details)}
                    </TableCell>
                  </TableRow>
                ))}
                {entries.length === 0 && (
                  <TableRow>
                    <TableCell
                      colSpan={4}
                      className="text-center py-8 text-muted-foreground"
                    >
                      No audit entries yet
                    </TableCell>
                  </TableRow>
                )}
              </TableBody>
            </Table>
          )}
        </CardContent>
      </Card>
    </div>
  );
}

function formatDetails(action: string, details: Record<string, unknown>): string {
  if (action === "user_login") {
    return "User logged in";
  }
  if (action === "ip_created") {
    const d = details as any;
    return `Added IP ${d?.ip_address ?? ""} with label "${d?.label ?? ""}"`;
  }
  if (action === "ip_updated") {
    const before = (details as any)?.before;
    const after = (details as any)?.after;
    if (before && after) {
      return `Changed label of ${after.ip_address} from "${before.label}" to "${after.label}"`;
    }
  }
  return JSON.stringify(details);
}
