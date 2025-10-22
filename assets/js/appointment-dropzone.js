/**
 * Shared Appointment Dropzone Functionality
 * Used by both appointment listing page and view page
 */

// Global variables for file management
var appointmentFiles = [];
var appointmentId = null;

/**
 * Get unique file identifier
 */
function getFileIdentifier(file) {
    return file.name + '-' + file.size + '-' + file.lastModified;
}

/**
 * Update input files with current appointmentFiles array
 */
function updateInputFiles(inputElement) {
    const dt = new DataTransfer();
    if (appointmentFiles.length > 0) {
        appointmentFiles.forEach(file => {
            dt.items.add(file);
        });
    }
    inputElement.files = dt.files;
}

/**
 * Add new files to dropzone with validation
 */
function addNewFiles(newFiles, inputElement, dropZoneElement) {
    // Check maximum file limit (10 files)
    const maxFiles = 10;
    const currentFileCount = appointmentFiles.length;
    const newFileCount = newFiles.length;
    
    if (currentFileCount + newFileCount > maxFiles) {
        alert_float('warning', 'Maximum ' + maxFiles + ' files allowed. You currently have ' + currentFileCount + ' file(s).');
        return;
    }
    
    // Check file types (allow common document and image types)
    const allowedFileTypes = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'application/pdf', 'application/msword', 
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation'
    ];
    
    const invalidFiles = newFiles.filter(file => !allowedFileTypes.includes(file.type));
    
    if (invalidFiles.length > 0) {
        showMessage('Invalid file type. Only images, PDFs, and Office documents are allowed.', dropZoneElement);
        return;
    }
    
    // Check total file size (max 50MB for appointments)
    const maxSize = 50 * 1024 * 1024; // 50 MB in bytes
    const totalSize = appointmentFiles.reduce((acc, file) => acc + file.size, 0) +
        newFiles.reduce((acc, file) => acc + file.size, 0);
    
    if (totalSize > maxSize) {
        showMessage('Total file size exceeds the maximum limit of 50 MB.', dropZoneElement);
        return;
    }
    
    // Add new files
    appointmentFiles = appointmentFiles.concat(newFiles);
    updateInputFiles(inputElement);
    newFiles.forEach(file => {
        updateThumbnail(dropZoneElement, file, inputElement);
    });
    
    // Update the hidden field with file count
    $('#appointment_uploaded_files').val(appointmentFiles.length);
}

/**
 * Show temporary message in dropzone
 */
function showMessage(message, dropZoneElement) {
    let messageElement = document.createElement('div');
    messageElement.textContent = message;
    messageElement.classList.add('upload-message');
    
    dropZoneElement.appendChild(messageElement);
    setTimeout(() => {
        messageElement.remove();
    }, 5000);
}

/**
 * Create and display thumbnail for uploaded file
 */
function updateThumbnail(dropZoneElement, file, inputElement) {
    let thumbnailElement = document.createElement("div");
    thumbnailElement.classList.add("drop-zone__thumb");
    
    let thumbnailLabel = document.createElement("div");
    thumbnailLabel.textContent = file.name;
    thumbnailElement.appendChild(thumbnailLabel);
    
    // Show thumbnail for image files
    if (file.type.startsWith("image/")) {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => {
            let img = document.createElement("img");
            img.src = reader.result;
            img.alt = file.name;
            thumbnailElement.appendChild(img);
        };
    }
    // Show icon for PDF files
    else if (file.type === "application/pdf") {
        let pdfIcon = document.createElement("i");
        pdfIcon.classList.add("fa", "fa-file-pdf-o");
        pdfIcon.style.fontSize = "48px";
        pdfIcon.style.color = "#dc3545";
        pdfIcon.style.marginTop = "30px";
        thumbnailElement.appendChild(pdfIcon);
    }
    // Show icon for Office documents
    else if (file.type.includes("word") || file.type.includes("document")) {
        let docIcon = document.createElement("i");
        docIcon.classList.add("fa", "fa-file-word-o");
        docIcon.style.fontSize = "48px";
        docIcon.style.color = "#2b579a";
        docIcon.style.marginTop = "30px";
        thumbnailElement.appendChild(docIcon);
    }
    else if (file.type.includes("excel") || file.type.includes("spreadsheet")) {
        let xlsIcon = document.createElement("i");
        xlsIcon.classList.add("fa", "fa-file-excel-o");
        xlsIcon.style.fontSize = "48px";
        xlsIcon.style.color = "#217346";
        xlsIcon.style.marginTop = "30px";
        thumbnailElement.appendChild(xlsIcon);
    }
    else if (file.type.includes("powerpoint") || file.type.includes("presentation")) {
        let pptIcon = document.createElement("i");
        pptIcon.classList.add("fa", "fa-file-powerpoint-o");
        pptIcon.style.fontSize = "48px";
        pptIcon.style.color = "#d24726";
        pptIcon.style.marginTop = "30px";
        thumbnailElement.appendChild(pptIcon);
    }
    
    addDeleteButton(thumbnailElement, file, dropZoneElement, inputElement);
    
    // Add to thumbnails container
    const thumbnailsContainer = dropZoneElement.querySelector("#appointmentThumbnails");
    if (thumbnailsContainer) {
        thumbnailsContainer.appendChild(thumbnailElement);
    } else {
        // Fallback to old method
        const uploadPrompt = dropZoneElement.querySelector(".drop-zone__prompt");
        dropZoneElement.insertBefore(thumbnailElement, uploadPrompt);
    }
}

