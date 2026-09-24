import { redirect } from "next/navigation";

export default function Home() {
  // No backend connected yet — show the self-contained cashier demo
  // instead of the dashboard (which needs a real API + login).
  redirect("/demo");
}
