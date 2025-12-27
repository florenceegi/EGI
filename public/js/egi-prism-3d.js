/**
 * EGI Prism 3D Component - EXACT COPY FROM shapes-gallery.blade.php
 * The working premium prism with rounded corners and glass effect
 */

import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
import { EffectComposer } from 'three/addons/postprocessing/EffectComposer.js';
import { RenderPass } from 'three/addons/postprocessing/RenderPass.js';
import { UnrealBloomPass } from 'three/addons/postprocessing/UnrealBloomPass.js';
import { RoomEnvironment } from 'three/addons/environments/RoomEnvironment.js';
import { RoundedBoxGeometry } from 'three/addons/geometries/RoundedBoxGeometry.js';

class EgiPrism3D {
    constructor(container, config = {}) {
        this.container = container;
        this.config = this.mergeConfig(config);
        this.scene = null;
        this.camera = null;
        this.renderer = null;
        this.controls = null;
        this.composer = null;
        this.bloomPass = null;
        this.prismGroup = null;
        this.glassMesh = null;
        this.innerMesh = null;
        this.ring1 = null;
        this.ring2 = null;
        this.animationId = null;
        this.clock = new THREE.Clock();
        
        this.init();
    }
    
    mergeConfig(userConfig) {
        // EXACT values from shapes-gallery that work perfectly
        const defaultConfig = {
            // Glass material - from shapes-gallery line 552-563
            glassColor: '#ffdd44',  // User requested default
            metalness: 0.05,
            roughness: 0.02,
            opacity: 0.7,           // User requested default
            envMapIntensity: 0.8,
            clearcoat: 0.8,
            clearcoatRoughness: 0.1,
            emissiveIntensity: 0,   // User requested default
            
            // Bloom - from shapes-gallery line 344-351
            bloomStrength: 0,
            bloomRadius: 0.2,
            bloomThreshold: 0.85,
            
            // Size
            size: 60,
            
            // Animation
            autoRotate: true,
            rotationSpeed: 0.02,    // User requested default
            showRings: false,
            
            // Edges (New)
            showEdges: false, // Default FALSE to avoid ugly wireframe on rounded box
            edgeColor: '#ffffff',
            edgeColor: '#ffffff',
            edgeOpacity: 0.05,
            
            // Missing Defaults (Crucial for Glass)
            transmission: 1.0,
            ior: 1.5,
            thickness: 0.5,
            depth: 0.55 // If used elsewhere
        };
        return { ...defaultConfig, ...userConfig };
    }
    
    init() {
        const canvas = this.container.querySelector('canvas');
        if (!canvas) return;
        
        const rect = this.container.getBoundingClientRect();
        const width = rect.width || 300;
        const height = rect.height || 300;
        
        // Scene - from shapes-gallery
        this.scene = new THREE.Scene();
        this.scene.background = new THREE.Color(0x020202);
        
        // Camera - closer for card display context
        this.camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 2000);
        this.camera.position.set(0, 0, 160); // Closer = bigger prism
        
        // Renderer - exact from shapes-gallery
        this.renderer = new THREE.WebGLRenderer({
            canvas,
            antialias: true,
            alpha: true
        });
        this.renderer.setSize(width, height);
        this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        this.renderer.toneMapping = THREE.ACESFilmicToneMapping;
        this.renderer.toneMappingExposure = 1.2;
        
        // Environment - exact from shapes-gallery line 336-338
        const pmremGenerator = new THREE.PMREMGenerator(this.renderer);
        this.scene.environment = pmremGenerator.fromScene(new RoomEnvironment(), 0.04).texture;
        
        // Post-processing with bloom - exact from shapes-gallery
        this.composer = new EffectComposer(this.renderer);
        this.composer.addPass(new RenderPass(this.scene, this.camera));
        
        this.bloomPass = new UnrealBloomPass(
            new THREE.Vector2(width, height),
            1.5, 0.4, 0.85
        );
        this.bloomPass.threshold = this.config.bloomThreshold;
        this.bloomPass.strength = this.config.bloomStrength;
        this.bloomPass.radius = this.config.bloomRadius;
        this.composer.addPass(this.bloomPass);
        
