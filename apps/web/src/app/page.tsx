export default function Home() {
  return (
    <main className="page-shell">
      <section className="card" aria-labelledby="home-title">
        <p className="eyebrow">JS Designs</p>
        <h1 id="home-title">Loja online em preparação</h1>
        <p>
          Base técnica inicial com Next.js Frontend/BFF e Laravel API. A experiência visual
          completa será implementada nas próximas stories.
        </p>
        <a href="/health">Ver health local</a>
      </section>
    </main>
  );
}
