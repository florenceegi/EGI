/**
 * @file engine.js
 * @author Padmin D. Curtis (OS3.0)
 * @project FLORENCE EGI HUB
 * @description Core PBR Rendering Engine. Features Volumetric Magma, Glass Refraction, and Hybrid CSS2D HUD.
 * @version 3.1.0 (Implacable)
 * @license PROPRIETARY - ORACODE SYSTEMS
 */

import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
// CSS2D Removed
import { EffectComposer } from 'three/addons/postprocessing/EffectComposer.js';
import { RenderPass } from 'three/addons/postprocessing/RenderPass.js';
import { UnrealBloomPass } from 'three/addons/postprocessing/UnrealBloomPass.js';
import { RoomEnvironment } from 'three/addons/environments/RoomEnvironment.js';
import TWEEN from '@tweenjs/tween.js';

// --- CONFIGURATION ---
const SYSTEM_CONFIG = {
    camera: { fov: 45, pos: [0, 50, 850] }, // GOD VIEW: See entire system
    bloom: { threshold: 0.9, strength: 0.3, radius: 0.1 }, // REDUCED for readability
    physics: { metalness: 0.2, roughness: 0.1, transmission: 1.0, thickness: 1.5 },
    scene: { yOffset: 15 }
};



// --- DATA IMPORT ---
// Assuming ecosystem_data.js loaded before this and exposed via window
const data = window.ecosystemData;
const orbitConfig = window.orbitalConfig;

if (!data || !orbitConfig) {
    console.error("CRITICAL: Ecosystem Data not loaded!");
}

// --- ENGINE CORE ---
const scene = new THREE.Scene();
const isMobile = window.innerWidth < 768;
// FIX: Drastically pull back camera to fit large spheres in portrait
const camZ = isMobile ? 650 : SYSTEM_CONFIG.camera.pos[2]; 

const camera = new THREE.PerspectiveCamera(SYSTEM_CONFIG.camera.fov, window.innerWidth / window.innerHeight, 0.1, 2000);
camera.position.set(SYSTEM_CONFIG.camera.pos[0], SYSTEM_CONFIG.camera.pos[1], camZ);

// WEBGL RENDERER (PBR)
const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, powerPreference: "high-performance" });
renderer.setSize(window.innerWidth, window.innerHeight);
renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
renderer.toneMapping = THREE.ACESFilmicToneMapping;
renderer.toneMappingExposure = 1.2;
document.body.appendChild(renderer.domElement);



// ENVIRONMENT & LIGHTING (PBR)
const pmremGenerator = new THREE.PMREMGenerator(renderer);
scene.environment = pmremGenerator.fromScene(new RoomEnvironment(), 0.04).texture;
scene.background = new THREE.Color(0x020202); // Deep Space Black

// POST-PROCESSING
const composer = new EffectComposer(renderer);
const renderPass = new RenderPass(scene, camera);
composer.addPass(renderPass);

const bloomPass = new UnrealBloomPass(new THREE.Vector2(window.innerWidth, window.innerHeight), 1.5, 0.4, 0.85);
bloomPass.threshold = SYSTEM_CONFIG.bloom.threshold;
bloomPass.strength = SYSTEM_CONFIG.bloom.strength;
bloomPass.radius = SYSTEM_CONFIG.bloom.radius;
composer.addPass(bloomPass);

// CONTROLS
const controls = new OrbitControls(camera, renderer.domElement); // Control via WebGL canvas
controls.enableDamping = true;
controls.dampingFactor = 0.05;
controls.maxDistance = 600;

// --- VOLUMETRIC SHADERS ---
const magmaVShader = `
  varying vec3 vNormal;
  varying vec3 vPosition;
  varying vec2 vUv;
  void main() {
    vNormal = normalize(normalMatrix * normal);
    vPosition = position;
    vUv = uv;
    gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
  }
`;

