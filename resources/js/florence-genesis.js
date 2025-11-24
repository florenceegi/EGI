import * as THREE from 'three';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

/**
 * FlorenceEGI Hero - GENESIS VERSION
 * 
 * This is the absolute original visual logic restored.
 * 1. Raymarching Shader (The "Beautiful" Exploding Sphere).
 * 2. Starfield (The Context).
 * 3. No low-poly meshes. No experiments.
 */

class FlorenceGenesis {
    constructor(canvasContainerId) {
        this.container = document.getElementById(canvasContainerId);
        if (!this.container) return;

        this.width = window.innerWidth;
        this.height = window.innerHeight;

        // Renderer
        this.renderer = new THREE.WebGLRenderer({ antialias: true, alpha: false });
        this.renderer.setSize(this.width, this.height);
        this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        this.container.appendChild(this.renderer.domElement);

        this.clock = new THREE.Clock();
        this.mouse = new THREE.Vector2();
        this.raycaster = new THREE.Raycaster();

        // Scene Setup
        this.scene = new THREE.Scene();
        this.camera = new THREE.OrthographicCamera(-1, 1, 1, -1, 0, 1); // 2D Camera for Shader

        // --- THE CORE: RAYMARCHING SHADER ---
        this.initShaderQuad();

        // --- THE STARS: FOREGROUND ---
        // We render stars in a separate scene or just overlay them?
        // To keep it simple and robust like the first version, let's try to integrate them
        // or just stick to the shader if that's what "The First Version" was.
        // But user asked for "Stars moving".
        // Let's use a second scene for stars.
        
        this.starScene = new THREE.Scene();
        this.starCamera = new THREE.PerspectiveCamera(60, this.width / this.height, 0.1, 1000);
        this.starCamera.position.z = 10;
        
        this.initStars();
        this.initMenu(); // Keep the menu as requested
        
        this.renderer.autoClear = false;

        this.initEvents();
        this.animate();
    }

    initShaderQuad() {
        const geometry = new THREE.PlaneGeometry(2, 2);
        
        const fragmentShader = `
            precision highp float;
            uniform vec2 u_resolution;
            uniform float u_time;
            uniform vec2 u_mouse;

            mat2 rot(float a) {
                float s = sin(a);
                float c = cos(a);
                return mat2(c, -s, s, c);
            }

            float sdIcosahedron(vec3 p, float r) {
                const float q = 2.61803398875;
                p = abs(p);
                return max(dot(p, normalize(vec3(1, q, 0))), max(dot(p, normalize(vec3(0, 1, q))), dot(p, normalize(vec3(q, 0, 1))))) - r;
            }

            float map(vec3 p) {
                vec3 p1 = p;
                p1.xz *= rot(u_time * 0.2);
                p1.xy *= rot(u_time * 0.15);
                float d1 = sdIcosahedron(p1, 1.0 + sin(u_time) * 0.1);
                
                vec3 p2 = p;
                p2 = abs(p2) - 2.5;
                p2.xy *= rot(u_time * 0.1);
                float d2 = length(p2) - 0.2;
                
                float k = 1.2;
                float h = clamp(0.5 + 0.5 * (d2 - d1) / k, 0.0, 1.0);
                return mix(d2, d1, h) - k * h * (1.0 - h);
            }

            void main() {
                vec2 uv = (gl_FragCoord.xy - 0.5 * u_resolution.xy) / u_resolution.y;
                vec3 ro = vec3(0.0, 0.0, -6.0);
                vec3 rd = normalize(vec3(uv, 1.0));
                
                vec2 m = u_mouse / u_resolution.xy;
                ro.xz *= rot((m.x - 0.5) * 0.5);
                rd.xz *= rot((m.x - 0.5) * 0.5);
                
                float t = 0.0;
                for(int i = 0; i < 80; i++) {
                    vec3 p = ro + rd * t;
                    float d = map(p);
                    if(d < 0.001 || t > 20.0) break;
                    t += d;
                }
                
                vec3 col = vec3(0.0);
                if(t < 20.0) {
                    vec3 p = ro + rd * t;
                    vec3 normal = normalize(vec3(
                        map(p + vec3(0.001, 0, 0)) - map(p - vec3(0.001, 0, 0)),
                        map(p + vec3(0, 0.001, 0)) - map(p - vec3(0, 0.001, 0)),
                        map(p + vec3(0, 0, 0.001)) - map(p - vec3(0, 0, 0.001))
                    ));
                    
                    vec3 lightPos = vec3(2.0, 5.0, -5.0);
                    vec3 lightDir = normalize(lightPos - p);
                    float diff = max(dot(normal, lightDir), 0.0);
                    
                    vec3 baseColor = vec3(0.83, 0.68, 0.21); // Oro
                    vec3 glowColor = vec3(0.0, 0.14, 0.4); // Blu
                    
                    col = baseColor * diff + glowColor * (1.0 - diff);
                    float fresnel = pow(1.0 - max(dot(normal, -rd), 0.0), 3.0);
                    col += vec3(1.0, 0.9, 0.5) * fresnel;
                }
                
                // Background
                col += vec3(0.0, 0.0, 0.05) * (1.0 - length(uv));
                
                gl_FragColor = vec4(col, 1.0);
            }
        `;

        this.shaderMaterial = new THREE.ShaderMaterial({
            uniforms: {
                u_resolution: { value: new THREE.Vector2(this.width, this.height) },
                u_time: { value: 0 },
                u_mouse: { value: new THREE.Vector2(0, 0) }
            },
            vertexShader: `void main() { gl_Position = vec4(position, 1.0); }`,
            fragmentShader: fragmentShader,
            depthWrite: false,
            depthTest: false
        });

        const mesh = new THREE.Mesh(geometry, this.shaderMaterial);
        this.scene.add(mesh);
    }

