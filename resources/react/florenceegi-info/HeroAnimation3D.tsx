import React, { useRef, useMemo, useEffect, useContext } from 'react';
import { Canvas, useFrame, useThree, RootState } from '@react-three/fiber';
import { OrbitControls, Environment, MeshTransmissionMaterial, useTexture } from '@react-three/drei';
import * as THREE from 'three';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import AnimationContext, { useAnimation } from './AnimationContext';
// Audio: usa sistema condiviso (opzionale)
import { useAudioOptional } from '../shared/audio';

gsap.registerPlugin(ScrollTrigger);

// Custom hook that respects animation pause state
function usePausableFrame(callback: (state: RootState, delta: number) => void) {
  const { isPaused } = useContext(AnimationContext);
  const lastTimeRef = useRef(0);

  useFrame((state, delta) => {
    if (isPaused) {
      // Store the last time when paused to avoid time jumps
      lastTimeRef.current = state.clock.elapsedTime;
      return;
    }
    callback(state, delta);
  });
}

/**
 * HERO ANIMATION - FLORENCEEGI
 *
 * 4-Phase Visual Journey:
 * Phase 1: MATERIALIZZAZIONE - Physical work enters the system (particle aggregation)
 * Phase 2: SCANSIONE BLOCKCHAIN - Hexagonal laser sweep, hash visualization
 * Phase 3: CERTIFICAZIONE - CoA "prints" in 3D space, metaball fusion
 * Phase 4: ATTIVAZIONE EPP - L-System tree growth, EPP token orbit
 *
 * Tech Stack:
 * - Three.js + React Three Fiber (R3F)
 * - Custom GLSL shaders (vertex displacement, raymarching SDF)
 * - Cannon.js physics simulation
 * - GSAP ScrollTrigger orchestration
 * - WebGPU-ready (fallback WebGL2)
 */

// ==================== CUSTOM SHADERS ====================