const magmaFShader = `
  uniform vec3 uColor;
  uniform float uTime;
  varying vec3 vNormal;
  varying vec3 vPosition;
  
  // Simplex Noise (Optimized)
  vec3 mod289(vec3 x) { return x - floor(x * (1.0 / 289.0)) * 289.0; }
  vec4 mod289(vec4 x) { return x - floor(x * (1.0 / 289.0)) * 289.0; }
  vec4 permute(vec4 x) { return mod289(((x*34.0)+1.0)*x); }
  vec4 taylorInvSqrt(vec4 r) { return 1.79284291400159 - 0.85373472095314 * r; }
  float snoise(vec3 v) { 
    const vec2  C = vec2(1.0/6.0, 1.0/3.0) ;
    const vec4  D = vec4(0.0, 0.5, 1.0, 2.0);
    vec3 i  = floor(v + dot(v, C.yyy) );
    vec3 x0 = v - i + dot(i, C.xxx) ;
    vec3 g = step(x0.yzx, x0.xyz);
    vec3 l = 1.0 - g;
    vec3 i1 = min( g.xyz, l.zxy );
    vec3 i2 = max( g.xyz, l.zxy );
    vec3 x1 = x0 - i1 + C.xxx;
    vec3 x2 = x0 - i2 + C.yyy;
    vec3 x3 = x0 - D.yyy;
    i = mod289(i); 
    vec4 p = permute( permute( permute( 
               i.z + vec4(0.0, i1.z, i2.z, 1.0 ))
             + i.y + vec4(0.0, i1.y, i2.y, 1.0 )) 
             + i.x + vec4(0.0, i1.x, i2.x, 1.0 ));
    float n_ = 0.142857142857;
    vec3  ns = n_ * D.wyz - D.xzx;
    vec4 j = p - 49.0 * floor(p * ns.z * ns.z);
    vec4 x_ = floor(j * ns.z);
    vec4 y_ = floor(j - 7.0 * x_ );
    vec4 x = x_ *ns.x + ns.yyyy;
    vec4 y = y_ *ns.x + ns.yyyy;
    vec4 h = 1.0 - abs(x) - abs(y);
    vec4 b0 = vec4( x.xy, y.xy );
    vec4 b1 = vec4( x.zw, y.zw );
    vec4 s0 = floor(b0)*2.0 + 1.0;
    vec4 s1 = floor(b1)*2.0 + 1.0;
    vec4 sh = -step(h, vec4(0.0));
    vec4 a0 = b0.xzyw + s0.xzyw*sh.xxyy ;
    vec4 a1 = b1.xzyw + s1.xzyw*sh.zzww ;
    vec3 p0 = vec3(a0.xy,h.x);
    vec3 p1 = vec3(a0.zw,h.y);
    vec3 p2 = vec3(a1.xy,h.z);
    vec3 p3 = vec3(a1.zw,h.w);
    vec4 norm = taylorInvSqrt(vec4(dot(p0,p0), dot(p1,p1), dot(p2, p2), dot(p3,p3)));
    p0 *= norm.x; p1 *= norm.y; p2 *= norm.z; p3 *= norm.w;
    vec4 m = max(0.6 - vec4(dot(x0,x0), dot(x1,x1), dot(x2,x2), dot(x3,x3)), 0.0);
    m = m * m;
    return 42.0 * dot( m*m, vec4( dot(p0,x0), dot(p1,x1), dot(p2,x2), dot(p3,x3) ) );
  }

  void main() {
    float noise = snoise(vPosition * 0.15 + uTime * 0.5);
    float noise2 = snoise(vPosition * 0.5 - uTime * 0.2);
    float intensity = (noise * 0.5 + 0.5) + (noise2 * 0.2);
    
    // Fresnel
    vec3 viewDir = vec3(0.0,0.0,1.0);
    float fresnel = pow(1.0 - dot(normalize(vNormal), viewDir), 2.0);
    
    vec3 coreColor = uColor * (0.3 + intensity * 0.2); // DIMMED for label readability
    gl_FragColor = vec4(coreColor + (vec3(0.3)*fresnel), 1.0); // Reduced fresnel
  }
`;

