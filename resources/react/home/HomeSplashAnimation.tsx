import * as React from 'react';
import { useRef, useMemo } from 'react';
import { useFrame } from '@react-three/fiber';
import { OrbitControls, Environment } from '@react-three/drei';
import * as THREE from 'three';

/**
 * HOME SPLASH SCREEN - FLORENCEEGI
 *
 * Holographic Certification Animation (Phase 3 extracted)
 * - Rainbow iridescent sphere with QR code pattern
 * - Particle system in structured grid
 * - Auto-play 3D scene for 5 seconds
 *
 * Nota: La gestione del fade out e della visibilità è ora gestita
 * dal componente parent (home-splash.tsx)
 */

// ==================== HOLOGRAPHIC SPHERE ====================

function HolographicSphere() {
  const meshRef = useRef<THREE.Mesh>(null!);
  const materialRef = useRef<THREE.ShaderMaterial>(null!);

  const vertexShader = `
    uniform float uTime;
    uniform float uIntensity;

    varying vec2 vUv;
    varying vec3 vNormal;
    varying vec3 vPosition;

    void main() {
      vUv = uv;
      vNormal = normalize(normalMatrix * normal);

      vec3 pos = position;

      // Hard-edge faceting (geometric crystallization)
      vec3 facetedPos = floor(pos * 8.0) / 8.0;
      pos = mix(pos, facetedPos, uIntensity * 0.6);

      // QR code-like pattern on surface
      float qrX = step(0.5, fract(pos.x * 20.0));
      float qrZ = step(0.5, fract(pos.z * 20.0));
      float qrPattern = qrX * qrZ;

      pos += normal * qrPattern * 0.04 * uIntensity;

      // Holographic displacement waves
      float wave = sin(pos.x * 10.0 + uTime * 2.0) * cos(pos.z * 10.0 + uTime * 2.0);
      pos += normal * wave * 0.02 * uIntensity;

      vPosition = pos;

      gl_Position = projectionMatrix * modelViewMatrix * vec4(pos, 1.0);
    }
  `;

  const fragmentShader = `
    uniform float uTime;
    uniform float uIntensity;

    varying vec2 vUv;
    varying vec3 vNormal;
    varying vec3 vPosition;

    void main() {
      // Rainbow iridescence (like real hologram)
      vec3 viewDir = normalize(cameraPosition - vPosition);
      float fresnel = pow(1.0 - abs(dot(viewDir, vNormal)), 2.0);

      // Animated rainbow bands
      float bands = sin(vPosition.y * 30.0 - uTime * 3.0);
      vec3 rainbow = vec3(
        sin(bands * 3.14159) * 0.5 + 0.5,
        sin(bands * 3.14159 + 2.0) * 0.5 + 0.5,
        sin(bands * 3.14159 + 4.0) * 0.5 + 0.5
      );

      vec3 color = mix(vec3(0.1, 0.15, 0.3), rainbow, fresnel * uIntensity);

      // QR code pattern overlay
      float qrX = step(0.5, fract(vPosition.x * 20.0));
      float qrZ = step(0.5, fract(vPosition.z * 20.0));
      float qrPattern = qrX * qrZ;
      color += vec3(1.0) * qrPattern * 0.3 * uIntensity;

      // Metallic sheen
      color += fresnel * vec3(0.5, 0.7, 1.0) * uIntensity;

      gl_FragColor = vec4(color, 1.0);
    }
  `;

  const uniforms = useMemo(
    () => ({
      uTime: { value: 0 },
      uIntensity: { value: 1.0 }
    }),
    []
  );

  useFrame((state) => {
    if (materialRef.current) {
      materialRef.current.uniforms.uTime.value = state.clock.elapsedTime;
    }

    if (meshRef.current) {
      meshRef.current.rotation.y = state.clock.elapsedTime * 0.2;
      meshRef.current.rotation.x = Math.sin(state.clock.elapsedTime * 0.3) * 0.2;
    }
  });

  return (
    <mesh ref={meshRef}>
      <icosahedronGeometry args={[1, 64]} />
      <shaderMaterial
        ref={materialRef}
        vertexShader={vertexShader}
        fragmentShader={fragmentShader}
        uniforms={uniforms}
        side={THREE.DoubleSide}
      />
    </mesh>
  );
}

// ==================== PARTICLE GRID (QR CODE STYLE) ====================

function ParticleGrid() {
  const meshRef = useRef<THREE.InstancedMesh>(null!);
  const dummy = useMemo(() => new THREE.Object3D(), []);
  const count = 1000;

  useFrame((state) => {
    const gridSize = 10;

    for (let i = 0; i < count; i++) {
      const gridX = (i % gridSize) - gridSize / 2;
      const gridZ = Math.floor(i / gridSize) % gridSize - gridSize / 2;

      dummy.position.x = gridX * 0.4;
      dummy.position.z = gridZ * 0.4;
      dummy.position.y = Math.sin(state.clock.elapsedTime * 2 + gridX * 0.5) * 0.3;

      // Some particles blink on/off (QR code effect)
      const scale = ((i + Math.floor(state.clock.elapsedTime * 3)) % 3) === 0 ? 0 : 1;
      dummy.scale.setScalar(scale * 0.05);

      dummy.updateMatrix();
      meshRef.current.setMatrixAt(i, dummy.matrix);
    }

    meshRef.current.instanceMatrix.needsUpdate = true;
  });

  return (
    <instancedMesh ref={meshRef} args={[undefined, undefined, count]}>
      <sphereGeometry args={[1, 16, 16]} />
      <meshStandardMaterial
        color="#0088ff"
        emissive="#0066cc"
        emissiveIntensity={2.0}
        toneMapped={false}
      />
    </instancedMesh>
  );
}

// ==================== 3D SCENE ====================

function Scene3D() {
  return (
    <>
      <color attach="background" args={['#000814']} />
      <fog attach="fog" args={['#000814', 5, 15]} />

      <ambientLight intensity={0.1} />

      {/* Holographic multi-color lighting */}
      <pointLight position={[3, 2, 3]} intensity={1.5} color="#ff00ff" />
      <pointLight position={[-3, 2, -3]} intensity={1.5} color="#00ffff" />
      <pointLight position={[0, 3, 0]} intensity={1.5} color="#ffff00" />

      <HolographicSphere />
      <ParticleGrid />

      <Environment preset="city" />

      <OrbitControls
        enableZoom={false}
        enablePan={false}
        autoRotate
        autoRotateSpeed={0.5}
        maxPolarAngle={Math.PI / 2}
        minPolarAngle={Math.PI / 2}
      />
    </>
  );
}

// ==================== MAIN SPLASH COMPONENT ====================

/**
 * HomeSplashAnimation - Componente della scena 3D
 *
 * Ora è solo responsabile della resa della scena Three.js.
 * La gestione del timing e della visibilità è delegata al parent.
 */
const HomeSplashAnimation: React.FC = () => {
  return <Scene3D />;
};

export default HomeSplashAnimation;

