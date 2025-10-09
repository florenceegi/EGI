/**
 * N.A.T.A.N. Upload Manager
 *
 * @package resources/ts/natan
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - N.A.T.A.N.)
 * @date 2025-10-09
 * @purpose Handles document upload with drag&drop and progress tracking
 */

import { NatanApiClient } from "./ApiClient";
import type { UploadEventDetail } from "./types";

/**
 * Upload Manager Class
 *
 * Manages file upload with drag&drop interface and real-time progress
 */
export class UploadManager {
    private dropzone: HTMLElement;
    private fileInput: HTMLInputElement;
    private api: NatanApiClient;
    private onUploadComplete?: (jobId: string) => void;
    private onUploadStart?: (filename: string) => void;
    private onUploadError?: (error: string) => void;

    /**
     * Constructor
     *
     * @param dropzoneSelector CSS selector for dropzone element
     * @param fileInputSelector CSS selector for file input element
     * @param api NatanApiClient instance
     */
    constructor(
        dropzoneSelector: string,
        fileInputSelector: string,
        api: NatanApiClient
    ) {
        const dropzone = document.querySelector<HTMLElement>(dropzoneSelector);
        const fileInput =
            document.querySelector<HTMLInputElement>(fileInputSelector);

        if (!dropzone || !fileInput) {
            throw new Error("Dropzone or file input not found");
        }

        this.dropzone = dropzone;
        this.fileInput = fileInput;
        this.api = api;

        this.init();
    }

    /**
     * Initialize drag&drop listeners
     */
    private init(): void {
        // Prevent default drag behaviors
        ["dragenter", "dragover", "dragleave", "drop"].forEach((eventName) => {
            this.dropzone.addEventListener(
                eventName,
                (e) => this.preventDefaults(e),
                false
            );
            document.body.addEventListener(
                eventName,
                (e) => this.preventDefaults(e),
                false
            );
        });

        // Highlight drop zone when item is dragged over it
        ["dragenter", "dragover"].forEach((eventName) => {
            this.dropzone.addEventListener(
                eventName,
                () => this.highlight(),
                false
            );
        });

        ["dragleave", "drop"].forEach((eventName) => {
            this.dropzone.addEventListener(
                eventName,
                () => this.unhighlight(),
                false
            );
        });

        // Handle dropped files
        this.dropzone.addEventListener(
            "drop",
            (e) => this.handleDrop(e as DragEvent),
            false
        );

        // Handle file input change
        this.fileInput.addEventListener("change", () =>
            this.handleFiles(this.fileInput.files)
        );

        // Click dropzone to trigger file input
        this.dropzone.addEventListener("click", () => this.fileInput.click());
    }

    /**
     * Prevent default drag behaviors
     */
    private preventDefaults(e: Event): void {
        e.preventDefault();
        e.stopPropagation();
    }

    /**
     * Highlight dropzone
     */
    private highlight(): void {
        this.dropzone.classList.add("dragover");
    }

    /**
     * Remove highlight
     */
    private unhighlight(): void {
        this.dropzone.classList.remove("dragover");
    }

    /**
     * Handle drop event
     */
    private handleDrop(e: DragEvent): void {
        const dt = e.dataTransfer;
        const files = dt?.files;
        if (files) {
            this.handleFiles(files);
        }
    }

    /**
     * Handle file selection/drop
     */
    private async handleFiles(files: FileList | null): Promise<void> {
        if (!files || files.length === 0) return;

        // Process each file
        for (let i = 0; i < files.length; i++) {
            const file = files[i];

            // Validate file
            const validation = this.validateFile(file);
            if (!validation.valid) {
                this.showError(validation.error || "File non valido");
                continue;
            }

            // Upload file
            await this.uploadFile(file);
        }
    }

    /**
     * Validate file type and size
     *
     * @param file File to validate
     * @returns Validation result
     */
    private validateFile(file: File): { valid: boolean; error?: string } {
        // Validate type
        const validTypes = ["application/pdf", "application/pkcs7-mime"];
        const validExtensions = [".pdf", ".p7m"];

        const isValidType =
            validTypes.includes(file.type) ||
            validExtensions.some((ext) =>
                file.name.toLowerCase().endsWith(ext)
            );

        if (!isValidType) {
            return {
                valid: false,
                error: `Tipo file non valido: ${file.name}. Solo PDF o P7M.`,
            };
        }

        // Validate size (10MB max)
        const maxSize = 10 * 1024 * 1024; // 10MB
        if (file.size > maxSize) {
            return {
                valid: false,
                error: `File troppo grande: ${file.name}. Max 10MB.`,
            };
        }

        return { valid: true };
    }