console.log("ENGINE: Shaders compiled. Starting build...");

// --- COMPLEX NODE BUILDER ---
const commonUniforms = { uTime: { value: 0 } };

// HELPER: Create Glass Material (NO TRANSMISSION - allows internal visibility)
function createGlassMaterial(colorHex) {
    return new THREE.MeshPhysicalMaterial({
        color: colorHex,
        metalness: 0.1,
        roughness: 0.05,
        transparent: true,
        opacity: 0.35, // Semi-transparent glass
        envMapIntensity: 2.0,
        clearcoat: 1.0,
        clearcoatRoughness: 0.1,
        side: THREE.FrontSide
        // NOTE: transmission DISABLED to allow internal geometry visibility
    });
}


// HELPER: Create Text Texture (Canvas) - For sphere mapping
function createTextTexture(text, subtext) {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    canvas.width = 2048; // Higher res for sphere
    canvas.height = 1024;
    
    // Fill with solid black for contrast
    ctx.fillStyle = '#000000';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    
    // Title - centered
    ctx.font = 'bold 180px "Rajdhani"'; 
    ctx.fillStyle = '#ffffff';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.shadowColor = "rgba(0, 0, 0, 1.0)";
    ctx.shadowBlur = 8;
    ctx.shadowOffsetX = 3;
    ctx.shadowOffsetY = 3;
    
    ctx.fillText(text.toUpperCase(), canvas.width / 2, canvas.height / 2);
    
    // Subtext
    if(subtext) {
        ctx.font = '100px "Share Tech Mono"';
        ctx.fillStyle = 'rgba(255, 255, 255, 0.9)';
        ctx.shadowBlur = 0;
        ctx.fillText(subtext.substring(0, 30).toUpperCase(), canvas.width / 2, canvas.height / 2 + 140);
    }

    const tex = new THREE.CanvasTexture(canvas);
    tex.anisotropy = 16;
    return tex;
}

function createComplexNode(id, data, radius) {
    const root = new THREE.Group();
    
    // 1. MAGMA CORE (Volumetric)
    const coreGeo = new THREE.IcosahedronGeometry(radius * 0.5, 5); 
    const coreMat = new THREE.ShaderMaterial({
        uniforms: {
            uColor: { value: new THREE.Color(data.color) },
            uTime: commonUniforms.uTime
        },
        vertexShader: magmaVShader,
        fragmentShader: magmaFShader
    });
    const coreMesh = new THREE.Mesh(coreGeo, coreMat);
    coreMesh.renderOrder = 0; // Render FIRST
    root.add(coreMesh);

    // 2. INTERNAL LABEL (Sphere with text - billboard rotated)
    const textTex = createTextTexture(data.label, data.tagline);
    const labelGeo = new THREE.SphereGeometry(radius * 0.85, 32, 16);
    const labelMat = new THREE.MeshBasicMaterial({ 
        map: textTex, 
        transparent: false,
        side: THREE.FrontSide,
        depthWrite: true
    });
    const labelMesh = new THREE.Mesh(labelGeo, labelMat);
    labelMesh.renderOrder = 1;
    root.add(labelMesh);

    // 3. GLASS SHELL (Physical Transmission)
    const glassGeo = new THREE.IcosahedronGeometry(radius, 4);
    const glassMat = createGlassMaterial(data.color);
    const glassMesh = new THREE.Mesh(glassGeo, glassMat);
    glassMesh.renderOrder = 2; // Render LAST (To refract everything inside)
    root.add(glassMesh);

    // 4. GYROSCOPIC RINGS
    const ringMat = new THREE.MeshStandardMaterial({ 
        color: 0xffffff, 
        metalness: 0.8, 
        roughness: 0.2,
        transparent: true,
        opacity: 0.6
    });
    
    const r1 = new THREE.Mesh(new THREE.TorusGeometry(radius * 1.3, 0.15, 16, 100), ringMat);
    r1.rotation.x = Math.PI / 1.7;
    root.add(r1);

    const r2 = new THREE.Mesh(new THREE.TorusGeometry(radius * 1.5, 0.15, 16, 100), ringMat);
    r2.rotation.y = Math.PI / 0.5;
    root.add(r2);

    // 5. HIT SPHERE
    const hitMesh = new THREE.Mesh(
        new THREE.SphereGeometry(radius * 2, 16, 16),
        new THREE.MeshBasicMaterial({ visible: false })
    );
    hitMesh.userData = { id: id };
    root.add(hitMesh);

    return { root, coreMesh, r1, r2, hitMesh, labelMesh };
}

