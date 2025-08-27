<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<script>
// Ensure jQuery is loaded before any CSRF setup
if (typeof jQuery === 'undefined') {
    document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"><\/script>');
}

// Override the problematic CSRF function to prevent errors
window.csrf_jquery_ajax_setup = function() {
    // Do nothing - prevent the error from general_helper.php
    return false;
};
</script>

<?php init_head(); ?>

<!-- Include module CSS -->
<link rel="stylesheet" href="<?php echo base_url('modules/ella_contractors/assets/css/ella_contractors.css'); ?>">

<div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        
                        <!-- Page Header -->
                            <div class="row">
                                <div class="col-md-8">
                            <h4 class="customer-profile-group-heading"><?= $title ?></h4>
                                        <?php if ($contract_id): ?>
                            <p class="text-muted">Media files for this specific contract</p>
                                        <?php else: ?>
                            <p class="text-muted">Default media files available for all contracts</p>
                                        <?php endif; ?>
                                </div>
                                <div class="col-md-4 text-right">
                                        <?php if ($contract_id): ?>
                            <a href="<?= admin_url('ella_contractors/view_contract/' . $contract_id) ?>" class="btn btn-default">
                                            <i class="fa fa-arrow-left"></i> Back to Contract
                                        </a>
                            <a href="<?= admin_url('ella_contractors/upload_media/' . $contract_id) ?>" class="btn btn-info">
                                            <i class="fa fa-upload"></i> Upload Media
                                        </a>
                                        <?php else: ?>
                            <a href="<?= admin_url('ella_contractors/contracts') ?>" class="btn btn-default">
                                            <i class="fa fa-arrow-left"></i> Back to Contracts
                                        </a>
                            <a href="<?= admin_url('ella_contractors/upload_media') ?>" class="btn btn-info">
                                            <i class="fa fa-upload"></i> Upload Default Media
                                        </a>
                                        <?php endif; ?>
                            
                            <!-- Update Table Structure Button (for existing installations) -->
                            <a href="<?= admin_url('ella_contractors/update_media_table') ?>" class="btn btn-warning" title="Update table structure if you encounter database errors">
                                <i class="fa fa-database"></i> Update Table Structure
                            </a>
                        </div>
                    </div>
                    <hr class="hr-panel-heading" />

                    <!-- Media Filters -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <i class="fa fa-filter"></i> Filter & Search Media Files
                                    </h4>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="category-filter">Filter by Category:</label>
                                            <select id="category-filter" class="form-control">
                                                <option value="">All Categories</option>
                                                <option value="documents">Documents</option>
                                                <option value="images">Images</option>
                                                <option value="presentations">Presentations</option>
                                                <option value="contracts">Contract Files</option>
                                                <option value="invoices">Invoices & Financial</option>
                                                <option value="blueprints">Blueprints & Plans</option>
                                                <option value="videos">Videos & Animations</option>
                                                <option value="audio">Audio Files</option>
                                                <option value="archives">Archives</option>
                                                <option value="other">Other Files</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="search-filter">Search Files:</label>
                                            <input type="text" id="search-filter" class="form-control" placeholder="Search by filename, description, or tags...">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="sort-filter">Sort By:</label>
                                            <select id="sort-filter" class="form-control">
                                                <option value="date-desc">Date (Newest First)</option>
                                                <option value="date-asc">Date (Oldest First)</option>
                                                <option value="name-asc">Name (A-Z)</option>
                                                <option value="name-desc">Name (Z-A)</option>
                                                <option value="size-desc">Size (Largest First)</option>
                                                <option value="size-asc">Size (Smallest First)</option>
                                            </select>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Media Gallery Content -->
                        <div class="row">
                            <div class="col-md-12">
                                <?php if (!empty($media_files)): ?>
                                                        <!-- Media Grid -->
                            <div class="row">
                                    <?php foreach ($media_files as $media): ?>
                                <div class="col-md-4 col-sm-6 media-item" data-category="<?= $media->media_category ?>" data-tags="<?= $media->tags ?>" data-name="<?= strtolower($media->original_name) ?>" data-size="<?= $media->file_size ?>" data-date="<?= $media->date_uploaded ?>">
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <?php 
                                            // Check both file extension and MIME type for images
                                            $file_extension = strtolower(pathinfo($media->original_name, PATHINFO_EXTENSION));
                                            $is_image_extension = in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
                                            
                                            // Check if the file_type field contains image MIME type
                                            $is_image_mime = stripos($media->file_type, 'image') !== false;
                                            
                                            // Check if the file_type field contains image extension
                                            $is_image_file_type = in_array(strtolower($media->file_type), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
                                            
                                            $is_image = $is_image_extension || $is_image_mime || $is_image_file_type;
                                            
                                            // Debug info (remove this after testing)
                                            if ($media->original_name == '184517cf3194ee4985a3f97f4a27c1b4.jpg' || $media->original_name == 'acrylx-shower-base-with-molded-seat-33"-w-x-60"l-8-4.jpg') {
                                                echo "<!-- DEBUG: File: {$media->original_name}, Extension: {$file_extension}, File Type: {$media->file_type}, Is Image: " . ($is_image ? 'YES' : 'NO') . " -->";
                                            }
                                            
                                            if ($is_image): 
                                            ?>
                                            <!-- Image Preview -->
                                            <div class="media-image-preview">
                                                <img src="<?= get_contract_media_url($media->contract_id) . $media->file_name ?>" 
                                                     alt="<?= $media->original_name ?>"
                                                     class="preview-image"
                                                     onclick="openImageLightbox(this.src, '<?= addslashes($media->original_name) ?>')"
                                                     style="cursor: pointer;"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="image-fallback" style="display: none;">
                                                    <i class="fa fa-image"></i>
                                                </div>
                                                <div class="image-overlay">
                                                    <i class="fa fa-expand"></i>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <div class="media-item-header">
                                                <div class="media-item-icon">
                                                    <i class="fa <?= get_file_icon($media->file_type) ?>"></i>
                                                    </div>
                                                <div class="media-item-info">
                                                    <h5 class="media-title"><?= character_limiter($media->original_name, 30) ?></h5>
                                                    <div class="media-item-meta">
                                                        <?= formatBytes($media->file_size) ?> • <?= strtoupper($media->file_type) ?>
                                                    </div>
                                                    </div>
                                                </div>
                                                
                                            <div class="media-item-details">
                                                <?php if (!empty($media->description)): ?>
                                                <div class="media-item-description">
                                                    <?= character_limiter($media->description, 60) ?>
                                                </div>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($media->media_category)): ?>
                                                <div class="media-category-badge">
                                                    <?= ucfirst($media->media_category) ?>
                                                </div>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($media->tags)): ?>
                                                <div class="media-tags">
                                                    <?php 
                                                    $tags = explode(',', $media->tags);
                                                    $display_tags = array_slice($tags, 0, 3);
                                                    foreach ($display_tags as $tag): ?>
                                                    <span class="media-tag"><?= trim($tag) ?></span>
                                                    <?php endforeach; ?>
                                                    <?php if (count($tags) > 3): ?>
                                                    <span class="media-tag media-tag-more">+<?= count($tags) - 3 ?></span>
                                                    <?php endif; ?>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                                    <div class="media-btn-group">
                                                        <a href="<?= get_contract_media_url($media->contract_id) . $media->file_name ?>" 
                                                   target="_blank" class="btn btn-xs btn-info" title="View File">
                                                    <i class="fa fa-eye"></i> View
                                                        </a>
                                                        <a href="<?= get_contract_media_url($media->contract_id) . $media->file_name ?>" 
                                                   download class="btn btn-xs btn-success" title="Download File">
                                                    <i class="fa fa-download"></i> Download
                                                        </a>
                                                        <a href="javascript:void(0)" 
                                                           onclick="confirmDeleteMedia(<?= $media->id ?>, '<?= addslashes($media->original_name) ?>', '<?= urlencode(current_url()) ?>')" 
                                                   class="btn btn-xs btn-danger" title="Delete File">
                                                    <i class="fa fa-trash"></i> Delete
                                                        </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                </div>
                                
                                <!-- Summary -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i>
                                        <strong>Summary:</strong> 
                                        <?= count($media_files) ?> media file(s) found. 
                                        Total size: <strong><?= formatBytes(array_sum(array_column($media_files, 'file_size'))) ?></strong>
                                        <?php if ($contract_id): ?>
                                        for this contract.
                                        <?php else: ?>
                                        in default gallery.
                                        <?php endif; ?>
                                    </div>
                                    </div>
                                </div>
                                
                                <?php else: ?>
                                <!-- Empty State -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="text-center" style="padding: 60px 20px;">
                                    <div class="media-empty-icon">
                                            <i class="fa fa-folder-open fa-4x text-muted"></i>
                                    </div>
                                        <h3 class="text-muted">No Media Files Found</h3>
                                    <?php if ($contract_id): ?>
                                        <p class="text-muted">No media files have been uploaded for this contract yet.</p>
                                    <a href="<?= admin_url('ella_contractors/upload_media/' . $contract_id) ?>" class="btn btn-primary btn-lg">
                                        <i class="fa fa-upload"></i> Upload First Media File
                                    </a>
                                    <?php else: ?>
                                        <p class="text-muted">No default media files have been uploaded yet.</p>
                                    <a href="<?= admin_url('ella_contractors/upload_media') ?>" class="btn btn-primary btn-lg">
                                        <i class="fa fa-upload"></i> Upload Default Media
                                    </a>
                                    <?php endif; ?>
                                </div>
                                </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Image Lightbox -->
