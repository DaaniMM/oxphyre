(function () {
  'use strict';

  if (typeof THREE === 'undefined') return;

  const panel  = document.getElementById('auth-sphere-panel');
  const canvas = document.getElementById('auth-sphere-canvas');
  if (!panel || !canvas) return;

  const renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true });
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

  const scene  = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(55, 1, 0.1, 100);
  camera.position.set(0, 0, 4.5);

  // Esfera wireframe dorada
  const geo      = new THREE.SphereGeometry(1.8, 36, 24);
  const wire     = new THREE.WireframeGeometry(geo);
  const lineMat  = new THREE.LineBasicMaterial({
    color: 0xFEB354,
    transparent: true,
    opacity: 0.28,
  });
  const sphere = new THREE.LineSegments(wire, lineMat);
  scene.add(sphere);

  // Fondo sólido de la esfera para dar profundidad
  const solidMat = new THREE.MeshBasicMaterial({ color: 0x030303 });
  scene.add(new THREE.Mesh(new THREE.SphereGeometry(1.79, 32, 24), solidMat));

  // Luz puntual ámbar desde abajo-frente
  const pointLight = new THREE.PointLight(0xFEB354, 1.5, 12);
  pointLight.position.set(0, -2.5, 2);
  scene.add(pointLight);
  scene.add(new THREE.AmbientLight(0xffffff, 0.05));

  let mouse = { x: 0, y: 0 };

  document.addEventListener('mousemove', e => {
    mouse.x = (e.clientX / window.innerWidth  - 0.5) * 2;
    mouse.y = (e.clientY / window.innerHeight - 0.5) * 2;
  });

  function resize() {
    const w = panel.clientWidth  || window.innerWidth;
    const h = panel.clientHeight || window.innerHeight;
    renderer.setSize(w, h, false);
    camera.aspect = w / h;
    camera.updateProjectionMatrix();
  }

  window.addEventListener('resize', resize);
  resize();

  let t = 0;
  let rotX = 0, rotY = 0;

  function animate() {
    requestAnimationFrame(animate);
    t += 0.004;

    // Parallax suave con el mouse
    rotY += (mouse.x * 0.25 - rotY) * 0.04;
    rotX += (-mouse.y * 0.1  - rotX) * 0.04;

    sphere.rotation.y = t + rotY;
    sphere.rotation.x = rotX;

    renderer.render(scene, camera);
  }

  animate();
})();
