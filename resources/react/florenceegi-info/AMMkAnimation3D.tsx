import React, { useRef, useMemo } from 'react';
import { Canvas, useFrame } from '@react-three/fiber';
import { OrbitControls } from '@react-three/drei';
import * as THREE from 'three';

/**
 * AMMk ANIMATION - Asset Market Maker
 *
 * Concept: Flowing, organic marketplace energy
 * - Fluid blob morphing (metaball-like)
 * - Soft glowing particles
 * - Flowing waves and streams
 * - NO hard geometry, everything is smooth and dynamic
 *
 * Colors: Purple/Gold gradient (brand colors)
 */

// ==================== ORGANIC BLOB SHADER ====================

const blobVertexShader = `
  uniform float uTime;
  uniform float uNoiseStrength;
  varying vec3 vNormal;
  varying vec3 vPosition;
  varying float vDisplacement;

  // Simplex noise functions
  vec3 mod289(vec3 x) { return x - floor(x * (1.0 / 289.0)) * 289.0; }
  vec4 mod289(vec4 x) { return x - floor(x * (1.0 / 289.0)) * 289.0; }
  vec4 permute(vec4 x) { return mod289(((x*34.0)+1.0)*x); }
  vec4 taylorInvSqrt(vec4 r) { return 1.79284291400159 - 0.85373472095314 * r; }

  float snoise(vec3 v) {
    const vec2 C = vec2(1.0/6.0, 1.0/3.0);
    const vec4 D = vec4(0.0, 0.5, 1.0, 2.0);
    vec3 i = floor(v + dot(v, C.yyy));
    vec3 x0 = v - i + dot(i, C.xxx);
    vec3 g = step(x0.yzx, x0.xyz);
    vec3 l = 1.0 - g;
    vec3 i1 = min(g.xyz, l.zxy);
    vec3 i2 = max(g.xyz, l.zxy);
    vec3 x1 = x0 - i1 + C.xxx;
    vec3 x2 = x0 - i2 + C.yyy;
    vec3 x3 = x0 - D.yyy;
    i = mod289(i);
    vec4 p = permute(permute(permute(
              i.z + vec4(0.0, i1.z, i2.z, 1.0))
            + i.y + vec4(0.0, i1.y, i2.y, 1.0))
            + i.x + vec4(0.0, i1.x, i2.x, 1.0));
    float n_ = 0.142857142857;
    vec3 ns = n_ * D.wyz - D.xzx;
    vec4 j = p - 49.0 * floor(p * ns.z * ns.z);
    vec4 x_ = floor(j * ns.z);
    vec4 y_ = floor(j - 7.0 * x_);
    vec4 x = x_ *ns.x + ns.yyyy;
    vec4 y = y_ *ns.x + ns.yyyy;
    vec4 h = 1.0 - abs(x) - abs(y);
    vec4 b0 = vec4(x.xy, y.xy);
    vec4 b1 = vec4(x.zw, y.zw);
    vec4 s0 = floor(b0)*2.0 + 1.0;
    vec4 s1 = floor(b1)*2.0 + 1.0;
    vec4 sh = -step(h, vec4(0.0));
    vec4 a0 = b0.xzyw + s0.xzyw*sh.xxyy;
    vec4 a1 = b1.xzyw + s1.xzyw*sh.zzww;
    vec3 p0 = vec3(a0.xy, h.x);
    vec3 p1 = vec3(a0.zw, h.y);
    vec3 p2 = vec3(a1.xy, h.z);
    vec3 p3 = vec3(a1.zw, h.w);
    vec4 norm = taylorInvSqrt(vec4(dot(p0,p0), dot(p1,p1), dot(p2,p2), dot(p3,p3)));
    p0 *= norm.x; p1 *= norm.y; p2 *= norm.z; p3 *= norm.w;
    vec4 m = max(0.6 - vec4(dot(x0,x0), dot(x1,x1), dot(x2,x2), dot(x3,x3)), 0.0);
    m = m * m;
    return 42.0 * dot(m*m, vec4(dot(p0,x0), dot(p1,x1), dot(p2,x2), dot(p3,x3)));
  }

  void main() {
    vNormal = normalize(normalMatrix * normal);
    vPosition = position;

    // Multi-layered organic noise displacement
    float noise1 = snoise(position * 1.5 + uTime * 0.3);
    float noise2 = snoise(position * 3.0 + uTime * 0.5) * 0.5;
    float noise3 = snoise(position * 6.0 + uTime * 0.7) * 0.25;

    float totalNoise = (noise1 + noise2 + noise3) * uNoiseStrength;
    vDisplacement = totalNoise;

    vec3 newPosition = position + normal * totalNoise;

    gl_Position = projectionMatrix * modelViewMatrix * vec4(newPosition, 1.0);
  }
`;

