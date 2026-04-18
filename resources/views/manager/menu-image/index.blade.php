<x-manager-layout>
    <x-slot name="header">
        {{ config('salon.service_menu_image') }}
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="glass-card rounded-2xl p-8 mb-8">
            <div class="flex items-start gap-6">
                <div class="w-16 h-16 bg-gradient-to-br from-violet-600 to-cyan-600 rounded-2xl flex items-center justify-center shadow-lg shadow-violet-500/30">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                        <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                        <circle cx="9" cy="9" r="2"/>
                        <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-white mb-2">{{ config('salon.entity') }} service menu image</h2>
                    <p class="text-white/60 text-sm leading-relaxed">
                        Upload one clear image of your price list / treatments &amp; retail. Clients see it on WhatsApp when they ask for your service list.
                    </p>
                </div>
            </div>
        </div>

        <!-- Current Image Preview -->
        @if($restaurant && $restaurant->menu_image)
            <div class="glass-card rounded-2xl p-6 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse"></div>
                        <h3 class="text-lg font-bold text-white">Current image</h3>
                    </div>
                    <span class="px-3 py-1.5 bg-emerald-500/10 text-emerald-400 text-xs font-bold rounded-full uppercase tracking-wider border border-emerald-500/20">
                        Active
                    </span>
                </div>
                
                <div class="relative group">
                    <!-- Image Container with Zoom Effect -->
                    <div class="relative overflow-hidden rounded-xl border border-white/10">
                        <img 
                            src="{{ $restaurant->menuImageUrl() }}" 
                            alt="Service menu image" 
                            class="w-full h-auto max-h-[600px] object-contain bg-surface-900 transition-transform duration-500 group-hover:scale-105"
                        >
                        <!-- Overlay on hover -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    
                    <!-- Image Info -->
                    <div class="mt-4 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <a href="{{ $restaurant->menuImageUrl() }}" target="_blank" class="flex items-center gap-2 px-4 py-2 glass rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-all text-sm font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                    <polyline points="15 3 21 3 21 9"/>
                                    <line x1="10" x2="21" y1="14" y2="3"/>
                                </svg>
                                View Full Size
                            </a>
                        </div>
                        
                        <!-- Delete Button -->
                        <form action="{{ route('manager.menu-image.destroy') }}" method="POST" onsubmit="return confirm('Delete this service menu image?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-red-500/10 text-red-400 rounded-lg hover:bg-red-500/20 transition-all text-sm font-medium border border-red-500/20">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 6h18"/>
                                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                    <line x1="10" x2="10" y1="11" y2="17"/>
                                    <line x1="14" x2="14" y1="11" y2="17"/>
                                </svg>
                                Delete Image
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Upload Form -->
        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-3 h-3 bg-violet-500 rounded-full"></div>
                <h3 class="text-lg font-bold text-white">
                    {{ $restaurant && $restaurant->menu_image ? 'Replace image' : 'Upload image' }}
                </h3>
            </div>

            <form action="{{ route('manager.menu-image.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                
                <!-- Drop Zone -->
                <div 
                    id="dropZone"
                    class="relative border-2 border-dashed border-white/10 hover:border-violet-500/50 rounded-xl p-12 text-center transition-all duration-300 cursor-pointer group"
                    onclick="document.getElementById('menu_image').click();"
                >
                    <!-- Preview Container (hidden by default) -->
                    <div id="previewContainer" class="hidden">
                        <img id="imagePreview" src="" alt="Preview" class="max-h-80 mx-auto rounded-lg mb-4">
                        <p id="fileName" class="text-white font-medium mb-2"></p>
                        <p id="fileSize" class="text-white/40 text-sm"></p>
                    </div>

                    <!-- Default Upload UI -->
                    <div id="uploadUI" class="space-y-4">
                        <div class="w-20 h-20 mx-auto bg-gradient-to-br from-violet-500/10 to-cyan-500/10 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-violet-400">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/>
                                <line x1="12" x2="12" y1="3" y2="15"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-white font-semibold text-lg mb-2">
                                Drop your {{ strtolower(config('salon.service_menu_image')) }} here
                            </p>
                            <p class="text-white/40 text-sm">
                                or click to browse files
                            </p>
                        </div>
                        <div class="flex flex-wrap justify-center gap-2 text-xs text-white/30">
                            <span class="px-2 py-1 bg-white/5 rounded">JPG</span>
                            <span class="px-2 py-1 bg-white/5 rounded">PNG</span>
                            <span class="px-2 py-1 bg-white/5 rounded">GIF</span>
                            <span class="px-2 py-1 bg-white/5 rounded">WEBP</span>
                            <span class="px-2 py-1 bg-white/5 rounded">Max 5MB</span>
                        </div>
                    </div>

                    <input 
                        type="file" 
                        name="menu_image" 
                        id="menu_image" 
                        accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                        class="hidden"
                        required
                    >
                </div>

                @error('menu_image')
                    <p class="text-red-400 text-sm mt-3 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" x2="12" y1="8" y2="12"/>
                            <line x1="12" x2="12.01" y1="16" y2="16"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror

                <!-- Submit Button -->
                <div class="mt-6 flex justify-end">
                    <button 
                        type="submit" 
                        id="submitBtn"
                        class="px-8 py-3 bg-gradient-to-r from-violet-600 to-cyan-600 text-white font-semibold rounded-xl shadow-lg shadow-violet-500/25 hover:shadow-violet-500/40 transition-all flex items-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" x2="12" y1="3" y2="15"/>
                        </svg>
                        <span id="btnText">Upload image</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- API Info -->
        <div class="glass-card rounded-2xl p-6 mt-8">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-gradient-to-br from-cyan-500/20 to-blue-500/20 rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400">
                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-white font-semibold">Bot API Endpoint</h4>
                    <p class="text-white/40 text-xs">Use this endpoint to fetch the service menu image in your bot</p>
                </div>
            </div>
            <div class="bg-surface-900 rounded-xl p-4 font-mono text-sm">
                <code class="text-cyan-400">GET /api/bot/restaurant/{restaurant_id}/menu-image</code>
            </div>
        </div>
    </div>

    <script>
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('menu_image');
        const previewContainer = document.getElementById('previewContainer');
        const imagePreview = document.getElementById('imagePreview');
        const uploadUI = document.getElementById('uploadUI');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const submitBtn = document.getElementById('submitBtn');

        // Drag and drop events
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.add('border-violet-500', 'bg-violet-500/5');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.remove('border-violet-500', 'bg-violet-500/5');
            }, false);
        });

        dropZone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length) {
                fileInput.files = files;
                handleFile(files[0]);
            }
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length) {
                handleFile(e.target.files[0]);
            }
        });

        function handleFile(file) {
            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                alert('Please select a valid image file (JPG, PNG, GIF, or WEBP)');
                return;
            }

            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB');
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.src = e.target.result;
                uploadUI.classList.add('hidden');
                previewContainer.classList.remove('hidden');
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                submitBtn.disabled = false;
            };
            reader.readAsDataURL(file);
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Form submit loading state
        document.getElementById('uploadForm').addEventListener('submit', function() {
            submitBtn.disabled = true;
            document.getElementById('btnText').textContent = 'Uploading...';
        });
    </script>
</x-manager-layout>