const vertexShader = `
  uniform float uTime;
  uniform float uPhase;
  uniform float uDisplacementStrength;

  varying vec2 vUv;
  varying vec3 vNormal;
  varying vec3 vPosition;
  varying float vDisplacement;

  // Perlin noise 3D
  vec3 mod289(vec3 x) { return x - floor(x * (1.0 / 289.0)) * 289.0; }
  vec4 mod289(vec4 x) { return x - floor(x * (1.0 / 289.0)) * 289.0; }
  vec4 permute(vec4 x) { return mod289(((x*34.0)+1.0)*x); }
  vec4 taylorInvSqrt(vec4 r) { return 1.79284291400159 - 0.85373472095314 * r; }

  float snoise(vec3 v) {
    const vec2 C = vec2(1.0/6.0, 1.0/3.0);
    const vec4 D = vec4(0.0, 0.5, 1.0, 2.0);

    vec3 i  = floor(v + dot(v, C.yyy));
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
    p0 *= norm.x;
    p1 *= norm.y;
    p2 *= norm.z;
    p3 *= norm.w;

    vec4 m = max(0.6 - vec4(dot(x0,x0), dot(x1,x1), dot(x2,x2), dot(x3,x3)), 0.0);
    m = m * m;
    return 42.0 * dot(m*m, vec4(dot(p0,x0), dot(p1,x1), dot(p2,x2), dot(p3,x3)));
  }

  void main() {
    vUv = uv;
    vNormal = normalize(normalMatrix * normal);

    vec3 pos = position;

    // Phase 1: MATERIALIZZAZIONE - particle aggregation with turbulent noise
    if (uPhase >= 0.0 && uPhase < 1.0) {
      float noise = snoise(pos * 2.0 + uTime * 0.5);
      float aggregation = smoothstep(0.0, 1.0, uPhase);
      pos += normal * noise * uDisplacementStrength * (1.0 - aggregation);
    }

    // Phase 2: SCANSIONE BLOCKCHAIN - hexagonal laser scan with sharp displacement
    if (uPhase >= 1.0 && uPhase < 2.0) {
      float scanProgress = (uPhase - 1.0);
      float scanY = scanProgress * 4.0 - 2.0; // Scans from -2 to +2

      // Create hexagonal pattern
      float hexX = abs(sin(pos.x * 15.0 + uTime));
      float hexZ = abs(sin(pos.z * 15.0 + uTime * 1.3));
      float hexPattern = hexX * hexZ;

      // Laser sweep with sharp edge
      float distToScan = abs(pos.y - scanY);
      float scanIntensity = smoothstep(0.3, 0.0, distToScan);

      // Displace vertices in scan zone
      pos += normal * hexPattern * scanIntensity * 0.3;

      // Add pulsing scan line
      if (distToScan < 0.05) {
        pos += normal * sin(uTime * 10.0) * 0.1;
      }
    }

    // Phase 3: CERTIFICAZIONE - Geometric faceting & QR code pattern
    if (uPhase >= 2.0 && uPhase < 3.0) {
      float certPhase = uPhase - 2.0;

      // Hard-edge faceting (geometric crystallization)
      vec3 facetedPos = floor(pos * 8.0) / 8.0;
      pos = mix(pos, facetedPos, certPhase * 0.6);

      // QR code-like pattern on surface
      float qrX = step(0.5, fract(pos.x * 20.0));
      float qrZ = step(0.5, fract(pos.z * 20.0));
      float qrPattern = qrX * qrZ;

      pos += normal * qrPattern * 0.04 * certPhase;

      // Holographic displacement waves
      float wave = sin(pos.x * 10.0 + uTime * 2.0) * cos(pos.z * 10.0 + uTime * 2.0);
      pos += normal * wave * 0.02 * certPhase;
    }

    // Phase 4: ATTIVAZIONE EPP - Organic flowing growth like tree bark
    if (uPhase >= 3.0 && uPhase < 4.0) {
      float growthPhase = uPhase - 3.0;

      // Vertical wood grain pattern
      float grain = snoise(vec3(pos.x * 5.0, pos.y * 15.0, pos.z * 5.0));

      // Organic flowing displacement (like tree growth rings)
      float rings = sin(length(pos.xz) * 8.0 - uTime);

      // Upward stretching motion
      pos.y += growthPhase * grain * 0.15;

      // Bark-like texture
      pos += normal * (grain * 0.1 + rings * 0.05) * growthPhase;

      // Pulsating life
      float pulse = sin(uTime * 2.0) * 0.5 + 0.5;
      pos += normal * pulse * 0.03 * growthPhase;
    }

    // Phase 5: AMMk MARKETPLACE - Fluid organic blob morphing (exact from AMMkAnimation3D)
    if (uPhase >= 4.0 && uPhase <= 5.0) {
      float ammkPhase = uPhase - 4.0;

      // Multi-layered organic noise displacement (exact from AMMkAnimation3D blobVertexShader)
      float noise1 = snoise(pos * 1.5 + uTime * 0.3);
      float noise2 = snoise(pos * 3.0 + uTime * 0.5) * 0.5;
      float noise3 = snoise(pos * 6.0 + uTime * 0.7) * 0.25;

      float totalNoise = (noise1 + noise2 + noise3) * 0.4; // uNoiseStrength = 0.4

      // Smooth morphing from tree to blob
      float blobDisplacement = totalNoise * ammkPhase;

      // Gentle rotation motion (from OrganicBlob useFrame)
      float rotY = uTime * 0.1;
      float rotX = sin(uTime * 0.15) * 0.2;

      pos += normal * blobDisplacement;
    }

    // Phase 6: TECHNOLOGY - Digital circuit/grid transformation
    if (uPhase >= 5.0 && uPhase <= 6.0) {
      float techPhase = uPhase - 5.0;

      // Circuit-like displacement - geometric, sharp edges
      float gridX = sin(pos.x * 10.0 + uTime * 2.0);
      float gridY = sin(pos.y * 10.0 + uTime * 1.5);
      float gridZ = sin(pos.z * 10.0 + uTime * 1.8);

      // Digital pulse effect
      float pulse = sin(uTime * 4.0) * 0.5 + 0.5;
      float digitalNoise = step(0.8, snoise(pos * 5.0 + uTime)) * pulse;

      // Sharp geometric transformation
      float circuitDisplacement = (gridX * gridY * 0.1 + digitalNoise * 0.15) * techPhase;

      // Data flow lines - vertical streams
      float dataStream = sin(pos.y * 20.0 + uTime * 5.0) * cos(pos.x * 8.0) * 0.05;

      pos += normal * (circuitDisplacement + dataStream * techPhase);

      // Slight flattening for tech feel
      pos.z *= 1.0 - techPhase * 0.1;
    }

    vPosition = pos;
    vDisplacement = length(pos - position);

    gl_Position = projectionMatrix * modelViewMatrix * vec4(pos, 1.0);
  }
`;

