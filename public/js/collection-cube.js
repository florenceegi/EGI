/**
 * Collection Cube 3D - Florence EGI
 * 
 * Modulo Three.js per gestire cubi 3D con immagini EGI sulle 6 facce.
 * Ogni istanza gestisce un singolo cubo nel carousel.
 */

import * as THREE from 'https://unpkg.com/three@0.160.0/build/three.module.js';

// ============================================================================
// COLLECTION CUBE CLASS
// ============================================================================

class CollectionCube {
    constructor(container, options = {}) {
        this.container = container;
        this.options = {
            autoRotate: true,
            rotationSpeed: 0.5,
            glassColor: 0x88ccff,
            glassOpacity: 0.15,
            glassMetalness: 0.9,
            glassRoughness: 0.05,
            edgeColor: 0xffffff,
            edgeOpacity: 0.5,
            showEdges: true,
            ...options
        };
        
        this.cubeGroup = null;
        this.glassMesh = null;
        this.innerMesh = null;
        this.edgesMesh = null;
        this.faceMaterials = [];
        this.faceTextures = [];
        this.isAnimating = false;
        this.animationId = null;
        
        this.init();
    }
    
    init() {
        // Scene
        this.scene = new THREE.Scene();
        
        // Camera
        const rect = this.container.getBoundingClientRect();
        const aspect = rect.width / rect.height || 1;
        this.camera = new THREE.PerspectiveCamera(50, aspect, 0.1, 100);
        this.camera.position.set(0, 0, 3.5);
        
        // Renderer
        this.renderer = new THREE.WebGLRenderer({
            antialias: true,
            alpha: true,
            powerPreference: 'high-performance'
        });
        this.renderer.setSize(rect.width, rect.height);
        this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        this.renderer.toneMapping = THREE.ACESFilmicToneMapping;
        this.renderer.toneMappingExposure = 1.2;
        this.container.appendChild(this.renderer.domElement);
        
        // Lights
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
        this.scene.add(ambientLight);
        
        const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
        directionalLight.position.set(5, 5, 5);
        this.scene.add(directionalLight);
        
        const backLight = new THREE.DirectionalLight(0x8888ff, 0.3);
        backLight.position.set(-5, -5, -5);
        this.scene.add(backLight);
        
        // Create cube
        this.createCube();
        
        // Start animation
        this.startAnimation();
        
        // Resize observer
        this.resizeObserver = new ResizeObserver(() => this.resize());
        this.resizeObserver.observe(this.container);
        
        // Mouse interaction for manual rotation
        this.setupMouseInteraction();
    }
    
    createCube() {
        this.cubeGroup = new THREE.Group();
        
        const size = 1.2;
        
        // 1. Inner cube with face textures
        const boxGeometry = new THREE.BoxGeometry(size, size, size);
        
        // Create 6 materials for 6 faces (will be updated with textures)
        this.faceMaterials = [];
        for (let i = 0; i < 6; i++) {
            const material = new THREE.MeshStandardMaterial({
                color: 0x1a1a2e,
                roughness: 0.8,
                metalness: 0.2,
                side: THREE.FrontSide
            });
            this.faceMaterials.push(material);
        }
        
        this.innerMesh = new THREE.Mesh(boxGeometry, this.faceMaterials);
        this.innerMesh.renderOrder = 1;
        this.cubeGroup.add(this.innerMesh);
        
        // 2. Glass shell (slightly larger)
        const glassSize = size * 1.05;
        const glassGeometry = new THREE.BoxGeometry(glassSize, glassSize, glassSize);
        const glassMaterial = new THREE.MeshPhysicalMaterial({
            color: this.options.glassColor,
            metalness: this.options.glassMetalness,
            roughness: this.options.glassRoughness,
            transparent: true,
            opacity: this.options.glassOpacity,
            side: THREE.FrontSide,
            depthWrite: false,
            envMapIntensity: 0.5
        });
        
        this.glassMesh = new THREE.Mesh(glassGeometry, glassMaterial);
        this.glassMesh.renderOrder = 2;
        this.cubeGroup.add(this.glassMesh);
        
        // 3. Edges
        if (this.options.showEdges) {
            const edgesGeometry = new THREE.EdgesGeometry(glassGeometry);
            const edgesMaterial = new THREE.LineBasicMaterial({
                color: this.options.edgeColor,
                transparent: true,
                opacity: this.options.edgeOpacity,
                linewidth: 1
            });
            this.edgesMesh = new THREE.LineSegments(edgesGeometry, edgesMaterial);
            this.edgesMesh.renderOrder = 3;
            this.cubeGroup.add(this.edgesMesh);
        }
        
        // Initial rotation for better view
        this.cubeGroup.rotation.x = Math.PI * 0.15;
        this.cubeGroup.rotation.y = Math.PI * 0.25;
        
        this.scene.add(this.cubeGroup);
    }
    