<div class="image-lightbox" id="imageLightbox">
    <div class="lightbox-content">
        <button class="lightbox-close" onclick="closeImageLightbox()">
            <i class="fa fa-times"></i>
        </button>
        <img src="" alt="" class="lightbox-image" id="lightboxImage">
        <div class="lightbox-title" id="lightboxTitle"></div>
    </div>
</div>

<?php init_tail(); ?>

<script>
// Initialize media filters and search functionality
$(document).ready(function() {
    initializeMediaFilters();
});

// Initialize media filters
function initializeMediaFilters() {
    const categoryFilter = $('#category-filter');
    const searchFilter = $('#search-filter');
    const sortFilter = $('#sort-filter');
    
    // Filter media items based on selected criteria
    function filterMedia() {
        const selectedCategory = categoryFilter.val();
        const searchTerm = searchFilter.val().toLowerCase();
        const sortBy = sortFilter.val();
        
        let visibleItems = 0;
        
        $('.media-item').each(function() {
            const $item = $(this);
            const category = $item.data('category');
            const tags = $item.data('tags');
            const name = $item.data('name');
            const size = $item.data('size');
            const date = $item.data('date');
            
            let showItem = true;
            
            // Category filter
            if (selectedCategory && category !== selectedCategory) {
                showItem = false;
            }
            
            // Search filter
            if (searchTerm) {
                const searchableText = name + ' ' + (tags || '');
                if (!searchableText.toLowerCase().includes(searchTerm)) {
                    showItem = false;
                }
            }
            
            // Show/hide item
            if (showItem) {
                $item.show();
                visibleItems++;
            } else {
                $item.hide();
            }
        });
        
        // Sort items
        sortMediaItems(sortBy);
        
        // Update summary
        updateMediaSummary(visibleItems);
    }
    
    // Sort media items
    function sortMediaItems(sortBy) {
        const $container = $('.media-item').parent();
        const $items = $container.find('.media-item:visible').get();
        
        $items.sort(function(a, b) {
            const $a = $(a);
            const $b = $(b);
            
            switch (sortBy) {
                case 'date-desc':
                    return new Date($b.data('date')) - new Date($a.data('date'));
                case 'date-asc':
                    return new Date($a.data('date')) - new Date($b.data('date'));
                case 'name-asc':
                    return $a.data('name').localeCompare($b.data('name'));
                case 'name-desc':
                    return $b.data('name').localeCompare($a.data('name'));
                case 'size-desc':
                    return $b.data('size') - $a.data('size');
                case 'size-asc':
                    return $a.data('size') - $b.data('size');
                default:
                    return 0;
            }
        });
        
        $container.append($items);
    }
    
    // Update media summary
    function updateMediaSummary(visibleCount) {
        const totalCount = $('.media-item').length;
        const summaryText = `${visibleCount} of ${totalCount} media file(s) found.`;
        
        $('.media-summary .alert strong').text(summaryText);
    }
    
    // Bind filter events
    categoryFilter.on('change', filterMedia);
    searchFilter.on('input', filterMedia);
    sortFilter.on('change', filterMedia);
    
    // Initial filter
    filterMedia();
}