const fragmentShader = `
  uniform float uTime;
  uniform float uPhase;
  uniform vec3 uColor1;
  uniform vec3 uColor2;
  uniform vec3 uColor3;
  uniform float uMetalness;
  uniform float uRoughness;
  uniform sampler2D uHashTexture;

  varying vec2 vUv;
  varying vec3 vNormal;
  varying vec3 vPosition;
  varying float vDisplacement;

  // Raymarching SDF for blockchain visualization
  float sdHexPrism(vec3 p, vec2 h) {
    const vec3 k = vec3(-0.8660254, 0.5, 0.57735);
    p = abs(p);
    p.xy -= 2.0 * min(dot(k.xy, p.xy), 0.0) * k.xy;
    vec2 d = vec2(
      length(p.xy - vec2(clamp(p.x, -k.z*h.x, k.z*h.x), h.x)) * sign(p.y - h.x),
      p.z - h.y
    );
    return min(max(d.x, d.y), 0.0) + length(max(d, 0.0));
  }

  float raymarchHash(vec3 ro, vec3 rd) {
    float t = 0.0;
    for(int i = 0; i < 32; i++) {
      vec3 p = ro + rd * t;
      float d = sdHexPrism(p, vec2(0.1, 0.05));
      if(d < 0.001) return t;
      t += d;
      if(t > 5.0) break;
    }
    return -1.0;
  }

  void main() {
    vec3 color = uColor1;

    // Phase 1: MATERIALIZZAZIONE - particle glow
    if (uPhase >= 0.0 && uPhase < 1.0) {
      float glow = vDisplacement * 2.0;
      color = mix(uColor1, uColor2, glow);
      color += vec3(0.5, 0.3, 0.1) * glow * (1.0 - uPhase);
    }

    // Phase 2: SCANSIONE BLOCKCHAIN - NEON CYAN laser scan with digital hash grid
    if (uPhase >= 1.0 && uPhase < 2.0) {
      float scanProgress = uPhase - 1.0;
      float scanY = scanProgress * 4.0 - 2.0;
      float distToScan = abs(vPosition.y - scanY);

      // Base color: deep blue/black
      color = vec3(0.0, 0.05, 0.15);

      // BRIGHT CYAN laser scan line
      if (distToScan < 0.05) {
        float intensity = 1.0 - (distToScan / 0.05);
        color = mix(color, vec3(0.0, 1.0, 0.9), intensity);
        color += vec3(0.0, 2.0, 1.5) * intensity; // NEON glow
      }

      // Hash grid pattern (scanned areas)
      if (vPosition.y < scanY) {
        vec3 hashColor = texture2D(uHashTexture, vUv * 3.0 + vec2(uTime * 0.05, 0.0)).rgb;
        float gridPattern = step(0.9, hashColor.r);
        color += vec3(0.0, 0.4, 0.4) * gridPattern;
      }

      // Hexagonal cells in scanned zone
      float hexX = abs(sin(vPosition.x * 15.0));
      float hexZ = abs(sin(vPosition.z * 15.0));
      float hexOutline = smoothstep(0.95, 1.0, hexX) + smoothstep(0.95, 1.0, hexZ);
      if (vPosition.y < scanY) {
        color += vec3(0.0, 0.6, 0.6) * hexOutline;
      }
    }

    // Phase 3: CERTIFICAZIONE - Holographic rainbow shimmer + QR code
    if (uPhase >= 2.0 && uPhase < 3.0) {
      float certPhase = uPhase - 2.0;

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

      color = mix(vec3(0.1, 0.15, 0.3), rainbow, fresnel * certPhase);

      // QR code pattern overlay
      float qrX = step(0.5, fract(vPosition.x * 20.0));
      float qrZ = step(0.5, fract(vPosition.z * 20.0));
      float qrPattern = qrX * qrZ;
      color += vec3(1.0) * qrPattern * 0.3 * certPhase;

      // Metallic sheen
      color += fresnel * vec3(0.5, 0.7, 1.0) * certPhase;
    }

    // Phase 4: ATTIVAZIONE EPP - Organic GREEN with visible growth rings
    if (uPhase >= 3.0 && uPhase < 4.0) {
      float eppPhase = uPhase - 3.0;

      // Wood grain / growth rings
      float rings = abs(sin(length(vPosition.xz) * 8.0 - uTime * 0.5));
      rings = smoothstep(0.4, 0.6, rings);

      // Base organic green
      vec3 darkGreen = vec3(0.1, 0.3, 0.15);
      vec3 lightGreen = vec3(0.3, 0.8, 0.4);
      color = mix(darkGreen, lightGreen, rings);

      // Vertical grain lines
      float grain = abs(sin(vPosition.y * 25.0));
      color *= 0.8 + grain * 0.4;

      // Pulsating life energy
      float pulse = sin(uTime * 2.0) * 0.5 + 0.5;
      color += vec3(0.2, 0.6, 0.3) * pulse * eppPhase * 0.3;

      // Glowing veins
      float veins = smoothstep(0.95, 1.0, abs(sin(vPosition.x * 20.0)) * abs(sin(vPosition.z * 20.0)));
      color += vec3(0.4, 1.0, 0.5) * veins * eppPhase;
    }

    // Phase 5: AMMk MARKETPLACE - Purple/Gold fluid gradient (from AMMkAnimation3D)
    if (uPhase >= 4.0 && uPhase <= 5.0) {
      float ammkPhase = uPhase - 4.0;

      // Fresnel for soft glow edges (exact from AMMkAnimation3D)
      vec3 viewDir = normalize(cameraPosition - vPosition);
      float fresnel = pow(1.0 - abs(dot(viewDir, vNormal)), 2.5);

      // Animated gradient purple -> gold (exact colors from AMMkAnimation3D)
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

      // Transition from green to purple/gold
      vec3 prevColor = mix(vec3(0.1, 0.3, 0.15), vec3(0.3, 0.8, 0.4), 0.5);
      color = mix(prevColor, finalColor, ammkPhase);
    }

    // Phase 6: TECHNOLOGY - Blue/cyan digital circuit aesthetic
    if (uPhase >= 5.0 && uPhase <= 6.0) {
      float techPhase = uPhase - 5.0;

      // Fresnel for holographic edge glow
      vec3 viewDir = normalize(cameraPosition - vPosition);
      float fresnel = pow(1.0 - abs(dot(viewDir, vNormal)), 3.0);

      // Tech color palette - blue, cyan, electric
      vec3 techBlue = vec3(0.0, 0.4, 0.9);
      vec3 techCyan = vec3(0.0, 0.9, 0.8);
      vec3 techWhite = vec3(0.9, 0.95, 1.0);

      // Circuit grid pattern
      float gridX = step(0.95, abs(sin(vPosition.x * 15.0)));
      float gridY = step(0.95, abs(sin(vPosition.y * 15.0)));
      float gridZ = step(0.95, abs(sin(vPosition.z * 15.0)));
      float circuitLines = max(max(gridX, gridY), gridZ);

      // Data pulse animation
      float dataPulse = sin(vPosition.y * 30.0 + uTime * 8.0) * 0.5 + 0.5;
      dataPulse = step(0.7, dataPulse);

      // Base color gradient
      float gradientShift = sin(uTime * 0.3 + vPosition.y * 1.5) * 0.5 + 0.5;
      vec3 baseColor = mix(techBlue, techCyan, gradientShift);

      // Add circuit lines in bright cyan
      baseColor = mix(baseColor, techWhite, circuitLines * 0.8);

      // Add data streams as bright pulses
      baseColor += techCyan * dataPulse * 0.3;

      // Holographic edge glow
      baseColor += techWhite * fresnel * 0.6;

      // Scanline effect
      float scanline = step(0.98, sin(vPosition.y * 100.0 + uTime * 2.0));
      baseColor += techCyan * scanline * 0.2;

      // Transition from purple/gold to tech blue
      vec3 prevColor = mix(vec3(0.4, 0.1, 0.6), vec3(0.85, 0.65, 0.2), 0.5);
      color = mix(prevColor, baseColor, techPhase);
    }

    gl_FragColor = vec4(color, 1.0);
  }
`;