const blobFragmentShader = `
  uniform float uTime;
  varying vec3 vNormal;
  varying vec3 vPosition;
  varying float vDisplacement;

  void main() {
    // Fresnel for soft glow edges
    vec3 viewDirection = normalize(cameraPosition - vPosition);
    float fresnel = pow(1.0 - abs(dot(viewDirection, vNormal)), 2.5);

    // Animated gradient purple -> gold
    float gradientShift = sin(uTime * 0.5 + vPosition.y * 2.0) * 0.5 + 0.5;

    vec3 purple = vec3(0.4, 0.1, 0.6);
    vec3 gold = vec3(0.85, 0.65, 0.2);
    vec3 cyan = vec3(0.1, 0.6, 0.8);

    // Mix colors based on position and time
    vec3 baseColor = mix(purple, gold, gradientShift);
    baseColor = mix(baseColor, cyan, fresnel * 0.3);

    // Add displacement-based color variation
    baseColor += vec3(vDisplacement * 0.3);

    // Soft glow at edges
    float glow = fresnel * 0.8;
    vec3 glowColor = mix(gold, vec3(1.0, 0.9, 0.7), fresnel);

    vec3 finalColor = baseColor + glowColor * glow;

    // Pulsing alpha for ethereal feel
    float alpha = 0.7 + sin(uTime * 2.0) * 0.1;
    alpha += fresnel * 0.3;

    gl_FragColor = vec4(finalColor, alpha);
  }
`;

// ==================== ORGANIC BLOB ====================

function OrganicBlob() {
  const meshRef = useRef<THREE.Mesh>(null);
  const materialRef = useRef<THREE.ShaderMaterial>(null);

  const uniforms = useMemo(() => ({
    uTime: { value: 0 },
    uNoiseStrength: { value: 0.4 }
  }), []);

  useFrame(({ clock }) => {
    if (materialRef.current) {
      materialRef.current.uniforms.uTime.value = clock.getElapsedTime();
    }
    if (meshRef.current) {
      meshRef.current.rotation.y = clock.getElapsedTime() * 0.1;
      meshRef.current.rotation.x = Math.sin(clock.getElapsedTime() * 0.15) * 0.2;
    }
  });

  return (
    <mesh ref={meshRef} position={[0, 0, 0]}>
      <sphereGeometry args={[2, 128, 128]} />
      <shaderMaterial
        ref={materialRef}
        vertexShader={blobVertexShader}
        fragmentShader={blobFragmentShader}
        uniforms={uniforms}
        transparent
        side={THREE.DoubleSide}
        depthWrite={false}
      />
    </mesh>
  );
}

// ==================== FLOWING PARTICLES ====================

