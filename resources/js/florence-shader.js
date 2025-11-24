import * as THREE from 'three';

/**
 * FlorenceEGI Hero - SHADER ONLY
 * 
 * This file contains ONLY the Raymarching Shader.
 * No meshes, no text, no distractions.
 */

console.log("FlorenceEGI: Loading SHADER ONLY version...");

class FlorenceShader {
    constructor(canvasContainerId) {
        this.container = document.getElementById(canvasContainerId);
        if (!this.container) {
            console.error("Canvas container not found!");
            return;
        }

        // Force cleanup of any existing canvas
        while(this.container.firstChild) {
            this.container.removeChild(this.container.firstChild);
        }

        this.width = window.innerWidth;
        this.height = window.innerHeight;

        this.renderer = new THREE.WebGLRenderer({ antialias: true, alpha: false });
        this.renderer.setSize(this.width, this.height);
        this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        this.container.appendChild(this.renderer.domElement);

        this.clock = new THREE.Clock();
        this.mouse = new THREE.Vector2();

        this.scene = new THREE.Scene();
        this.camera = new THREE.OrthographicCamera(-1, 1, 1, -1, 0, 1);

        // --- THE SHADER QUAD ---
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
                
                col += vec3(0.0, 0.0, 0.05) * (1.0 - length(uv));
                
                gl_FragColor = vec4(col, 1.0);
            }
        `;

        this.material = new THREE.ShaderMaterial({
            uniforms: {
                u_resolution: { value: new THREE.Vector2(this.width, this.height) },
                u_time: { value: 0 },
                u_mouse: { value: new THREE.Vector2(0, 0) }
            },
            vertexShader: `void main() { gl_Position = vec4(position, 1.0); }`,
            fragmentShader: fragmentShader
        });

        const mesh = new THREE.Mesh(geometry, this.material);
        this.scene.add(mesh);

        this.initEvents();
        this.animate();
    }

    initEvents() {
        window.addEventListener('resize', () => {
            this.width = window.innerWidth;
            this.height = window.innerHeight;
            this.renderer.setSize(this.width, this.height);
            this.material.uniforms.u_resolution.value.set(this.width, this.height);
        });

        document.addEventListener('mousemove', (e) => {
            this.mouse.x = (e.clientX / this.width) * 2 - 1;
            this.mouse.y = -(e.clientY / this.height) * 2 + 1;
            this.material.uniforms.u_mouse.value.set(e.clientX, this.height - e.clientY);
        });
    }

    animate() {
        requestAnimationFrame(() => this.animate());
        this.material.uniforms.u_time.value = this.clock.getElapsedTime();
        this.renderer.render(this.scene, this.camera);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new FlorenceShader('canvas-container');
});
