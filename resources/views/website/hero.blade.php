<style>
.hero         { min-height:100vh; display:flex; align-items:center; justify-content:center; position:relative; overflow:hidden; background:var(--dark-3); padding-top:70px; }
.hero-bg      { position:absolute; inset:0; background:radial-gradient(ellipse 60% 60% at 70% 50%, rgba(216,33,40,0.12) 0%, transparent 70%), radial-gradient(ellipse 40% 40% at 20% 80%, rgba(240,180,41,0.06) 0%, transparent 60%); }
.hero-bg::before { content:''; position:absolute; inset:0; background-image:repeating-linear-gradient(45deg,transparent,transparent 40px,rgba(255,255,255,0.01) 40px,rgba(255,255,255,0.01) 41px); }
.hero-bg::after  { content:''; position:absolute; left:0; top:0; bottom:0; width:4px; background:linear-gradient(to bottom,transparent,var(--red),transparent); }
.hero-content { position:relative; z-index:2; text-align:center; padding:2rem 1.25rem; max-width:900px; width:100%; }
.hero-eyebrow { display:inline-block; background:rgba(216,33,40,0.15); border:1px solid rgba(216,33,40,0.4); color:var(--red); font-size:0.7rem; font-weight:700; letter-spacing:0.2em; text-transform:uppercase; padding:0.4rem 1.2rem; border-radius:2px; margin-bottom:1.5rem; animation:fadeUp 0.6s ease both; }
.hero-title   { font-family:'Bebas Neue',sans-serif; font-size:clamp(2.8rem,9vw,7rem); line-height:1; letter-spacing:0.04em; animation:fadeUp 0.6s 0.1s ease both; }
.hero-title .accent { color:var(--red); }
.hero-title .gold   { color:var(--gold); }
.hero-sub     { font-size:0.95rem; color:rgba(255,255,255,0.55); margin:1.5rem auto 2.5rem; max-width:500px; line-height:1.7; animation:fadeUp 0.6s 0.2s ease both; }
.hero-actions { display:flex; gap:1rem; justify-content:center; flex-wrap:wrap; animation:fadeUp 0.6s 0.3s ease both; }
.btn-primary  { background:var(--red); color:white; padding:0.9rem 2rem; border-radius:4px; font-weight:700; font-size:0.82rem; letter-spacing:0.1em; text-transform:uppercase; text-decoration:none; border:2px solid var(--red); transition:all 0.2s; }
.btn-primary:hover { background:var(--red-dark); transform:translateY(-2px); box-shadow:0 8px 24px rgba(216,33,40,0.4); }
.btn-outline  { background:transparent; color:white; padding:0.9rem 2rem; border-radius:4px; font-weight:700; font-size:0.82rem; letter-spacing:0.1em; text-transform:uppercase; text-decoration:none; border:2px solid rgba(255,255,255,0.2); transition:all 0.2s; }
.btn-outline:hover { border-color:var(--gold); color:var(--gold); }
.hero-stats   { display:flex; justify-content:center; gap:3rem; margin-top:4rem; padding-top:2.5rem; border-top:1px solid rgba(255,255,255,0.06); animation:fadeUp 0.6s 0.4s ease both; flex-wrap:wrap; }
.stat-item    { text-align:center; min-width:80px; }
.stat-number  { font-family:'Bebas Neue',sans-serif; font-size:2.2rem; color:var(--gold); line-height:1; }
.stat-label   { font-size:0.65rem; color:var(--muted); letter-spacing:0.12em; text-transform:uppercase; margin-top:0.25rem; }

@media (max-width: 768px) {
    .hero-eyebrow { font-size:0.6rem; padding:0.35rem 0.9rem; }
    .hero-sub { font-size:0.85rem; }
    .hero-actions { flex-direction:column; align-items:center; }
    .btn-primary, .btn-outline { width:100%; max-width:280px; text-align:center; }
    .hero-stats { gap:1.5rem; margin-top:3rem; padding-top:2rem; }
    .stat-number { font-size:1.8rem; }
    .stat-label  { font-size:0.6rem; }
}
</style>

<section class="hero" id="inicio">
    <div class="hero-bg"></div>
    <div class="hero-content">
        <div class="hero-eyebrow">🚗 Servicio profesional de lavado y lubricación</div>
        <h1 class="hero-title">
            LAVADORA Y<br>
            <span class="accent">LUBRICADORA</span><br>
            <span class="gold">ENDARA</span>
        </h1>
        <p class="hero-sub">Tu vehículo merece el mejor cuidado. Lavado completo, express, premium y servicios de lubricación profesional en un solo lugar.</p>
        <div class="hero-actions">
            <a href="#servicios" class="btn-primary">Ver Servicios</a>
            <a href="#contacto"  class="btn-outline">Contáctanos</a>
        </div>
        <div class="hero-stats">
            <div class="stat-item">
                <div class="stat-number">7+</div>
                <div class="stat-label">Servicios disponibles</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">100%</div>
                <div class="stat-label">Satisfacción garantizada</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $empresa->telefono ?? '—' }}</div>
                <div class="stat-label">Llámanos</div>
            </div>
        </div>
    </div>
</section>