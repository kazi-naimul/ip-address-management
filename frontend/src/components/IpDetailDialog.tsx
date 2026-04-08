import { type IpRecord, mockAuditLog } from "@/lib/mock-data";
import { Dialog, DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { Badge } from "@/components/ui/badge";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Separator } from "@/components/ui/separator";

const actionColors: Record<string, "default" | "secondary" | "outline"> = {
  login: "secondary",
  add: "default",
  modify: "outline",
};

interface IpDetailDialogProps {
  record: IpRecord | null;
  open: boolean;
  onOpenChange: (open: boolean) => void;
}

export default function IpDetailDialog({ record, open, onOpenChange }: IpDetailDialogProps) {
  if (!record) return null;

  const relatedAudit = mockAuditLog.filter((entry) =>
    entry.details.includes(record.ipAddress)
  );

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="max-w-2xl max-h-[80vh] overflow-y-auto">
        <DialogHeader>
          <DialogTitle className="font-mono text-lg">{record.ipAddress}</DialogTitle>
        </DialogHeader>

        <div className="space-y-4">
          {/* Details */}
          <div className="grid grid-cols-2 gap-4 text-sm">
            <div>
              <p className="text-muted-foreground">Label</p>
              <Badge variant={record.label === "Spare" ? "secondary" : "default"} className="mt-1">
                {record.label}
              </Badge>
            </div>
            <div>
              <p className="text-muted-foreground">Created By</p>
              <p className="mt-1 font-medium">{record.createdBy}</p>
            </div>
            <div>
              <p className="text-muted-foreground">Created At</p>
              <p className="mt-1">{new Date(record.createdAt).toLocaleString()}</p>
            </div>
            <div>
              <p className="text-muted-foreground">Last Updated</p>
              <p className="mt-1">{new Date(record.updatedAt).toLocaleString()}</p>
            </div>
          </div>

          <Separator />

          {/* Audit Log */}
          <div>
            <h3 className="text-sm font-semibold mb-2">Audit History</h3>
            {relatedAudit.length === 0 ? (
              <p className="text-sm text-muted-foreground">No audit entries for this IP.</p>
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
                  {relatedAudit.map((entry) => (
                    <TableRow key={entry.id}>
                      <TableCell className="text-xs text-muted-foreground">
                        {new Date(entry.timestamp).toLocaleString()}
                      </TableCell>
                      <TableCell>
                        <Badge variant={actionColors[entry.action] || "secondary"} className="text-xs">
                          {entry.action}
                        </Badge>
                      </TableCell>
                      <TableCell className="text-xs">{entry.details}</TableCell>
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
