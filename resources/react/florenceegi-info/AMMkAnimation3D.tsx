import React, { useRef, useMemo } from 'react';
import { Canvas, useFrame } from '@react-three/fiber';
import { OrbitControls } from '@react-three/drei';
import * as THREE from 'three';

/**
 * AMMK ANIMATION - Asset Market Maker
 *
 * Concept: Marketplace Generator Visualization
 * - Central hub (AMMk Core) pulses
 * - Multiple marketplace nodes orbit and multiply
 * - Network connections visualize ecosystem growth
 * - Color morphing represents customization
 */

// Custom shader for holographic marketplace nodes
const nodeVertexShader = `
  uniform float uTime;
  varying vec2 vUv;
  varying vec3 vNormal;
  varying vec3 vPosition;

  void main() {
    vUv = uv;
    vNormal = normalize(normalMatrix * normal);
    vPosition = position;

    // Subtle pulsing effect
    vec3 pos = position;
    float pulse = sin(uTime * 2.0 + position.y * 3.0) * 0.05;
    pos += normal * pulse;

    gl_Position = projectionMatrix * modelViewMatrix * vec4(pos, 1.0);
  }
`;

const nodeFragmentShader = `
  uniform float uTime;
  uniform vec3 uColor;
  uniform float uOpacity;
  varying vec2 vUv;
  varying vec3 vNormal;
  varying vec3 vPosition;

  void main() {
    // Holographic effect
    vec3 viewDirection = normalize(cameraPosition - vPosition);
    float fresnel = pow(1.0 - dot(viewDirection, vNormal), 3.0);

    // Rainbow iridescence
    vec3 rainbow = vec3(
      sin(uTime * 0.5 + vPosition.x * 2.0) * 0.5 + 0.5,
      sin(uTime * 0.5 + vPosition.y * 2.0 + 2.0) * 0.5 + 0.5,
      sin(uTime * 0.5 + vPosition.z * 2.0 + 4.0) * 0.5 + 0.5
    );

    vec3 finalColor = mix(uColor, rainbow, fresnel * 0.6);
    float alpha = uOpacity * (0.4 + fresnel * 0.6);

    gl_FragColor = vec4(finalColor, alpha);
  }
`;

// Central AMMk Core
function AMMkCore() {
  const meshRef = useRef<THREE.Mesh>(null);
  const materialRef = useRef<THREE.ShaderMaterial>(null);

  useFrame(({ clock }) => {
    if (!meshRef.current || !materialRef.current) return;

    const time = clock.getElapsedTime();
    materialRef.current.uniforms.uTime.value = time;

    // Gentle rotation
    meshRef.current.rotation.y = time * 0.3;
    meshRef.current.rotation.x = Math.sin(time * 0.2) * 0.1;

    // Pulse scale
    const scale = 1.0 + Math.sin(time * 1.5) * 0.1;
    meshRef.current.scale.setScalar(scale);
  });

  const uniforms = useMemo(
    () => ({
      uTime: { value: 0 },
      uColor: { value: new THREE.Color('#8000ff') },
      uOpacity: { value: 0.9 }
    }),
    []
  );

  return (
    <mesh ref={meshRef}>
      <icosahedronGeometry args={[1.2, 2]} />
      <shaderMaterial
        ref={materialRef}
        vertexShader={nodeVertexShader}
        fragmentShader={nodeFragmentShader}
        uniforms={uniforms}
        transparent
        side={THREE.DoubleSide}
      />
    </mesh>
  );
}

// Marketplace Node (orbiting satellites)
interface MarketplaceNodeProps {
  index: number;
  total: number;
  radius: number;
  speed: number;
  color: string;
}

function MarketplaceNode({ index, total, radius, speed, color }: MarketplaceNodeProps) {
  const meshRef = useRef<THREE.Mesh>(null);
  const materialRef = useRef<THREE.ShaderMaterial>(null);
  const lineRef = useRef<THREE.Line>(null);

  useFrame(({ clock }) => {
    if (!meshRef.current || !materialRef.current) return;

    const time = clock.getElapsedTime();
    materialRef.current.uniforms.uTime.value = time;

    // Orbital motion
    const angle = (index / total) * Math.PI * 2 + time * speed;
    const x = Math.cos(angle) * radius;
    const z = Math.sin(angle) * radius;
    const y = Math.sin(time * 0.5 + index) * 0.3;

    meshRef.current.position.set(x, y, z);
    meshRef.current.rotation.y = time * 0.5 + index;
    meshRef.current.rotation.x = time * 0.3;

    // Update connection line to center
    if (lineRef.current) {
      const positions = lineRef.current.geometry.attributes.position;
      positions.setXYZ(0, 0, 0, 0); // Center
      positions.setXYZ(1, x, y, z); // Node position
      positions.needsUpdate = true;
    }
  });

  const uniforms = useMemo(
    () => ({
      uTime: { value: 0 },
      uColor: { value: new THREE.Color(color) },
      uOpacity: { value: 0.7 }
    }),
    [color]
  );

  // Connection line geometry
  const lineGeometry = useMemo(() => {
    const geometry = new THREE.BufferGeometry();
    const positions = new Float32Array(6); // 2 points * 3 coordinates
    geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
    return geometry;
  }, []);

  return (
    <group>
      {/* Connection line */}
      <line ref={lineRef} geometry={lineGeometry}>
        <lineBasicMaterial color={color} transparent opacity={0.2} />
      </line>

      {/* Marketplace node */}
      <mesh ref={meshRef}>
        <octahedronGeometry args={[0.3, 0]} />
        <shaderMaterial
          ref={materialRef}
          vertexShader={nodeVertexShader}
          fragmentShader={nodeFragmentShader}
          uniforms={uniforms}
          transparent
          side={THREE.DoubleSide}
        />
      </mesh>
    </group>
  );
}