        // Controls for card context
        this.controls = new OrbitControls(this.camera, canvas);
        this.controls.enableDamping = true;
        this.controls.dampingFactor = 0.05;
        this.controls.maxDistance = 300;
        this.controls.minDistance = 80;
        
        // Prism group
        this.prismGroup = new THREE.Group();
        this.scene.add(this.prismGroup);
        
        // Start animation
        this.animate();
        
        // Resize
        this.resize();
        window.addEventListener('resize', () => this.resize());
    }
    
    resize() {
        const rect = this.container.getBoundingClientRect();
        const width = rect.width || 300;
        const height = rect.height || 300;
        this.camera.aspect = width / height;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize(width, height);
        this.composer.setSize(width, height);
    }
    
    loadImage(imagePath, egiInfo = {}) {
        const textureLoader = new THREE.TextureLoader();
        textureLoader.load(imagePath, (texture) => {
            texture.colorSpace = THREE.SRGBColorSpace;
            texture.anisotropy = 16;
            this.createPrism(texture, egiInfo);
        }, undefined, (error) => {
            console.error('Error loading texture:', error);
            this.createPrism(null, egiInfo);
        });
    }
    
    // Create text texture - exact from shapes-gallery line 446-513
    createTextTexture(title, egiInfo) {
        const canvas = document.createElement('canvas');
        canvas.width = 512;
        canvas.height = 800;
        const ctx = canvas.getContext('2d');

        // DARK background - proper contrast
        const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
        gradient.addColorStop(0, '#0a1828');
        gradient.addColorStop(0.5, '#0d1f2d');
        gradient.addColorStop(1, '#071018');
        ctx.fillStyle = gradient;
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Outer border - gold accent
        ctx.strokeStyle = '#ffaa00';
        ctx.lineWidth = 6;
        ctx.strokeRect(16, 16, canvas.width - 32, canvas.height - 32);

        // Inner border - subtle
        ctx.strokeStyle = 'rgba(255, 170, 0, 0.4)';
        ctx.lineWidth = 2;
        ctx.strokeRect(28, 28, canvas.width - 56, canvas.height - 56);

        // Title - GOLD, bold, large
        ctx.fillStyle = '#ffaa00';
        ctx.font = 'bold 42px "Segoe UI", Arial, sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText(title.substring(0, 20), canvas.width / 2, 110);

        // Title underline
        ctx.strokeStyle = '#ffaa00';
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(80, 135);
        ctx.lineTo(canvas.width - 80, 135);
        ctx.stroke();

        // Info lines
        const lines = [
            `Token ID: #${egiInfo.id || 'N/A'}`,
            `Collection: ${(egiInfo.collection || 'N/A').substring(0, 18)}`,
            '---',
            'Powered by Algorand',
            'Florence EGI Platform'
        ];

        ctx.fillStyle = '#ffffff';
        ctx.font = '26px "Segoe UI", Arial, sans-serif';
        let yPos = 210;
        const lineHeight = 55;

        lines.forEach((line) => {
            if (line === '---') {
                ctx.strokeStyle = 'rgba(255, 170, 0, 0.5)';
                ctx.lineWidth = 1;
                ctx.beginPath();
                ctx.moveTo(100, yPos - 15);
                ctx.lineTo(canvas.width - 100, yPos - 15);
                ctx.stroke();
            } else {
                ctx.fillText(line, canvas.width / 2, yPos);
            }
            yPos += lineHeight;
        });

        // Bottom decoration
        ctx.fillStyle = '#00ddff';
        ctx.font = 'bold 20px "Segoe UI", monospace';
        ctx.fillText('◆ BLOCKCHAIN VERIFIED ◆', canvas.width / 2, canvas.height - 55);

        const texture = new THREE.CanvasTexture(canvas);
        texture.colorSpace = THREE.SRGBColorSpace;
        return texture;
    }
    
    createPrism(imageTexture, egiInfo = {}) {
        // Clear existing
        while (this.prismGroup.children.length > 0) {
            const child = this.prismGroup.children[0];
            if (child.geometry) child.geometry.dispose();
            if (child.material) {
                if (Array.isArray(child.material)) {
                    child.material.forEach(m => m.dispose());
                } else {
                    child.material.dispose();
                }
            }
            this.prismGroup.remove(child);
        }
        
        const size = this.config.size;
        
        // PRISM GEOMETRY - exact from shapes-gallery line 399-405
        // RoundedBoxGeometry for smooth rounded corners!
        const width = size * 1.1;
        const height = size * 1.8;
        const depth = size * 0.25;
        const radius = size * 0.08;
        const glassGeo = new RoundedBoxGeometry(width, height, depth, 4, radius);
        
        // GLASS MATERIAL - exact from shapes-gallery line 552-563
        const glassMat = new THREE.MeshPhysicalMaterial({
            color: new THREE.Color(this.config.glassColor),
            metalness: this.config.metalness,
            roughness: this.config.roughness,
            transparent: true,
            opacity: this.config.opacity,
            envMapIntensity: this.config.envMapIntensity,
            clearcoat: this.config.clearcoat,
            clearcoatRoughness: this.config.clearcoatRoughness,
            
            // CRITICAL FIX: Pass Glass Properties
            transmission: this.config.transmission,
            ior: this.config.ior,
            thickness: this.config.thickness,
            side: THREE.FrontSide,
            depthWrite: false
        });
        
        this.glassMesh = new THREE.Mesh(glassGeo, glassMat);
        this.glassMesh.renderOrder = 2;
        this.prismGroup.add(this.glassMesh);
        
        // INNER CONTENT - exact from shapes-gallery line 569-631
        const innerScale = 0.9;
        const innerWidth = size * innerScale;
        const innerHeight = size * 1.55 * innerScale;
        const innerDepth = size * 0.15 * innerScale;
        
        // Text texture for back
        const textTexture = this.createTextTexture(
            egiInfo.title || 'EGI Asset',
            egiInfo
        );
        
        // Side material - dark
        const sideMat = new THREE.MeshStandardMaterial({
            color: 0x050810,
            roughness: 1.0,
            metalness: 0.0,
            transparent: true,
            opacity: 0.8
        });
        
        // Front face - Image with emissive glow
        const frontMat = new THREE.MeshStandardMaterial({
            map: imageTexture,
            side: THREE.FrontSide,
            emissive: 0xffffff,
            emissiveMap: imageTexture,
            emissiveIntensity: this.config.emissiveIntensity, // Dynamic
            roughness: 1.0,
            metalness: 0.0
        });
        
        // Back face - Text info
        const backMat = new THREE.MeshStandardMaterial({
            map: textTexture,
            side: THREE.FrontSide,
            emissive: 0xffffff,
            emissiveMap: textTexture,
            emissiveIntensity: this.config.emissiveIntensity * 0.6, // Relative to front
            roughness: 1.0,
            metalness: 0.0
        });
        
        // Materials array: [+X, -X, +Y, -Y, +Z (front), -Z (back)]
        const materials = [
            sideMat, // right
            sideMat, // left
            sideMat, // top
            sideMat, // bottom
            frontMat, // front - image
            backMat   // back - text
        ];
        
        const innerGeo = new THREE.BoxGeometry(innerWidth, innerHeight, innerDepth);
        this.innerMesh = new THREE.Mesh(innerGeo, materials);
        this.innerMesh.renderOrder = 1;
        this.prismGroup.add(this.innerMesh);
        
        // DECORATIVE RINGS - exact from shapes-gallery line 656-682
        if (this.config.showRings) {
            const ringMat = new THREE.MeshStandardMaterial({
                color: 0xffffff,
                metalness: 0.8,
                roughness: 0.2,
                transparent: true,
                opacity: 0.5
            });
            
            this.ring1 = new THREE.Mesh(
                new THREE.TorusGeometry(size * 1.3, 0.4, 16, 100),
                ringMat
            );
            this.ring1.rotation.x = Math.PI / 1.7;
            this.prismGroup.add(this.ring1);
            
            this.ring2 = new THREE.Mesh(
                new THREE.TorusGeometry(size * 1.5, 0.4, 16, 100),
                ringMat
            );
            this.ring2.rotation.y = Math.PI / 2;
            this.prismGroup.add(this.ring2);
        }
        
        // EDGES (New functionality)
        if (this.config.showEdges) {
            this.createEdges(glassGeo);
        }
    }
    
    createEdges(geometry) {
        // Create edges helper
        const edges = new THREE.EdgesGeometry(geometry);
        const lineMat = new THREE.LineBasicMaterial({
            color: new THREE.Color(this.config.edgeColor),
            transparent: true,
            opacity: this.config.edgeOpacity
        });
        
        this.edgeLines = new THREE.LineSegments(edges, lineMat);
        this.prismGroup.add(this.edgeLines);
    }
    
    updateConfig(newConfig) {
        this.config = { ...this.config, ...newConfig };
        
        if (this.glassMesh && this.glassMesh.material) {
            const mat = this.glassMesh.material;
            mat.color.set(this.config.glassColor);
            mat.metalness = this.config.metalness;
            mat.roughness = this.config.roughness;
            mat.opacity = this.config.opacity;
            mat.transmission = this.config.transmission;
            mat.ior = this.config.ior;
            mat.thickness = this.config.thickness;
            mat.clearcoat = this.config.clearcoat;
            mat.clearcoatRoughness = this.config.clearcoatRoughness;
            mat.needsUpdate = true;
        }

        // Update Inner Mesh Emissive (Brightness)
        if (this.innerMesh && this.innerMesh.material) {
             // Materials array: [+X, -X, +Y, -Y, +Z (front), -Z (back)]
             // Index 4 is Front, Index 5 is Back
             if (this.innerMesh.material[4]) {
                 this.innerMesh.material[4].emissiveIntensity = this.config.emissiveIntensity;
             }
             if (this.innerMesh.material[5]) {
                 this.innerMesh.material[5].emissiveIntensity = this.config.emissiveIntensity * 0.6;
             }
        }
        
        // Update Edges
        if (newConfig.showEdges !== undefined || newConfig.edgeColor || newConfig.edgeOpacity) {
            if (this.config.showEdges) {
                if (!this.edgeLines && this.glassMesh) {
                    this.createEdges(this.glassMesh.geometry);
                } else if (this.edgeLines) {
                    this.edgeLines.visible = true;
                    this.edgeLines.material.color.set(this.config.edgeColor);
                    this.edgeLines.material.opacity = this.config.edgeOpacity;
                }
            } else if (this.edgeLines) {
                this.edgeLines.visible = false;
            }
        }
        
        if (this.bloomPass) {
            this.bloomPass.strength = this.config.bloomStrength;
            this.bloomPass.radius = this.config.bloomRadius;
            this.bloomPass.threshold = this.config.bloomThreshold;
        }
    }
    
    animate() {
        this.animationId = requestAnimationFrame(() => this.animate());
        
        const time = this.clock.getElapsedTime();
        
        // Rotate prism - exact from shapes-gallery line 739
        if (this.prismGroup && this.config.autoRotate) {
            this.prismGroup.rotation.y += this.config.rotationSpeed;
        }
        
        // Animate rings - exact from shapes-gallery line 742-747
        if (this.ring1) {
            this.ring1.rotation.z += 0.008;
        }
        if (this.ring2) {
            this.ring2.rotation.y -= 0.01;
        }
        
        if (this.controls) {
            this.controls.update();
        }
        
        if (this.composer) {
            this.composer.render();
        }
    }
    
    destroy() {
        if (this.animationId) {
            cancelAnimationFrame(this.animationId);
        }
        
        if (this.renderer) {
            this.renderer.dispose();
        }
        
        if (this.composer) {
            this.composer.dispose();
        }
        
        if (this.scene) {
            this.scene.traverse((object) => {
                if (object.geometry) object.geometry.dispose();
                if (object.material) {
                    if (Array.isArray(object.material)) {
                        object.material.forEach(m => m.dispose());
                    } else {
                        object.material.dispose();
                    }
                }
            });
        }
    }
}

window.EgiPrism3D = EgiPrism3D;
export { EgiPrism3D };