// --- SCENE GENERATION ---
const systemGroup = new THREE.Group();
systemGroup.position.y = SYSTEM_CONFIG.scene.yOffset;
scene.add(systemGroup);

const nodes = {};

// 1. CORE SYSTEM
// SCALE UP: 24 -> 50 (Compensate for Camera Z)
const coreNode = createComplexNode('core', data.core, 50);
systemGroup.add(coreNode.root);
nodes['core'] = coreNode;

// 2. SATELLITES & CABLES
const cableMat = new THREE.MeshBasicMaterial({ color: 0xffffff, transparent: true, opacity: 0.05, wireframe: true });

orbitConfig.forEach((cfg, idx) => {
    const d = data[cfg.id];
    if(!d) return;

    // Distribute planets in 2D circle
    // FIXED RADIUS: All satellites equidistant from HUB
    const orbitR = 150; // Reduced to fit all on screen
    const angle = (idx / orbitConfig.length) * Math.PI * 2; // Even distribution
    
    // ECLIPTIC FLATTENING: Remove vertical sine wave
    const zDepth = 0; // All on same Z plane (facing camera)

    // 2D CIRCLE LAYOUT: Satellites on X-Y plane (vertical circle facing camera)
    const x = Math.cos(angle) * orbitR;
    const y = Math.sin(angle) * orbitR; // Y varies (vertical circle)

    // SCALE UP SATELLITE: 12 -> 28
    const node = createComplexNode(cfg.id, d, 28);
    node.root.position.set(x, y, zDepth); // X-Y circle, Z=0
    node.root.lookAt(0,0,0); // Orient rings to center
    
    systemGroup.add(node.root);
    nodes[cfg.id] = node;

    // FIBER OPTIC CABLE (Tube)
    const curve = new THREE.CatmullRomCurve3([
        new THREE.Vector3(0,0,0),
        new THREE.Vector3(x*0.5, y*0.5, zDepth + 20), // Small arc in Z
        new THREE.Vector3(x, y, zDepth)
    ]);
    const tubeGeo = new THREE.TubeGeometry(curve, 20, 0.3, 8, false); // Thinner
    const tube = new THREE.Mesh(tubeGeo, new THREE.MeshBasicMaterial({ 
        color: d.color, 
        transparent: true, 
        opacity: 0.05, // Dimmer
        blending: THREE.AdditiveBlending 
    }));
    systemGroup.add(tube);
});

// --- ATMOSPHERE (STARS & DUST) ---
const starsGeo = new THREE.BufferGeometry();
const starsCount = 3000; // Reduced from 6000
const starPos = new Float32Array(starsCount*3);
for(let i=0;i<starsCount*3;i++) {
    starPos[i] = (Math.random()-0.5)*1200; // Wider field
}
starsGeo.setAttribute('position', new THREE.BufferAttribute(starPos, 3));
const stars = new THREE.Points(starsGeo, new THREE.PointsMaterial({color: 0x888888, size: 0.7, transparent: true, opacity: 0.8}));
scene.add(stars);