    /**
     * Set images for cube faces
     * @param {Array} images - Array of up to 6 image URLs
     * Order: [+X, -X, +Y, -Y, +Z, -Z] = [right, left, top, bottom, front, back]
     */
    setFaceImages(images) {
        const textureLoader = new THREE.TextureLoader();
        
        images.forEach((imageUrl, index) => {
            if (index >= 6) return;
            
            if (imageUrl) {
                textureLoader.load(imageUrl, (texture) => {
                    texture.colorSpace = THREE.SRGBColorSpace;
                    texture.anisotropy = 4;
                    
                    this.faceTextures[index] = texture;
                    this.faceMaterials[index].map = texture;
                    this.faceMaterials[index].color.set(0xffffff);
                    this.faceMaterials[index].emissive.set(0xffffff);
                    this.faceMaterials[index].emissiveMap = texture;
                    this.faceMaterials[index].emissiveIntensity = 0.3;
                    this.faceMaterials[index].needsUpdate = true;
                }, undefined, (err) => {
                    console.warn('Failed to load texture:', imageUrl, err);
                });
            } else {
                // Empty face - dark color
                this.faceMaterials[index].map = null;
                this.faceMaterials[index].color.set(0x1a1a2e);
                this.faceMaterials[index].emissiveIntensity = 0;
                this.faceMaterials[index].needsUpdate = true;
            }
        });
    }
    
    /**
     * Update material properties
     */
    updateGlass(props) {
        if (props.color !== undefined) {
            this.options.glassColor = props.color;
            this.glassMesh.material.color.set(props.color);
        }
        if (props.opacity !== undefined) {
            this.options.glassOpacity = props.opacity;
            this.glassMesh.material.opacity = props.opacity;
        }
        if (props.metalness !== undefined) {
            this.options.glassMetalness = props.metalness;
            this.glassMesh.material.metalness = props.metalness;
        }
        if (props.roughness !== undefined) {
            this.options.glassRoughness = props.roughness;
            this.glassMesh.material.roughness = props.roughness;
        }
        this.glassMesh.material.needsUpdate = true;
    }
    
    /**
     * Update edge properties
     */
    updateEdges(props) {
        if (!this.edgesMesh) return;
        
        if (props.color !== undefined) {
            this.options.edgeColor = props.color;
            this.edgesMesh.material.color.set(props.color);
        }
        if (props.opacity !== undefined) {
            this.options.edgeOpacity = props.opacity;
            this.edgesMesh.material.opacity = props.opacity;
        }
        if (props.visible !== undefined) {
            this.options.showEdges = props.visible;
            this.edgesMesh.visible = props.visible;
        }
        this.edgesMesh.material.needsUpdate = true;
    }
    
    /**
     * Update rotation settings
     */
    updateRotation(props) {
        if (props.autoRotate !== undefined) {
            this.options.autoRotate = props.autoRotate;
        }
        if (props.speed !== undefined) {
            this.options.rotationSpeed = props.speed;
        }
    }
    
    /**
     * Apply a preset style
     */
    applyPreset(presetName) {
        const presets = {
            crystal: {
                glassColor: 0x88ccff,
                glassOpacity: 0.15,
                glassMetalness: 0.9,
                glassRoughness: 0.05,
                edgeColor: 0xffffff,
                edgeOpacity: 0.5
            },
            neon: {
                glassColor: 0xff00ff,
                glassOpacity: 0.2,
                glassMetalness: 0.5,
                glassRoughness: 0.1,
                edgeColor: 0x00ffff,
                edgeOpacity: 0.8
            },
            gold: {
                glassColor: 0xffaa00,
                glassOpacity: 0.25,
                glassMetalness: 1.0,
                glassRoughness: 0.2,
                edgeColor: 0xffd700,
                edgeOpacity: 0.6
            },
            ice: {
                glassColor: 0xaaddff,
                glassOpacity: 0.1,
                glassMetalness: 0.7,
                glassRoughness: 0.02,
                edgeColor: 0xccffff,
                edgeOpacity: 0.4
            },
            ember: {
                glassColor: 0xff4400,
                glassOpacity: 0.2,
                glassMetalness: 0.6,
                glassRoughness: 0.15,
                edgeColor: 0xff8800,
                edgeOpacity: 0.7
            },
            hologram: {
                glassColor: 0x00ff88,
                glassOpacity: 0.12,
                glassMetalness: 0.8,
                glassRoughness: 0.05,
                edgeColor: 0x88ff88,
                edgeOpacity: 0.6
            }
        };
        
        const preset = presets[presetName];
        if (preset) {
            this.updateGlass({
                color: preset.glassColor,
                opacity: preset.glassOpacity,
                metalness: preset.glassMetalness,
                roughness: preset.glassRoughness
            });
            this.updateEdges({
                color: preset.edgeColor,
                opacity: preset.edgeOpacity
            });
        }
    }
    
    /**
     * Get current configuration
     */
    getConfig() {
        return {
            autoRotate: this.options.autoRotate,
            rotationSpeed: this.options.rotationSpeed,
            glassColor: this.options.glassColor,
            glassOpacity: this.options.glassOpacity,
            glassMetalness: this.options.glassMetalness,
            glassRoughness: this.options.glassRoughness,
            edgeColor: this.options.edgeColor,
            edgeOpacity: this.options.edgeOpacity,
            showEdges: this.options.showEdges,
            faceImages: this.faceTextures.map((_, i) => this.options.faceImages?.[i] || null)
        };
    }
    