    initStars() {
        const starGeo = new THREE.BufferGeometry();
        const starCount = 4000;
        const posArray = new Float32Array(starCount * 3);

        for(let i = 0; i < starCount; i++) {
            posArray[i * 3] = (Math.random() - 0.5) * 1000;
            posArray[i * 3 + 1] = (Math.random() - 0.5) * 1000;
            posArray[i * 3 + 2] = (Math.random() - 0.5) * 1000;
        }

        starGeo.setAttribute('position', new THREE.BufferAttribute(posArray, 3));
        
        const starMat = new THREE.PointsMaterial({
            color: 0xffffff,
            size: 1.5,
            transparent: true,
            opacity: 0.8,
            sizeAttenuation: true,
            blending: THREE.AdditiveBlending
        });

        this.starField = new THREE.Points(starGeo, starMat);
        this.starScene.add(this.starField);
    }

    createLabel(text) {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = 512;
        canvas.height = 128;
        
        ctx.font = 'bold 70px "Inter", sans-serif';
        ctx.fillStyle = '#D4AF37';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.shadowColor = 'rgba(0,0,0,1)';
        ctx.shadowBlur = 8;
        ctx.fillText(text, canvas.width / 2, canvas.height / 2);

        const texture = new THREE.CanvasTexture(canvas);
        const material = new THREE.SpriteMaterial({ map: texture, transparent: true, depthTest: false });
        const sprite = new THREE.Sprite(material);
        sprite.scale.set(10, 2.5, 1);
        sprite.renderOrder = 999;
        return sprite;
    }

    initMenu() {
        const items = [
            { id: 'concept', label: 'CONCEPT' },
            { id: 'problems', label: 'PROBLEMS' },
            { id: 'how', label: 'HOW IT WORKS' },
            { id: 'ammk', label: 'AMMk' }
        ];

        const radius = 12;
        const angleStep = (Math.PI * 2) / items.length;

        items.forEach((item, index) => {
            const angle = index * angleStep;
            const x = Math.cos(angle) * radius;
            const y = Math.sin(angle) * radius;
            
            const sprite = this.createLabel(item.label);
            sprite.position.set(x, y, -30);
            this.starScene.add(sprite);
        });
    }

    initEvents() {
        window.addEventListener('resize', () => {
            this.width = window.innerWidth;
            this.height = window.innerHeight;
            this.renderer.setSize(this.width, this.height);
            this.shaderMaterial.uniforms.u_resolution.value.set(this.width, this.height);
            this.starCamera.aspect = this.width / this.height;
            this.starCamera.updateProjectionMatrix();
        });

        document.addEventListener('mousemove', (e) => {
            this.mouse.x = (e.clientX / this.width) * 2 - 1;
            this.mouse.y = -(e.clientY / this.height) * 2 + 1;
            this.shaderMaterial.uniforms.u_mouse.value.set(e.clientX, this.height - e.clientY);
        });
    }

    animate() {
        requestAnimationFrame(() => this.animate());
        const time = this.clock.getElapsedTime();
        
        this.shaderMaterial.uniforms.u_time.value = time;
        
        // Move Stars
        const positions = this.starField.geometry.attributes.position.array;
        for(let i = 2; i < positions.length; i += 3) {
            positions[i] += 2.0;
            if (positions[i] > 500) positions[i] = -500;
        }
        this.starField.geometry.attributes.position.needsUpdate = true;

        this.renderer.clear();
        this.renderer.render(this.scene, this.camera); // Background Shader
        this.renderer.clearDepth();
        this.renderer.render(this.starScene, this.starCamera); // Stars & Menu
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new FlorenceGenesis('canvas-container');
});