// ==================== PARTICLE SYSTEM ====================

interface ParticleSystemProps {
  count: number;
  phase: number;
}

function ParticleSystem({ count, phase }: ParticleSystemProps) {
  const meshRef = useRef<THREE.InstancedMesh>(null!);
  const dummy = useMemo(() => new THREE.Object3D(), []);

  const particles = useMemo(() => {
    const temp = [];
    for (let i = 0; i < count; i++) {
      temp.push({
        position: new THREE.Vector3(
          (Math.random() - 0.5) * 10,
          (Math.random() - 0.5) * 10,
          (Math.random() - 0.5) * 10
        ),
        velocity: new THREE.Vector3(
          (Math.random() - 0.5) * 0.02,
          (Math.random() - 0.5) * 0.02,
          (Math.random() - 0.5) * 0.02
        ),
        targetPosition: new THREE.Vector3(
          (Math.random() - 0.5) * 2,
          (Math.random() - 0.5) * 2,
          (Math.random() - 0.5) * 2
        ),
        scale: Math.random() * 0.5 + 0.5,
      });
    }
    return temp;
  }, [count]);

  usePausableFrame((state, delta) => {
    particles.forEach((particle, i) => {
      // Phase 1: MATERIALIZZAZIONE - particles aggregate toward center
      if (phase < 1.0) {
        particle.position.lerp(particle.targetPosition, delta * phase);
      }

      // Phase 2: SCANSIONE BLOCKCHAIN - Hexagonal grid orbit
      if (phase >= 1.0 && phase < 2.0) {
        const scanPhase = phase - 1.0;
        const hexAngle = ((i % 6) / 6) * Math.PI * 2;
        const hexRadius = 2.5;
        const layer = Math.floor(i / 6);

        particle.position.x = Math.cos(hexAngle + state.clock.elapsedTime * 0.3) * hexRadius;
        particle.position.z = Math.sin(hexAngle + state.clock.elapsedTime * 0.3) * hexRadius;
        particle.position.y = -2 + (layer % 10) * 0.4 + scanPhase * 4;
      }

      // Phase 3: CERTIFICAZIONE - Structured grid (QR code style)
      if (phase >= 2.0 && phase < 3.0) {
        const certPhase = phase - 2.0;
        const gridSize = 10;
        const gridX = (i % gridSize) - gridSize / 2;
        const gridZ = Math.floor(i / gridSize) % gridSize - gridSize / 2;

        particle.position.x = gridX * 0.4;
        particle.position.z = gridZ * 0.4;
        particle.position.y = Math.sin(state.clock.elapsedTime * 2 + gridX * 0.5) * 0.3;

        // Some particles blink on/off (QR code effect)
        particle.scale = ((i + Math.floor(state.clock.elapsedTime * 3)) % 3) === 0 ? 0 : 1;
      }

      // Phase 4: ATTIVAZIONE EPP - Organic spiral upward (like tree growth)
      if (phase >= 3.0 && phase < 4.0) {
        const eppPhase = phase - 3.0;
        const spiralAngle = (i / count) * Math.PI * 8 + state.clock.elapsedTime * 0.5;
        const spiralRadius = 1.5 + Math.sin(i * 0.1) * 0.5;

        particle.position.x = Math.cos(spiralAngle) * spiralRadius * (1 - eppPhase * 0.3);
        particle.position.z = Math.sin(spiralAngle) * spiralRadius * (1 - eppPhase * 0.3);
        particle.position.y = (i / count) * 4 - 2 + Math.sin(state.clock.elapsedTime + i * 0.1) * 0.2;
      }

      // Phase 5: AMMk MARKETPLACE - Orbital flow around blob
      if (phase >= 4.0) {
        const ammkPhase = phase - 4.0;
        const orbitAngle = (i / count) * Math.PI * 2 + state.clock.elapsedTime * 0.3;
        const orbitRadius = 3 + Math.sin(i * 0.05 + state.clock.elapsedTime) * 1;
        const verticalOffset = Math.sin(orbitAngle * 3 + state.clock.elapsedTime) * 1.5;

        particle.position.x = Math.cos(orbitAngle) * orbitRadius;
        particle.position.z = Math.sin(orbitAngle) * orbitRadius;
        particle.position.y = verticalOffset;

        // Gentle pulsing scale
        particle.scale = 0.5 + Math.sin(state.clock.elapsedTime * 2 + i * 0.1) * 0.3;
      }

      dummy.position.copy(particle.position);
      dummy.scale.setScalar(particle.scale * 0.05);
      dummy.updateMatrix();

      meshRef.current.setMatrixAt(i, dummy.matrix);
    });

    meshRef.current.instanceMatrix.needsUpdate = true;
  });

  return (
    <instancedMesh ref={meshRef} args={[undefined, undefined, count]}>
      <sphereGeometry args={[1, 16, 16]} />
      <meshStandardMaterial
        color={phase < 1.0 ? "#ffa500" : phase < 2.0 ? "#00ffcc" : phase < 3.0 ? "#0088ff" : phase < 4.0 ? "#22ff44" : "#8b5cf6"}
        emissive={phase < 1.0 ? "#ff6600" : phase < 2.0 ? "#00ccaa" : phase < 3.0 ? "#0066cc" : phase < 4.0 ? "#00cc22" : "#d4af37"}
        emissiveIntensity={2.0}
        toneMapped={false}
      />
    </instancedMesh>
  );
}