/**
 * Add delete button to thumbnail
 */
function addDeleteButton(thumbnailElement, file, dropZoneElement, inputElement) {
    let deleteIcon = document.createElement("i");
    deleteIcon.classList.add("fa", "fa-close");
    
    let deleteButton = document.createElement("button");
    deleteButton.classList.add("delete-btn");
    deleteButton.appendChild(deleteIcon);
    
    thumbnailElement.appendChild(deleteButton);
    
    deleteButton.addEventListener('click', function(event) {
        event.preventDefault();
        event.stopPropagation();
        
        // Remove file from appointmentFiles array
        appointmentFiles = appointmentFiles.filter(f => f !== file);
        updateInputFiles(inputElement);
        thumbnailElement.remove();
        
        // Update the hidden field with file count
        $('#appointment_uploaded_files').val(appointmentFiles.length);
        
        // Show prompt if no more thumbnails
        const thumbnailsContainer = dropZoneElement.querySelector("#appointmentThumbnails");
        if (thumbnailsContainer && thumbnailsContainer.children.length === 0) {
            const prompt = dropZoneElement.querySelector(".drop-zone__prompt");
            if (prompt) {
                prompt.style.display = 'block';
            }
        }
    });
}

/**
 * Apply event listeners to dropzone elements
 */
function applyAppointmentEventListeners() {
    document.querySelectorAll("#appointment_files").forEach((inputElement) => {
        const dropZoneElement = inputElement.closest(".drop-zone");
        
        // Check if already initialized to prevent double event listeners
        if (dropZoneElement.dataset.dropzoneInitialized === 'true') {
            return;
        }
        
        // Mark as initialized
        dropZoneElement.dataset.dropzoneInitialized = 'true';
        
        // Click event to trigger file select (but not on thumbnails or delete buttons)
        dropZoneElement.addEventListener("click", (e) => {
            // Don't trigger file picker if clicking on thumbnail or delete button
            if (e.target.classList.contains('drop-zone__thumb') || 
                e.target.classList.contains('delete-btn') ||
                e.target.closest('.drop-zone__thumb') ||
                e.target.closest('.delete-btn')) {
                return;
            }
            // Trigger file picker for all other clicks in dropzone
            inputElement.click();
        });
        
        // File input change event
        inputElement.addEventListener("change", () => {
            const existingFileIdentifiers = appointmentFiles.map(getFileIdentifier);
            const newFiles = Array.from(inputElement.files).filter(
                file => !existingFileIdentifiers.includes(getFileIdentifier(file))
            );
            if (newFiles.length) {
                addNewFiles(newFiles, inputElement, dropZoneElement);
            }
        });
        
        // Drag events
        dropZoneElement.addEventListener("dragover", (e) => {
            e.preventDefault();
            dropZoneElement.classList.add("drop-zone--over");
        });
        
        ["dragleave", "dragend"].forEach((type) => {
            dropZoneElement.addEventListener(type, () => {
                dropZoneElement.classList.remove("drop-zone--over");
            });
        });
        
        // Drop event
        dropZoneElement.addEventListener("drop", (e) => {
            e.preventDefault();
            dropZoneElement.classList.remove("drop-zone--over");
            
            const droppedFiles = Array.from(e.dataTransfer.files);
            const existingFileIdentifiers = appointmentFiles.map(getFileIdentifier);
            const newFiles = droppedFiles.filter(
                file => !existingFileIdentifiers.includes(getFileIdentifier(file))
            );
            
            if (newFiles.length) {
                addNewFiles(newFiles, inputElement, dropZoneElement);
            }
        });
    });
}

/**
 * Clear all dropzone files and reset
 */
function clearAppointmentDropzone() {
    appointmentFiles = [];
    appointmentId = null;
    $('#appointment_uploaded_files').val('');
    $('#appointmentThumbnails').empty();
    $('.drop-zone__thumb').remove();
    $('.drop-zone__prompt').show();
}

// Initialize dropzone when document is ready
$(document).ready(function() {
    applyAppointmentEventListeners();
});

