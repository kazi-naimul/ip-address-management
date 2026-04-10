import { useIpAddressHistory } from "@/hooks/useAuditLog";
import type { IpAddressRecord } from "@/types/apiTypes";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import { Badge } from "@/components/ui/badge";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { Separator } from "@/components/ui/separator";
import { Loader2 } from "lucide-react";

type BadgeVariant = "default" | "secondary" | "outline";

const actionVariantMap: Record<string, BadgeVariant> = {
  ip_created: "default",
  ip_updated: "outline",
};

function formatAction(action: string) {
  switch (action) {
    case "ip_created": return "add";
    case "ip_updated": return "modify";
    default: return action;
  }
}

function formatDetails(action: string, details: Record<string, unknown>): string {
  if (action === "ip_created") {
    const d = details as any;
    return `Added with label "${d?.label ?? ""}"`;
  }
  if (action === "ip_updated") {
    const before = (details as any)?.before;
    const after = (details as any)?.after;
    if (before && after) {
      return `Label changed from "${before.label}" to "${after.label}"`;
    }
  }
  return JSON.stringify(details);
}

interface IpDetailDialogProps {
  record: IpAddressRecord | null;
  open: boolean;
  onOpenChange: (open: boolean) => void;
}

export default function IpDetailDialog({
  record,
  open,
  onOpenChange,
}: IpDetailDialogProps) {
  const { history, isLoading } = useIpAddressHistory(record?.id ?? null);

  if (!record) return null;

  const changes = history?.changes ?? [];

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="max-w-2xl max-h-[80vh] overflow-y-auto">
        <DialogHeader>
          <DialogTitle className="font-mono text-lg">
            {record.ip_address}
          </DialogTitle>
        </DialogHeader>

        <div className="space-y-4">
          {/* IP Details */}
          <div className="grid grid-cols-2 gap-4 text-sm">
            <div>
              <p className="text-muted-foreground">Label</p>
              <Badge
                variant={
                  record.label.toLowerCase() === "spare"
                    ? "secondary"
                    : "default"
                }
                className="mt-1"
              >
                {record.label}
              </Badge>
            </div>
            <div>
              <p className="text-muted-foreground">Created By</p>
              <p className="mt-1 font-medium">
                {record.creator?.email ?? "—"}
              </p>
            </div>
            <div>
              <p className="text-muted-foreground">Created At</p>
              <p className="mt-1">
                {new Date(record.created_at).toLocaleString()}
              </p>
            </div>
            <div>
              <p className="text-muted-foreground">Last Updated</p>
              <p className="mt-1">
                {new Date(record.updated_at).toLocaleString()}
              </p>
            </div>
          </div>

          <Separator />

          {/* Audit History */}
          <div>
            <h3 className="text-sm font-semibold mb-2">Audit History</h3>
            {isLoading ? (
              <div className="flex items-center justify-center py-6">
                <Loader2 className="h-5 w-5 animate-spin text-muted-foreground" />
              </div>
            ) : changes.length === 0 ? (
              <p className="text-sm text-muted-foreground">
                No audit entries for this IP.
              </p>
            ) : (
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead className="w-40">Timestamp</TableHead>
                    <TableHead className="w-20">Action</TableHead>
                    <TableHead>Details</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {changes.map((entry) => (
                    <TableRow key={entry.id}>
                      <TableCell className="text-xs text-muted-foreground">
                        {new Date(entry.created_at).toLocaleString()}
                      </TableCell>
                      <TableCell>
                        <Badge
                          variant={
                            actionVariantMap[entry.action] ?? "secondary"
                          }
                          className="text-xs"
                        >
                          {formatAction(entry.action)}
                        </Badge>
                      </TableCell>
                      <TableCell className="text-xs">
                        {formatDetails(entry.action, entry.details)}
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            )}
          </div>
        </div>
      </DialogContent>
    </Dialog>
  );
}
