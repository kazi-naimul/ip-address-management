import { useState } from "react";
import useIpAddress from "@/hooks/useIpAddress";
import { validateIpAddress } from "@/lib/mock-data";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
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
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogFooter,
} from "@/components/ui/dialog";
import { Badge } from "@/components/ui/badge";
import { Plus, Pencil, Network, Search, Eye, Loader2 } from "lucide-react";
import IpDetailDialog from "@/components/IpDetailDialog";

export default function Dashboard() {
  const {
    records,
    isLoading,
    isCreating,
    isUpdating,
    showAddDialog,
    setShowAddDialog,
    editRecord,
    setEditRecord,
    viewRecord,
    setViewRecord,
    addIpAddress,
    updateLabel,
    openEdit,
    openView,
  } = useIpAddress();

  const [search, setSearch] = useState("");
  const [newIp, setNewIp] = useState("");
  const [newLabel, setNewLabel] = useState("");
  const [addError, setAddError] = useState("");
  const [editLabel, setEditLabel] = useState("");

  const filtered = records.filter(
    (r) =>
      r.ip_address.includes(search) ||
      r.label.toLowerCase().includes(search.toLowerCase())
  );

  const handleAdd = async () => {
    setAddError("");
    if (!newIp) { setAddError("IP address is required"); return; }
    if (!validateIpAddress(newIp)) { setAddError("Invalid IPv4 address"); return; }
    if (!newLabel.trim()) { setAddError("Label is required"); return; }

    const ok = await addIpAddress(newIp.trim(), newLabel.trim());
    if (ok) {
      setNewIp("");
      setNewLabel("");
    }
  };

  const handleEdit = async () => {
    if (!editRecord || !editLabel.trim()) return;
    await updateLabel(editRecord.id, editLabel.trim());
  };

  const openEditDialog = (record: typeof records[0]) => {
    openEdit(record);
    setEditLabel(record.label);
  };

  const assigned = records.filter((r) => r.label.toLowerCase() !== "spare").length;
  const spare = records.filter((r) => r.label.toLowerCase() === "spare").length;

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-6 w-6 animate-spin text-muted-foreground" />
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-foreground">IP Addresses</h1>
          <p className="text-sm text-muted-foreground">
            Manage your IP address assignments
          </p>
        </div>
        <Button onClick={() => setShowAddDialog(true)}>
          <Plus className="h-4 w-4 mr-2" />
          Add IP Address
        </Button>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <Card>
          <CardContent className="p-4 flex items-center gap-3">
            <div className="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center">
              <Network className="h-5 w-5 text-primary" />
            </div>
            <div>
              <p className="text-2xl font-bold">{records.length}</p>
              <p className="text-xs text-muted-foreground">Total IPs</p>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4 flex items-center gap-3">
            <div className="h-10 w-10 rounded-lg bg-success/10 flex items-center justify-center">
              <Network className="h-5 w-5 text-success" />
            </div>
            <div>
              <p className="text-2xl font-bold">{assigned}</p>
              <p className="text-xs text-muted-foreground">Assigned</p>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4 flex items-center gap-3">
            <div className="h-10 w-10 rounded-lg bg-warning/10 flex items-center justify-center">
              <Network className="h-5 w-5 text-warning" />
            </div>
            <div>
              <p className="text-2xl font-bold">{spare}</p>
              <p className="text-xs text-muted-foreground">Spare</p>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Search & Table */}
      <Card>
        <CardHeader className="pb-3">
          <div className="flex items-center justify-between">
            <CardTitle className="text-base">All Records</CardTitle>
            <div className="relative w-64">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
              <Input
                placeholder="Search IP or label..."
                className="pl-9"
                value={search}
                onChange={(e) => setSearch(e.target.value)}
              />
            </div>
          </div>
        </CardHeader>
        <CardContent className="p-0">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>IP Address</TableHead>
                <TableHead>Label</TableHead>
                <TableHead>Created By</TableHead>
                <TableHead>Last Updated</TableHead>
                <TableHead className="w-16"></TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {filtered.map((r) => (
                <TableRow key={r.id}>
                  <TableCell className="font-mono font-medium">
                    {r.ip_address}
                  </TableCell>
                  <TableCell>
                    <Badge
                      variant={
                        r.label.toLowerCase() === "spare"
                          ? "secondary"
                          : "default"
                      }
                    >
                      {r.label}
                    </Badge>
                  </TableCell>
                  <TableCell className="text-muted-foreground text-sm">
                    {r.creator?.email ?? "—"}
                  </TableCell>
                  <TableCell className="text-muted-foreground text-sm">
                    {new Date(r.updated_at).toLocaleDateString()}
                  </TableCell>
                  <TableCell className="flex gap-1">
                    <Button
                      variant="ghost"
                      size="icon"
                      onClick={() => openView(r)}
                    >
                      <Eye className="h-4 w-4" />
                    </Button>
                    <Button
                      variant="ghost"
                      size="icon"
                      onClick={() => openEditDialog(r)}
                    >
                      <Pencil className="h-4 w-4" />
                    </Button>
                  </TableCell>
                </TableRow>
              ))}
              {filtered.length === 0 && (
                <TableRow>
                  <TableCell
                    colSpan={5}
                    className="text-center py-8 text-muted-foreground"
                  >
                    No IP addresses found
                  </TableCell>
                </TableRow>
              )}
            </TableBody>
          </Table>
        </CardContent>
      </Card>

      {/* Add Dialog */}
      <Dialog open={showAddDialog} onOpenChange={setShowAddDialog}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Add IP Address</DialogTitle>
          </DialogHeader>
          <div className="space-y-4 py-2">
            <div className="space-y-2">
              <Label>IP Address</Label>
              <Input
                placeholder="e.g. 192.168.1.1"
                value={newIp}
                onChange={(e) => setNewIp(e.target.value)}
                className="font-mono"
              />
            </div>
            <div className="space-y-2">
              <Label>Label / Comment</Label>
              <Input
                placeholder="e.g. Web Server, Spare"
                value={newLabel}
                onChange={(e) => setNewLabel(e.target.value)}
              />
            </div>
            {addError && (
              <p className="text-sm text-destructive">{addError}</p>
            )}
          </div>
          <DialogFooter>
            <Button
              variant="outline"
              onClick={() => {
                setShowAddDialog(false);
                setAddError("");
                setNewIp("");
                setNewLabel("");
              }}
            >
              Cancel
            </Button>
            <Button onClick={handleAdd} disabled={isCreating}>
              {isCreating ? (
                <>
                  <Loader2 className="h-4 w-4 mr-2 animate-spin" />
                  Adding...
                </>
              ) : (
                "Add"
              )}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* Edit Dialog */}
      <Dialog
        open={!!editRecord}
        onOpenChange={(open) => !open && setEditRecord(null)}
      >
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Edit Label</DialogTitle>
          </DialogHeader>
          <div className="space-y-4 py-2">
            <div className="space-y-2">
              <Label>IP Address</Label>
              <Input
                value={editRecord?.ip_address ?? ""}
                disabled
                className="font-mono"
              />
            </div>
            <div className="space-y-2">
              <Label>Label / Comment</Label>
              <Input
                value={editLabel}
                onChange={(e) => setEditLabel(e.target.value)}
              />
            </div>
          </div>
          <DialogFooter>
            <Button
              variant="outline"
              onClick={() => setEditRecord(null)}
            >
              Cancel
            </Button>
            <Button onClick={handleEdit} disabled={isUpdating}>
              {isUpdating ? (
                <>
                  <Loader2 className="h-4 w-4 mr-2 animate-spin" />
                  Saving...
                </>
              ) : (
                "Save"
              )}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* View Dialog */}
      <IpDetailDialog
        record={viewRecord}
        open={!!viewRecord}
        onOpenChange={(open) => !open && setViewRecord(null)}
      />
    </div>
  );
}