// Particle Grid (background ambient)
function ParticleGrid() {
  const pointsRef = useRef<THREE.Points>(null);

  const { positions, colors } = useMemo(() => {
    const count = 800;
    const positions = new Float32Array(count * 3);
    const colors = new Float32Array(count * 3);

    for (let i = 0; i < count; i++) {
      // Spherical distribution
      const theta = Math.random() * Math.PI * 2;
      const phi = Math.acos(Math.random() * 2 - 1);
      const r = 8 + Math.random() * 4;

      positions[i * 3] = r * Math.sin(phi) * Math.cos(theta);
      positions[i * 3 + 1] = r * Math.sin(phi) * Math.sin(theta);
      positions[i * 3 + 2] = r * Math.cos(phi);

      // Gradient colors (purple to cyan)
      const t = Math.random();
      colors[i * 3] = 0.5 + t * 0.5; // R
      colors[i * 3 + 1] = 0.0 + t * 1.0; // G
      colors[i * 3 + 2] = 1.0 - t * 0.2; // B
    }

    return { positions, colors };
  }, []);

  useFrame(({ clock }) => {
    if (!pointsRef.current) return;
    pointsRef.current.rotation.y = clock.getElapsedTime() * 0.05;
  });

  return (
    <points ref={pointsRef}>
      <bufferGeometry>
        <bufferAttribute
          attach="attributes-position"
          count={positions.length / 3}
          array={positions}
          itemSize={3}
        />
        <bufferAttribute
          attach="attributes-color"
          count={colors.length / 3}
          array={colors}
          itemSize={3}
        />
      </bufferGeometry>
      <pointsMaterial
        size={0.05}
        vertexColors
        transparent
        opacity={0.6}
        sizeAttenuation
        blending={THREE.AdditiveBlending}
      />
    </points>
  );
}

// Main Scene
function Scene3D() {
  // 8 marketplace nodes (matching 8 user categories)
  const marketplaceNodes = [
    { color: '#ff6b9d', radius: 3.0, speed: 0.3 }, // Artisti
    { color: '#ffd700', radius: 3.5, speed: 0.25 }, // Musei
    { color: '#00d4ff', radius: 3.2, speed: 0.35 }, // Brand
    { color: '#7bed9f', radius: 3.8, speed: 0.28 }, // Onlus
    { color: '#ff6348', radius: 3.3, speed: 0.32 }, // Università
    { color: '#a29bfe', radius: 3.6, speed: 0.27 }, // Comuni
    { color: '#fd79a8', radius: 3.4, speed: 0.33 }, // Agenzie
    { color: '#00ffc8', radius: 3.7, speed: 0.29 }  // Studi legali
  ];

  return (
    <>
      {/* Ambient light */}
      <ambientLight intensity={0.3} />
      <pointLight position={[10, 10, 10]} intensity={0.8} color="#8000ff" />
      <pointLight position={[-10, -10, -10]} intensity={0.5} color="#00ffc8" />

      {/* Central AMMk Core */}
      <AMMkCore />

      {/* Orbiting Marketplace Nodes */}
      {marketplaceNodes.map((node, i) => (
        <MarketplaceNode
          key={i}
          index={i}
          total={marketplaceNodes.length}
          radius={node.radius}
          speed={node.speed}
          color={node.color}
        />
      ))}

      {/* Background particles */}
      <ParticleGrid />

      {/* Camera controls */}
      <OrbitControls
        enableZoom={false}
        enablePan={false}
        autoRotate
        autoRotateSpeed={0.5}
        maxPolarAngle={Math.PI / 1.8}
        minPolarAngle={Math.PI / 2.5}
      />
    </>
  );
}

// Main Component
export default function AMMkAnimation3D() {
  return (
    <div className="ammk-animation-container">
      <Canvas
        camera={{ position: [0, 3, 12], fov: 60 }}
        gl={{ antialias: true, alpha: true }}
        style={{ background: 'transparent' }}
      >
        <Scene3D />
      </Canvas>
    </div>
  );
}