function FlowingParticles() {
  const pointsRef = useRef<THREE.Points>(null);
  const count = 800;

  const [positions, velocities, colors] = useMemo(() => {
    const positions = new Float32Array(count * 3);
    const velocities = new Float32Array(count * 3);
    const colors = new Float32Array(count * 3);

    for (let i = 0; i < count; i++) {
      // Start in a sphere
      const theta = Math.random() * Math.PI * 2;
      const phi = Math.acos(Math.random() * 2 - 1);
      const r = 3 + Math.random() * 4;

      positions[i * 3] = r * Math.sin(phi) * Math.cos(theta);
      positions[i * 3 + 1] = r * Math.sin(phi) * Math.sin(theta);
      positions[i * 3 + 2] = r * Math.cos(phi);

      // Random velocities for flow
      velocities[i * 3] = (Math.random() - 0.5) * 0.02;
      velocities[i * 3 + 1] = (Math.random() - 0.5) * 0.02;
      velocities[i * 3 + 2] = (Math.random() - 0.5) * 0.02;

      // Purple to gold to cyan
      const t = Math.random();
      if (t < 0.33) {
        colors[i * 3] = 0.4 + Math.random() * 0.2;
        colors[i * 3 + 1] = 0.1;
        colors[i * 3 + 2] = 0.6 + Math.random() * 0.2;
      } else if (t < 0.66) {
        colors[i * 3] = 0.85;
        colors[i * 3 + 1] = 0.65 + Math.random() * 0.2;
        colors[i * 3 + 2] = 0.2;
      } else {
        colors[i * 3] = 0.1;
        colors[i * 3 + 1] = 0.6 + Math.random() * 0.2;
        colors[i * 3 + 2] = 0.8;
      }
    }

    return [positions, velocities, colors];
  }, []);

  const geometry = useMemo(() => {
    const geo = new THREE.BufferGeometry();
    geo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
    geo.setAttribute('color', new THREE.BufferAttribute(colors, 3));
    return geo;
  }, [positions, colors]);

  useFrame(({ clock }) => {
    if (pointsRef.current) {
      const time = clock.getElapsedTime();
      const posArray = pointsRef.current.geometry.attributes.position.array as Float32Array;

      for (let i = 0; i < count; i++) {
        const i3 = i * 3;

        // Orbital flow around center
        const x = posArray[i3];
        const z = posArray[i3 + 2];
        const angle = Math.atan2(z, x);
        const radius = Math.sqrt(x * x + z * z);

        const newAngle = angle + 0.002;
        posArray[i3] = Math.cos(newAngle) * radius;
        posArray[i3 + 2] = Math.sin(newAngle) * radius;

        // Gentle vertical wave
        posArray[i3 + 1] += Math.sin(time + i * 0.1) * 0.005;

        // Keep within bounds
        const dist = Math.sqrt(posArray[i3] ** 2 + posArray[i3 + 1] ** 2 + posArray[i3 + 2] ** 2);
        if (dist > 8) {
          const scale = 4 / dist;
          posArray[i3] *= scale;
          posArray[i3 + 1] *= scale;
          posArray[i3 + 2] *= scale;
        }
      }

      pointsRef.current.geometry.attributes.position.needsUpdate = true;
      pointsRef.current.rotation.y = time * 0.05;
    }
  });

  return (
    <points ref={pointsRef} geometry={geometry}>
      <pointsMaterial
        size={0.08}
        vertexColors
        transparent
        opacity={0.6}
        sizeAttenuation
        blending={THREE.AdditiveBlending}
        depthWrite={false}
      />
    </points>
  );
}

// ==================== GLOW RINGS ====================

function GlowRings() {
  const groupRef = useRef<THREE.Group>(null);

  useFrame(({ clock }) => {
    if (groupRef.current) {
      groupRef.current.rotation.x = clock.getElapsedTime() * 0.1;
      groupRef.current.rotation.z = clock.getElapsedTime() * 0.05;
    }
  });

  const rings = [
    { radius: 3, color: '#8b5cf6', opacity: 0.3 },
    { radius: 4, color: '#d4af37', opacity: 0.2 },
    { radius: 5, color: '#06b6d4', opacity: 0.15 },
  ];

  return (
    <group ref={groupRef}>
      {rings.map((ring, i) => (
        <mesh key={i} rotation={[Math.PI / 2, 0, i * 0.5]}>
          <torusGeometry args={[ring.radius, 0.02, 16, 100]} />
          <meshBasicMaterial
            color={ring.color}
            transparent
            opacity={ring.opacity}
          />
        </mesh>
      ))}
    </group>
  );
}

// ==================== MAIN SCENE ====================

function Scene3D() {
  return (
    <>
      {/* Soft ambient lighting */}
      <ambientLight intensity={0.4} />
      <pointLight position={[5, 5, 5]} intensity={0.8} color="#d4af37" />
      <pointLight position={[-5, -3, -5]} intensity={0.5} color="#8b5cf6" />
      <pointLight position={[0, -5, 3]} intensity={0.4} color="#06b6d4" />

      {/* Main organic blob */}
      <OrganicBlob />

      {/* Flowing particles around */}
      <FlowingParticles />

      {/* Subtle glow rings */}
      <GlowRings />

      {/* Gentle auto-rotation */}
      <OrbitControls
        enableZoom={false}
        enablePan={false}
        autoRotate
        autoRotateSpeed={0.3}
        enableRotate={false}
      />
    </>
  );
}

// ==================== MAIN COMPONENT ====================

export default function AMMkAnimation3D() {
  return (
    <div style={{
      position: 'absolute',
      top: 0,
      left: 0,
      width: '100%',
      height: '100%',
      zIndex: 1,
      pointerEvents: 'none'
    }}>
      <Canvas
        camera={{ position: [0, 0, 10], fov: 50 }}
        gl={{ antialias: true, alpha: true }}
        dpr={[1, 2]}
        style={{ background: 'transparent' }}
      >
        <Scene3D />
      </Canvas>
    </div>
  );
}
