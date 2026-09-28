import type { ReactNode } from "react";
import { cn } from "@/lib/utils";

export type Tone = "green" | "red" | "amber" | "blue" | "gray" | "violet";

const tones: Record<Tone, string> = {
  green: "bg-emerald-500/10 text-emerald-700 ring-emerald-600/20 dark:text-emerald-400",
  red: "bg-red-500/10 text-red-700 ring-red-600/20 dark:text-red-400",
  amber: "bg-amber-500/10 text-amber-700 ring-amber-600/20 dark:text-amber-400",
  blue: "bg-sky-500/10 text-sky-700 ring-sky-600/20 dark:text-sky-400",
  gray: "bg-slate-500/10 text-slate-600 ring-slate-500/20 dark:text-slate-400",
  violet: "bg-violet-500/10 text-violet-700 ring-violet-600/20 dark:text-violet-400",
};

export function Badge({ tone = "gray", children, className, dot }: { tone?: Tone; children: ReactNode; className?: string; dot?: boolean }) {
  return (
    <span className={cn("inline-flex items-center gap-1.5 rounded-md px-2 py-0.5 text-xs font-medium whitespace-nowrap ring-1 ring-inset", tones[tone], className)}>
      {dot && <span className="size-1.5 rounded-full bg-current" />}
      {children}
    </span>
  );
}