// Function to confirm media deletion with SweetAlert
function confirmDeleteMedia(mediaId, fileName, redirectUrl) {
    if (confirm('Are you sure you want to delete "' + fileName + '"? This action cannot be undone.')) {
        // Show loading state
        $('body').append('<div class="loading-overlay"><i class="fa fa-spinner fa-spin"></i> Deleting...</div>');
        
        // Make AJAX request to delete
        $.ajax({
            url: '<?= admin_url('ella_contractors/delete_media/') ?>' + mediaId,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                $('.loading-overlay').remove();
                if (response.success) {
                    alert('Media file deleted successfully!');
                    window.location.href = redirectUrl;
                } else {
                    alert('Error: ' + (response.message || 'An error occurred while deleting the file.'));
                }
            },
            error: function() {
                $('.loading-overlay').remove();
                alert('An error occurred while deleting the file.');
            }
        });
    }
}

// Image Lightbox Functions
function openImageLightbox(imageSrc, imageTitle) {
    const lightbox = document.getElementById('imageLightbox');
    const lightboxImage = document.getElementById('lightboxImage');
    const lightboxTitle = document.getElementById('lightboxTitle');
    
    lightboxImage.src = imageSrc;
    lightboxImage.alt = imageTitle;
    lightboxTitle.textContent = imageTitle;
    
    lightbox.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeImageLightbox() {
    const lightbox = document.getElementById('imageLightbox');
    lightbox.classList.remove('show');
    document.body.style.overflow = 'auto';
}

// Close lightbox when clicking outside the image
document.addEventListener('DOMContentLoaded', function() {
    const lightbox = document.getElementById('imageLightbox');
    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) {
            closeImageLightbox();
        }
    });
    
    // Close lightbox with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImageLightbox();
        }
    });
});
</script>