// ==================== CENTRAL CORE ====================

interface CoreGeometryProps {
  phase: number;
}

function CoreGeometry({ phase }: CoreGeometryProps) {
  const meshRef = useRef<THREE.Mesh>(null!);
  const materialRef = useRef<THREE.ShaderMaterial>(null!);

  // Generate procedural hash texture
  const hashTexture = useMemo(() => {
    const size = 512;
    const data = new Uint8Array(size * size * 4);

    for (let i = 0; i < size * size; i++) {
      const stride = i * 4;
      // Simulate blockchain hash pattern
      const hash = Math.sin(i * 0.1) * 127 + 128;
      data[stride] = hash;
      data[stride + 1] = Math.sin(i * 0.15) * 127 + 128;
      data[stride + 2] = Math.sin(i * 0.2) * 127 + 128;
      data[stride + 3] = 255;
    }

    const texture = new THREE.DataTexture(data, size, size, THREE.RGBAFormat);
    texture.needsUpdate = true;
    return texture;
  }, []);

  const uniforms = useMemo(
    () => ({
      uTime: { value: 0 },
      uPhase: { value: phase },
      uDisplacementStrength: { value: 0.3 },
      uColor1: { value: new THREE.Color('#ff6b35') },
      uColor2: { value: new THREE.Color('#004e89') },
      uColor3: { value: new THREE.Color('#1a9c39') },
      uMetalness: { value: 0.9 },
      uRoughness: { value: 0.1 },
      uHashTexture: { value: hashTexture },
    }),
    [hashTexture]
  );

  usePausableFrame((state) => {
    if (materialRef.current) {
      materialRef.current.uniforms.uTime.value = state.clock.elapsedTime;
      materialRef.current.uniforms.uPhase.value = phase;
    }

    // Rotate core based on phase
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

// ==================== EPP TREE (L-System) ====================

interface EPPTreeProps {
  phase: number;
}

function EPPTree({ phase }: EPPTreeProps) {
  const groupRef = useRef<THREE.Group>(null!);

  // Simplified L-System for tree growth
  const branches = useMemo(() => {
    const result: Array<{ start: THREE.Vector3; end: THREE.Vector3; thickness: number }> = [];

    const grow = (pos: THREE.Vector3, dir: THREE.Vector3, length: number, thickness: number, depth: number) => {
      if (depth <= 0 || thickness < 0.01) return;

      const end = pos.clone().add(dir.clone().multiplyScalar(length));
      result.push({ start: pos, end, thickness });

      // Branch
      const angle1 = Math.PI / 6;
      const angle2 = -Math.PI / 6;

      const dir1 = dir.clone().applyAxisAngle(new THREE.Vector3(1, 0, 0), angle1);
      const dir2 = dir.clone().applyAxisAngle(new THREE.Vector3(0, 0, 1), angle2);

      grow(end, dir1, length * 0.7, thickness * 0.7, depth - 1);
      grow(end, dir2, length * 0.7, thickness * 0.7, depth - 1);
    };

    grow(
      new THREE.Vector3(0, -2, 0),
      new THREE.Vector3(0, 1, 0),
      0.5,
      0.1,
      4
    );

    return result;
  }, []);

  usePausableFrame((state) => {
    if (groupRef.current && phase >= 3.0) {
      const growthPhase = Math.min((phase - 3.0), 1.0);
      groupRef.current.scale.setScalar(growthPhase);
      groupRef.current.position.y = -2 + growthPhase * 0.5;
    }
  });

  if (phase < 3.0 || phase >= 4.0) return null; // Hide in Phase 5

  return (
    <group ref={groupRef}>
      {branches.map((branch, i) => (
        <mesh key={i} position={branch.start}>
          <cylinderGeometry args={[branch.thickness, branch.thickness * 0.8, branch.start.distanceTo(branch.end), 8]} />
          <meshStandardMaterial color="#4a7c4e" roughness={0.8} />
        </mesh>
      ))}
    </group>
  );
}

// ==================== AMMk FLOWING PARTICLES (exact from AMMkAnimation3D) ====================

interface AMMkFlowingParticlesProps {
  phase: number;
}

function AMMkFlowingParticles({ phase }: AMMkFlowingParticlesProps) {
  const pointsRef = useRef<THREE.Points>(null);
  const count = 800;

  const [positions, colors] = useMemo(() => {
    const positions = new Float32Array(count * 3);
    const colors = new Float32Array(count * 3);

    for (let i = 0; i < count; i++) {
      const theta = Math.random() * Math.PI * 2;
      const phi = Math.acos(Math.random() * 2 - 1);
      const r = 3 + Math.random() * 4;

      positions[i * 3] = r * Math.sin(phi) * Math.cos(theta);
      positions[i * 3 + 1] = r * Math.sin(phi) * Math.sin(theta);
      positions[i * 3 + 2] = r * Math.cos(phi);

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

    return [positions, colors];
  }, []);

  const geometry = useMemo(() => {
    const geo = new THREE.BufferGeometry();
    geo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
    geo.setAttribute('color', new THREE.BufferAttribute(colors, 3));
    return geo;
  }, [positions, colors]);

  usePausableFrame((state) => {
    const clock = state.clock;
    if (pointsRef.current) {
      const time = clock.getElapsedTime();
      const posArray = pointsRef.current.geometry.attributes.position.array as Float32Array;

      for (let i = 0; i < count; i++) {
        const i3 = i * 3;
        const x = posArray[i3];
        const z = posArray[i3 + 2];
        const angle = Math.atan2(z, x);
        const radius = Math.sqrt(x * x + z * z);

        const newAngle = angle + 0.002;
        posArray[i3] = Math.cos(newAngle) * radius;
        posArray[i3 + 2] = Math.sin(newAngle) * radius;
        posArray[i3 + 1] += Math.sin(time + i * 0.1) * 0.005;

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

  // Fade in during phase transition
  const opacity = phase >= 4.0 ? Math.min((phase - 4.0) * 2, 0.6) : 0;

  if (phase < 4.0) return null;

  return (
    <points ref={pointsRef} geometry={geometry}>
      <pointsMaterial
        size={0.08}
        vertexColors
        transparent
        opacity={opacity}
        sizeAttenuation
        blending={THREE.AdditiveBlending}
        depthWrite={false}
      />
    </points>
  );
}

// ==================== AMMk GLOW RINGS (exact from AMMkAnimation3D) ====================

interface AMMkGlowRingsProps {
  phase: number;
}

function AMMkGlowRings({ phase }: AMMkGlowRingsProps) {
  const groupRef = useRef<THREE.Group>(null);

  usePausableFrame((state) => {
    if (groupRef.current) {
      groupRef.current.rotation.x = state.clock.getElapsedTime() * 0.1;
      groupRef.current.rotation.z = state.clock.getElapsedTime() * 0.05;
    }
  });

  const rings = [
    { radius: 3, color: '#8b5cf6', opacity: 0.3 },
    { radius: 4, color: '#d4af37', opacity: 0.2 },
    { radius: 5, color: '#06b6d4', opacity: 0.15 },
  ];

  // Fade in during phase transition
  const fadeIn = phase >= 4.0 ? Math.min((phase - 4.0) * 2, 1) : 0;

  if (phase < 4.0) return null;

  return (
    <group ref={groupRef}>
      {rings.map((ring, i) => (
        <mesh key={i} rotation={[Math.PI / 2, 0, i * 0.5]}>
          <torusGeometry args={[ring.radius, 0.02, 16, 100]} />
          <meshBasicMaterial
            color={ring.color}
            transparent
            opacity={ring.opacity * fadeIn}
          />
        </mesh>
      ))}
    </group>
  );
}

// ==================== MAIN SCENE ====================

interface Scene3DProps {
  phase: number;
}

function Scene3D({ phase }: Scene3DProps) {
  return (
    <>
      <color attach="background" args={['#000814']} />
      <fog attach="fog" args={['#000814', 5, 15]} />

      {/* Lighting - DYNAMIC per phase */}
      <ambientLight intensity={0.1} />

      {/* Phase 1: Warm orange glow */}
      {phase < 1.0 && (
        <>
          <pointLight position={[3, 3, 3]} intensity={2.0} color="#ff6b35" />
          <pointLight position={[-3, -3, -3]} intensity={1.5} color="#ffa500" />
        </>
      )}

      {/* Phase 2: Laser scan - BRIGHT CYAN from above */}
      {phase >= 1.0 && phase < 2.0 && (
        <>
          <spotLight
            position={[0, 5, 0]}
            angle={0.5}
            penumbra={0.2}
            intensity={4.0}
            color="#00ffcc"
            castShadow
          />
          <pointLight position={[0, -3, 0]} intensity={1.0} color="#004d66" />
        </>
      )}

      {/* Phase 3: Holographic multi-color */}
      {phase >= 2.0 && phase < 3.0 && (
        <>
          <pointLight position={[3, 2, 3]} intensity={1.5} color="#ff00ff" />
          <pointLight position={[-3, 2, -3]} intensity={1.5} color="#00ffff" />
          <pointLight position={[0, 3, 0]} intensity={1.5} color="#ffff00" />
        </>
      )}

      {/* Phase 4: Natural green (sun through leaves) */}
      {phase >= 3.0 && phase < 4.0 && (
        <>
          <directionalLight position={[5, 10, 5]} intensity={1.5} color="#a8e6a1" />
          <pointLight position={[0, -2, 0]} intensity={1.0} color="#1a5c2e" />
          <hemisphereLight groundColor="#1a3a1f" color="#90ee90" intensity={0.8} />
        </>
      )}

      {/* Phase 5: AMMk Marketplace - Purple/Gold ambient */}
      {phase >= 4.0 && phase < 5.0 && (
        <>
          <pointLight position={[3, 3, 3]} intensity={2.0} color="#8b5cf6" />
          <pointLight position={[-3, -2, -3]} intensity={1.5} color="#d4af37" />
          <pointLight position={[0, -3, 2]} intensity={1.0} color="#06b6d4" />
          <hemisphereLight groundColor="#1a0a2e" color="#d4af37" intensity={0.6} />
        </>
      )}

      {/* Phase 6: Technology - Blue/Cyan digital */}
      {phase >= 5.0 && (
        <>
          <pointLight position={[3, 3, 3]} intensity={2.5} color="#0096ff" />
          <pointLight position={[-3, 2, -3]} intensity={2.0} color="#00ffc8" />
          <pointLight position={[0, -2, 3]} intensity={1.5} color="#00d4ff" />
          <spotLight position={[0, 5, 0]} angle={0.6} penumbra={0.3} intensity={1.5} color="#ffffff" />
          <hemisphereLight groundColor="#0a1628" color="#00ffc8" intensity={0.7} />
        </>
      )}

      {/* Core Geometry */}
      <CoreGeometry phase={phase} />

      {/* Particle System */}
      <ParticleSystem count={1000} phase={phase} />

      {/* EPP Tree */}
      <EPPTree phase={phase} />

      {/* AMMk Flowing Particles (Phase 5) */}
      <AMMkFlowingParticles phase={phase} />

      {/* AMMk Glow Rings (Phase 5) */}
      <AMMkGlowRings phase={phase} />

      {/* Environment */}
      <Environment preset="city" />

      {/* Controls */}
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

// ==================== MAIN COMPONENT ====================

function HeroAnimation3D() {
  const [phase, setPhase] = React.useState(0);
  const containerRef = useRef<HTMLDivElement>(null!);

  // Audio context è disponibile tramite il sistema condiviso
  // Per ora non usiamo phase-based audio, ma il contesto è disponibile se necessario
  // const audioContext = useAudioOptional();

  useEffect(() => {
    const ctx = gsap.context(() => {
      // ScrollTrigger timeline for 6 phases (including Technology)
      const tl = gsap.timeline({
        scrollTrigger: {
          trigger: containerRef.current,
          start: 'top top',
          end: '+=600%',
          scrub: 1,
          pin: true,
        },
      });

      tl.to(
        {},
        {
          duration: 1,
          onUpdate: function () {
            setPhase(this.progress() * 6);
          },
        }
      );
    }, containerRef);

    return () => ctx.revert();
  }, []);

  // Phase labels for UI
  const phaseLabels = [
    'MATERIALIZZAZIONE',
    'SCANSIONE BLOCKCHAIN',
    'CERTIFICAZIONE',
    'ATTIVAZIONE EPP',
    'AMMk MARKETPLACE',
    'TECHNOLOGY',
  ];

  const currentPhaseIndex = Math.floor(phase);
  const currentLabel = phaseLabels[Math.min(currentPhaseIndex, 5)];

  // Get animation disabled state
  const { isDisabled } = useAnimation();

  // If animations are disabled, show a static gradient background instead
  if (isDisabled) {
    return (
      <div
        ref={containerRef}
        style={{
          position: "absolute",
          top: 0,
          left: 0,
          width: "100%",
          height: "100%",
          zIndex: 1,
          background: 'linear-gradient(135deg, #0a0a1a 0%, #1a0a2e 30%, #0a1a2e 70%, #0a0a1a 100%)',
        }}
      >
        {/* Static decorative elements when 3D is off */}
        <div style={{
          position: 'absolute',
          top: '50%',
          left: '50%',
          transform: 'translate(-50%, -50%)',
          width: '300px',
          height: '300px',
          borderRadius: '50%',
          background: 'radial-gradient(circle, rgba(212,175,55,0.15) 0%, transparent 70%)',
          filter: 'blur(40px)',
        }} />
        <div style={{
          position: 'absolute',
          top: '30%',
          left: '30%',
          width: '200px',
          height: '200px',
          borderRadius: '50%',
          background: 'radial-gradient(circle, rgba(128,0,255,0.1) 0%, transparent 70%)',
          filter: 'blur(30px)',
        }} />
        <div style={{
          position: 'absolute',
          bottom: '20%',
          right: '25%',
          width: '250px',
          height: '250px',
          borderRadius: '50%',
          background: 'radial-gradient(circle, rgba(0,255,200,0.08) 0%, transparent 70%)',
          filter: 'blur(35px)',
        }} />
      </div>
    );
  }

  return (
    <div ref={containerRef} style={{ position: "absolute", top: 0, left: 0, width: "100%", height: "100%", zIndex: 1 }}>
      <Canvas
        camera={{ position: [0, 0, 5], fov: 45 }}
        gl={{ antialias: true, alpha: false }}
        dpr={[1, 2]}
      >
        <Scene3D phase={phase} />
      </Canvas>


    </div>
  );
}

export default HeroAnimation3D;
