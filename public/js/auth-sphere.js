(function () {
  'use strict';
  if (typeof THREE === 'undefined') return;

  const panel  = document.getElementById('auth-sphere-panel');
  const canvas = document.getElementById('auth-sphere-canvas');
  if (!panel || !canvas) return;

  const SIZE = 2.2;

  const renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true });
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

  const scene  = new THREE.Scene();
  // aspect fijo a 1 porque el canvas es siempre cuadrado (100vh × 100vh)
  const camera = new THREE.PerspectiveCamera(50, 1, 0.1, 100);
  camera.position.set(0, 0, 5);

  // ── Glow exterior: BackSide, respira con sin()
  const glowMesh = new THREE.Mesh(
    new THREE.SphereGeometry(SIZE * 1.4, 32, 32),
    new THREE.MeshBasicMaterial({ color: 0xffa040, side: THREE.BackSide, transparent: true, opacity: 0.06 })
  );
  scene.add(glowMesh);

  // ── Wireframe
  const wireMesh = new THREE.Mesh(
    new THREE.SphereGeometry(SIZE, 64, 64),
    new THREE.MeshBasicMaterial({ color: 0xffb060, wireframe: true, transparent: true, opacity: 0.25 })
  );
  scene.add(wireMesh);

  // ── Core oscuro
  const coreMesh = new THREE.Mesh(
    new THREE.SphereGeometry(SIZE * 0.98, 32, 32),
    new THREE.MeshBasicMaterial({ color: 0x1a0f08, transparent: true, opacity: 0.4 })
  );
  scene.add(coreMesh);

  // ── Núcleo central sólido
  const nucMesh = new THREE.Mesh(
    new THREE.SphereGeometry(SIZE * 0.15, 16, 16),
    new THREE.MeshBasicMaterial({ color: 0xffd28a })
  );
  scene.add(nucMesh);

  const meshes = [glowMesh, wireMesh, coreMesh, nucMesh];

  // ── Resize: buffer cuadrado de window.innerHeight × window.innerHeight
  const setSize = () => {
    const s = window.innerHeight;
    renderer.setSize(s, s, false); // false = no sobrescribir CSS
    // camera.aspect = 1 siempre (canvas cuadrado)
    camera.updateProjectionMatrix();
  };

  const resizeObserver = new ResizeObserver(setSize);
  resizeObserver.observe(panel);
  setSize();

  // ── Parallax: CSS custom properties en el canvas (sin tocar el canvas WebGL)
  window.addEventListener('mousemove', e => {
    const nx = (e.clientX / window.innerWidth  - 0.5) * 2;
    const ny = (e.clientY / window.innerHeight - 0.5) * 2;
    canvas.style.setProperty('--ox-sx', (-nx * 14).toFixed(2) + 'px');
    canvas.style.setProperty('--ox-sy', (-ny * 14).toFixed(2) + 'px');
  });

  // ── Loop con delta real de THREE.Clock
  const clock = new THREE.Clock();
  let elapsed = 0;

  function animate() {
    requestAnimationFrame(animate);
    const delta = clock.getDelta();
    elapsed += delta;

    glowMesh.scale.setScalar(1 + Math.sin(elapsed * 0.8) * 0.05);

    meshes.forEach(m => {
      m.rotation.y += delta * 0.08;
      m.rotation.x += delta * 0.02;
    });

    renderer.render(scene, camera);
  }

  animate();
})();
