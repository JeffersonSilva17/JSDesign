export default function HealthPage() {
  return (
    <main className="page-shell">
      <section className="card" aria-labelledby="health-title">
        <p className="eyebrow">JS Designs</p>
        <h1 id="health-title">Fundação web operacional</h1>
        <p>
          Esta página valida que o Frontend/BFF em Next.js está renderizando. A rota
          server-side <code>/api/health</code> consulta a Laravel API.
        </p>
      </section>
    </main>
  );
}