    /**
     * Load configuration
     */
    loadConfig(config) {
        if (config.autoRotate !== undefined) this.options.autoRotate = config.autoRotate;
        if (config.rotationSpeed !== undefined) this.options.rotationSpeed = config.rotationSpeed;
        
        this.updateGlass({
            color: config.glassColor,
            opacity: config.glassOpacity,
            metalness: config.glassMetalness,
            roughness: config.glassRoughness
        });
        
        this.updateEdges({
            color: config.edgeColor,
            opacity: config.edgeOpacity,
            visible: config.showEdges
        });
        
        if (config.faceImages) {
            this.setFaceImages(config.faceImages);
        }
    }
    
    setupMouseInteraction() {
        let isDragging = false;
        let previousMousePosition = { x: 0, y: 0 };
        
        this.renderer.domElement.addEventListener('mousedown', (e) => {
            isDragging = true;
            previousMousePosition = { x: e.clientX, y: e.clientY };
        });
        
        this.renderer.domElement.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            
            const deltaX = e.clientX - previousMousePosition.x;
            const deltaY = e.clientY - previousMousePosition.y;
            
            this.cubeGroup.rotation.y += deltaX * 0.01;
            this.cubeGroup.rotation.x += deltaY * 0.01;
            
            previousMousePosition = { x: e.clientX, y: e.clientY };
        });
        
        this.renderer.domElement.addEventListener('mouseup', () => {
            isDragging = false;
        });
        
        this.renderer.domElement.addEventListener('mouseleave', () => {
            isDragging = false;
        });
        
        // Touch support
        this.renderer.domElement.addEventListener('touchstart', (e) => {
            if (e.touches.length === 1) {
                isDragging = true;
                previousMousePosition = { x: e.touches[0].clientX, y: e.touches[0].clientY };
            }
        });
        
        this.renderer.domElement.addEventListener('touchmove', (e) => {
            if (!isDragging || e.touches.length !== 1) return;
            
            const deltaX = e.touches[0].clientX - previousMousePosition.x;
            const deltaY = e.touches[0].clientY - previousMousePosition.y;
            
            this.cubeGroup.rotation.y += deltaX * 0.01;
            this.cubeGroup.rotation.x += deltaY * 0.01;
            
            previousMousePosition = { x: e.touches[0].clientX, y: e.touches[0].clientY };
        });
        
        this.renderer.domElement.addEventListener('touchend', () => {
            isDragging = false;
        });
    }
    
    startAnimation() {
        if (this.isAnimating) return;
        this.isAnimating = true;
        
        const animate = () => {
            if (!this.isAnimating) return;
            
            this.animationId = requestAnimationFrame(animate);
            
            if (this.options.autoRotate && this.cubeGroup) {
                this.cubeGroup.rotation.y += 0.005 * this.options.rotationSpeed;
            }
            
            this.renderer.render(this.scene, this.camera);
        };
        
        animate();
    }
    
    stopAnimation() {
        this.isAnimating = false;
        if (this.animationId) {
            cancelAnimationFrame(this.animationId);
            this.animationId = null;
        }
    }
    
    resize() {
        const rect = this.container.getBoundingClientRect();
        if (rect.width === 0 || rect.height === 0) return;
        
        this.camera.aspect = rect.width / rect.height;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize(rect.width, rect.height);
    }
    
    dispose() {
        this.stopAnimation();
        
        if (this.resizeObserver) {
            this.resizeObserver.disconnect();
        }
        
        // Dispose geometries and materials
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
        
        // Dispose textures
        this.faceTextures.forEach(texture => {
            if (texture) texture.dispose();
        });
        
        this.renderer.dispose();
        
        // Remove canvas
        if (this.renderer.domElement.parentNode) {
            this.renderer.domElement.parentNode.removeChild(this.renderer.domElement);
        }
    }
}

// ============================================================================
// GLOBAL REGISTRY
// ============================================================================

window.CollectionCubes = window.CollectionCubes || {};

/**
 * Initialize a collection cube
 * @param {string} id - Unique identifier
 * @param {HTMLElement} container - Container element
 * @param {Object} options - Configuration options
 * @returns {CollectionCube}
 */
window.initCollectionCube = function(id, container, options = {}) {
    // Dispose existing instance if any
    if (window.CollectionCubes[id]) {
        window.CollectionCubes[id].dispose();
    }
    
    const cube = new CollectionCube(container, options);
    window.CollectionCubes[id] = cube;
    return cube;
};

/**
 * Get a collection cube instance by ID
 * @param {string} id
 * @returns {CollectionCube|null}
 */
window.getCollectionCube = function(id) {
    return window.CollectionCubes[id] || null;
};

/**
 * Dispose a collection cube
 * @param {string} id
 */
window.disposeCollectionCube = function(id) {
    if (window.CollectionCubes[id]) {
        window.CollectionCubes[id].dispose();
        delete window.CollectionCubes[id];
    }
};

export { CollectionCube };
