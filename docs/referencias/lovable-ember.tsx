import { createFileRoute } from "@tanstack/react-router";
import { MoodSwitcher, Nav } from "@/components/HeroChrome";

export const Route = createFileRoute("/ember")({
  head: () => ({
    meta: [
      { title: "Oxphyre — Ember" },
      { name: "description", content: "La brasa final. Calor en la oscuridad." },
    ],
  }),
  component: EmberPage,
});

function EmberPage() {
  return (
    <main className="relative min-h-screen overflow-hidden ember-bg text-white">
      <div className="pointer-events-none absolute inset-0 ember-glow-bottom" />
      <div className="pointer-events-none absolute inset-x-0 bottom-0 h-[40vh] ember-glow-floor" />
      <div className="grain absolute inset-0 pointer-events-none" />

      <Nav tone="ember" />

      <section className="relative z-10 mx-auto grid max-w-7xl grid-cols-1 gap-12 px-6 pb-32 pt-16 lg:grid-cols-12 lg:gap-10 lg:px-10 lg:pt-28">
        <div className="lg:col-span-7">
          <p className="eyebrow text-[oklch(0.82_0.14_70)]/80">— La brasa que queda</p>
          <h1 className="mt-8 font-serif text-[3.4rem] font-medium leading-[0.95] tracking-tight md:text-7xl lg:text-[5.5rem]">
            Cuando todo
            <br />
            <em className="text-white/85">se apaga, brilla.</em>
          </h1>
          <p className="mt-8 max-w-md text-[15px] leading-relaxed text-white/65">
            Tu negocio en 3D, modelado con la calidez de un fuego bajo. Atmósfera, materia y una luz
            que invita a quedarse.
          </p>
          <div className="mt-10 flex flex-wrap items-center gap-3">
            <button className="ember-button rounded-full px-6 py-3 text-sm font-semibold transition-transform hover:scale-[1.02]">
              Entrar al demo →
            </button>
            <button className="rounded-full border border-white/15 bg-transparent px-6 py-3 text-sm font-medium text-white transition-colors hover:bg-white/5">
              Cómo funciona
            </button>
          </div>

          <div className="mt-16 flex flex-wrap gap-3">
            <FloatStat label="Visitantes ahora" value="247" />
            <FloatStat label="Conversión" value="4.8×" />
            <FloatStat label="Tiempo medio" value="03:42" />
          </div>
        </div>

        <div className="relative lg:col-span-5">
          <div className="relative mx-auto aspect-square w-full max-w-md">
            <div className="absolute inset-8 rounded-full ember-orb pulse-soft drift" />
            <div className="absolute inset-0 rounded-full border border-[oklch(0.82_0.14_70)]/10" />
            <div className="absolute inset-12 rounded-full border border-[oklch(0.82_0.14_70)]/[0.06]" />
          </div>
        </div>
      </section>

      <section className="relative z-10 mx-auto grid max-w-7xl grid-cols-1 gap-4 px-6 pb-32 md:grid-cols-3 lg:px-10">
        {features.map((f, i) => (
          <article key={f.title} className="ember-card rounded-2xl p-6">
            <p className="eyebrow text-[oklch(0.82_0.14_70)]/70">{`0${i + 1}`}</p>
            <h3 className="mt-6 font-serif text-2xl tracking-tight">{f.title}</h3>
            <p className="mt-3 text-sm leading-relaxed text-white/55">{f.desc}</p>
          </article>
        ))}
      </section>

      <MoodSwitcher active="ember" />
    </main>
  );
}

function FloatStat({ label, value }: { label: string; value: string }) {
  return (
    <div className="ember-card rounded-xl px-4 py-3">
      <p className="eyebrow text-white/40">{label}</p>
      <p className="mt-1 font-serif text-xl tracking-tight text-[oklch(0.88_0.13_75)]">{value}</p>
    </div>
  );
}

const features = [
  { title: "Visitamos tu espacio", desc: "Una mañana es suficiente. Capturamos cada metro con precisión." },
  { title: "Lo reconstruimos", desc: "Three.js cuidando luz, materiales y proporción. Calidez real." },
  { title: "Lo entregamos vivo", desc: "Una URL que abre tu negocio. Tus visitantes entran, miran, vuelven." },
];