// Floating Dust (Close range)
const dustGeo = new THREE.BufferGeometry();
const dustCount = 2000;
const dustPos = new Float32Array(dustCount*3);
for(let i=0;i<dustCount*3;i++) {
    dustPos[i] = (Math.random()-0.5)*400; // Closer field
}
dustGeo.setAttribute('position', new THREE.BufferAttribute(dustPos, 3));
const dustMat = new THREE.PointsMaterial({
    color: 0x00ffdd, // Cyan tint
    size: 0.5,
    transparent: true,
    opacity: 0.3,
    blending: THREE.AdditiveBlending
});
const dust = new THREE.Points(dustGeo, dustMat);
scene.add(dust);



// --- ANIMATION ---
const clock = new THREE.Clock();

function animate() {
    requestAnimationFrame(animate);

    const delta = clock.getDelta();
    const time = clock.getElapsedTime();

    controls.update();
    TWEEN.update();

    // UPDATE UNIFORMS
    commonUniforms.uTime.value = time;

    // ROTATE SYSTEM (DISABLED for 2D layout)
    // systemGroup.rotation.y = time * 0.05;

    // ANIMATE NODES
    Object.values(nodes).forEach(n => {
        // Magma Core Pulse
        if(n.coreMesh) {
            n.coreMesh.rotation.y -= 0.005;
            n.coreMesh.rotation.x += 0.002;
        }

        // Gyro Rings
        if(n.r1) {
            n.r1.rotation.z += 0.01;
            n.r1.rotation.x = Math.sin(time * 0.5) * 0.2;
        }
        if(n.r2) {
            n.r2.rotation.y -= 0.015;
            n.r2.rotation.z = Math.cos(time * 0.3) * 0.2;
        }

        // Billboard Labels: Make label sphere face camera
        if(n.labelMesh) {
            // Get the world position of this node
            const nodeWorldPos = new THREE.Vector3();
            n.root.getWorldPosition(nodeWorldPos);
            
            // Get camera world position
            const camWorldPos = camera.position.clone();
            
            // Calculate direction from node to camera (world space)
            const dirToCamera = new THREE.Vector3().subVectors(camWorldPos, nodeWorldPos).normalize();
            
            // Create a target point in front of labelMesh (in world space)
            // labelMesh is at origin of root, so its world pos = nodeWorldPos
            const targetWorld = nodeWorldPos.clone().add(dirToCamera);
            
            // Make labelMesh look at this target (world space lookAt)
            n.labelMesh.lookAt(targetWorld);
            
            // Rotate 180° on Y because sphere UV has text on the back face (-Z)
            n.labelMesh.rotation.y += Math.PI;
        }
    });

    // Render WebGL
    composer.render();
}

// --- INTERACTION ---
const raycaster = new THREE.Raycaster();
const mouse = new THREE.Vector2();

function onPointerMove(event) {
    mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
    mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;
}

function onClick(event) {
    // 1. Update Raycaster
    raycaster.setFromCamera(mouse, camera);

    // 2. Intersect Objects (Hit Meshes)
    const hits = raycaster.intersectObjects(scene.children, true);
    
    // Filter for "hitMesh"
    const validHit = hits.find(h => h.object.userData && h.object.userData.id);

    if (validHit) {
        const id = validHit.object.userData.id;
        console.log("CLICKED:", id);
        
        // Trigger UI (if function exists)
        if(window.openDetailPanel) {
            window.openDetailPanel(id);
        } else {
            console.warn("UI function 'openDetailPanel' not found!");
        }
    }
}

window.addEventListener('pointermove', onPointerMove);
window.addEventListener('click', onClick);

animate();

// --- RESIZE ---
window.addEventListener('resize', () => {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
    labelRenderer.setSize(window.innerWidth, window.innerHeight);
    composer.setSize(window.innerWidth, window.innerHeight);
});
