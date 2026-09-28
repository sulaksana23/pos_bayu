"use client";

import { useState } from "react";
import { assetUrl } from "@/lib/config";
import { cn } from "@/lib/utils";

/** Product image with a colored-initials fallback when missing or failing to load. */
export function ProductThumb({ name, src, color, className, textClassName }: { name: string; src: string | null | undefined; color?: string | null; className?: string; textClassName?: string }) {
  const [failed, setFailed] = useState(false);
  const url = assetUrl(src);
  const c = color || "#64748b";

  return (
    <div className={cn("grid place-items-center overflow-hidden", className)} style={{ background: `${c}1a`, color: c }}>
      {url && !failed ? (
        // eslint-disable-next-line @next/next/no-img-element
        <img src={url} alt="" loading="lazy" onError={() => setFailed(true)} className="size-full object-cover transition-transform group-hover:scale-105" />
      ) : (
        <span className={cn("font-bold", textClassName)}>{name.slice(0, 2).toUpperCase()}</span>
      )}
    </div>
  );
}
