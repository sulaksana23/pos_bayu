import type { HTMLAttributes, TdHTMLAttributes, ThHTMLAttributes } from "react";
import { cn } from "@/lib/utils";

export function Table({ className, ...props }: HTMLAttributes<HTMLTableElement>) {
  return (
    <div className="overflow-x-auto">
      <table className={cn("w-full text-left text-sm", className)} {...props} />
    </div>
  );
}

export function Th({ className, ...props }: ThHTMLAttributes<HTMLTableCellElement>) {
  return <th className={cn("border-b border-border bg-surface-2/60 px-4 py-2.5 text-xs font-medium whitespace-nowrap text-muted", className)} {...props} />;
}

export function Td({ className, ...props }: TdHTMLAttributes<HTMLTableCellElement>) {
  return <td className={cn("border-b border-border px-4 py-3 align-middle", className)} {...props} />;
}

export function Tr({ className, onClick, ...props }: HTMLAttributes<HTMLTableRowElement>) {
  return <tr onClick={onClick} className={cn("transition-colors last:[&>td]:border-b-0 hover:bg-surface-2/50", onClick && "cursor-pointer", className)} {...props} />;
}