    /**
     * Upload file to server
     *
     * @param file File to upload
     */
    private async uploadFile(file: File): Promise<void> {
        this.showProgress(file.name);

        if (this.onUploadStart) {
            this.onUploadStart(file.name);
        }

        // Dispatch upload started event
        this.dispatchEvent("natan:upload-started", {
            jobId: "pending",
            filename: file.name,
            status: "pending",
        });

        try {
            const result = await this.api.uploadDocument(file);

            this.showSuccess(`Upload completato: ${file.name}. Processing...`);

            if (this.onUploadComplete) {
                this.onUploadComplete(result.job_id);
            }

            // Dispatch upload completed event
            this.dispatchEvent("natan:upload-completed", {
                jobId: result.job_id,
                filename: file.name,
                status: "pending",
            });

            // Start polling job status
            this.pollJobStatus(result.job_id, file.name);
        } catch (error) {
            const errorMessage =
                error instanceof Error ? error.message : "Upload fallito";
            this.showError(errorMessage);

            if (this.onUploadError) {
                this.onUploadError(errorMessage);
            }

            // Dispatch upload failed event
            this.dispatchEvent("natan:upload-failed", {
                jobId: "failed",
                filename: file.name,
                status: "failed",
            });
        }
    }

    /**
     * Poll job status until completed
     *
     * @param jobId Job UUID
     * @param filename Original filename
     */
    private async pollJobStatus(
        jobId: string,
        filename: string
    ): Promise<void> {
        const maxAttempts = 60; // 60 * 2s = 2 min max
        let attempts = 0;

        const poll = async (): Promise<void> => {
            try {
                const result = await this.api.getJobStatus(jobId);

                if (result.status === "completed") {
                    this.showSuccess(`Documento processato: ${filename}`);

                    // Dispatch act processed event
                    if (result.act) {
                        window.dispatchEvent(
                            new CustomEvent("natan:act-processed", {
                                detail: { act: result.act },
                            })
                        );
                    }
                    return;
                }

                if (result.status === "failed") {
                    this.showError(`Processing fallito: ${filename}`);
                    return;
                }

                // Still processing, poll again
                attempts++;
                if (attempts < maxAttempts) {
                    setTimeout(poll, 2000);
                } else {
                    this.showError(
                        `Timeout: processing troppo lungo per ${filename}`
                    );
                }
            } catch (error) {
                console.error("Polling error:", error);
                this.showError("Errore durante il polling dello stato.");
            }
        };

        poll();
    }

    /**
     * Dispatch custom event
     *
     * @param eventName Event name
     * @param detail Event detail
     */
    private dispatchEvent(eventName: string, detail: UploadEventDetail): void {
        window.dispatchEvent(new CustomEvent(eventName, { detail }));
    }

    /**
     * Show progress indicator
     *
     * @param filename File name
     */
    private showProgress(filename: string): void {
        this.setMessage(`Uploading ${filename}...`, "info");
    }

    /**
     * Show success message
     *
     * @param message Success message
     */
    private showSuccess(message: string): void {
        this.setMessage(message, "success");
    }

    /**
     * Show error message
     *
     * @param message Error message
     */
    private showError(message: string): void {
        this.setMessage(message, "error");
    }

    /**
     * Set message in UI
     *
     * @param message Message text
     * @param type Message type
     */
    private setMessage(
        message: string,
        type: "info" | "success" | "error"
    ): void {
        // Find or create message container
        let messageEl =
            this.dropzone.querySelector<HTMLDivElement>(".upload-message");

        if (!messageEl) {
            messageEl = document.createElement("div");
            messageEl.className = "upload-message mt-4 p-3 rounded text-sm";
            this.dropzone.appendChild(messageEl);
        }

        // Set styling based on type
        messageEl.className = "upload-message mt-4 p-3 rounded text-sm ";

        switch (type) {
            case "info":
                messageEl.className += "bg-blue-100 text-blue-800";
                break;
            case "success":
                messageEl.className += "bg-green-100 text-green-800";
                break;
            case "error":
                messageEl.className += "bg-red-100 text-red-800";
                break;
        }

        messageEl.textContent = message;

        // Auto-hide success/error after 5s
        if (type !== "info") {
            setTimeout(() => {
                messageEl?.remove();
            }, 5000);
        }
    }

    /**
     * Set upload complete callback
     *
     * @param callback Callback function
     */
    public onComplete(callback: (jobId: string) => void): void {
        this.onUploadComplete = callback;
    }

    /**
     * Set upload start callback
     *
     * @param callback Callback function
     */
    public onStart(callback: (filename: string) => void): void {
        this.onUploadStart = callback;
    }

    /**
     * Set upload error callback
     *
     * @param callback Callback function
     */
    public onError(callback: (error: string) => void): void {
        this.onUploadError = callback;
    }

    /**
     * Reset upload interface
     */
    public reset(): void {
        this.fileInput.value = "";
        const messageEl = this.dropzone.querySelector(".upload-message");
        messageEl?.remove();
    }
}
