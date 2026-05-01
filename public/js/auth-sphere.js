(function () {
  'use strict';
  if (typeof THREE === 'undefined') return;

  const panel  = document.getElementById('auth-brand-panel');
  const canvas = document.getElementById('auth-sphere-canvas');
  if (!panel || !canvas) return;

  const SIZE = 2.0;

  const renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true });
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

  const scene  = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(50, 1, 0.1, 100);
  camera.position.set(0, 0, 5);

  // Glow exterior BackSide — escala respira
  const glowMesh = new THREE.Mesh(
    new THREE.SphereGeometry(SIZE * 1.4, 32, 32),
    new THREE.MeshBasicMaterial({ color: 0xffa040, side: THREE.BackSide, transparent: true, opacity: 0.06 })
  );
  scene.add(glowMesh);

  // Wireframe
  const wireMesh = new THREE.Mesh(
    new THREE.SphereGeometry(SIZE, 64, 64),
    new THREE.MeshBasicMaterial({ color: 0xffb060, wireframe: true, transparent: true, opacity: 0.25 })
  );
  scene.add(wireMesh);

  // Core oscuro
  const coreMesh = new THREE.Mesh(
    new THREE.SphereGeometry(SIZE * 0.98, 32, 32),
    new THREE.MeshBasicMaterial({ color: 0x1a0f08, transparent: true, opacity: 0.4 })
  );
  scene.add(coreMesh);

  // Núcleo central sólido
  const nucMesh = new THREE.Mesh(
    new THREE.SphereGeometry(SIZE * 0.15, 16, 16),
    new THREE.MeshBasicMaterial({ color: 0xffd28a })
  );
  scene.add(nucMesh);

  const meshes = [glowMesh, wireMesh, coreMesh, nucMesh];

  const setSize = () => {
    const w = panel.clientWidth;
    const h = panel.clientHeight;
    renderer.setSize(w, h, false);
    camera.aspect = w / h;
    camera.updateProjectionMatrix();
  };

  new ResizeObserver(setSize).observe(panel);
  setSize();

  const clock = new THREE.Clock();

  function animate() {
    requestAnimationFrame(animate);
    const delta = clock.getDelta();

    // clock.elapsedTime se actualiza con cada getDelta()
    glowMesh.scale.setScalar(1 + Math.sin(clock.elapsedTime * 0.8) * 0.05);

    meshes.forEach(m => {
      m.rotation.y += delta * 0.08;
      m.rotation.x += delta * 0.02;
    });

    renderer.render(scene, camera);
  }

  animate();
})();
