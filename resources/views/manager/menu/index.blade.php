<x-manager-layout>
    <x-slot name="header">
        {{ config('salon.service_catalog_title') }}
    </x-slot>

    <div x-data="{ selectedCategory: 'all' }">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold text-white tracking-tight">{{ config('salon.service_catalog_title') }}</h2>
                <p class="text-sm font-medium text-white/40 uppercase tracking-wider">{{ config('salon.service_catalog_subtitle') }}</p>
            </div>
            <div class="flex gap-3">
                <button onclick="openCategoriesModal()" class="glass px-5 py-3 rounded-xl font-semibold text-white/70 hover:text-white hover:bg-white/10 transition-all flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z"/><path d="m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65"/><path d="m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65"/>
                    </svg>
                    Manage Categories
                </button>
                <button onclick="openAddMenuModal()" class="bg-gradient-to-r from-violet-600 to-cyan-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg hover:shadow-violet-500/25 transition-all flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14"/><path d="M12 5v14"/>
                    </svg>
                    {{ config('salon.add_service_item') }}
                </button>
            </div>
        </div>

        <!-- Categories Tabs -->
        <div class="flex gap-3 mb-8 overflow-x-auto pb-2 hide-scrollbar">
            <button @click="selectedCategory = 'all'" :class="selectedCategory === 'all' ? 'bg-gradient-to-r from-violet-600 to-cyan-600 text-white shadow-lg shadow-violet-500/20' : 'glass text-white/60 hover:text-white hover:bg-white/10'" class="px-5 py-2.5 rounded-xl font-semibold transition-all">{{ config('salon.all_service_items') }}</button>
            @foreach($categories as $category)
                <button @click="selectedCategory = {{ $category->id }}" :class="selectedCategory === {{ $category->id }} ? 'bg-gradient-to-r from-violet-600 to-cyan-600 text-white shadow-lg shadow-violet-500/20' : 'glass text-white/60 hover:text-white hover:bg-white/10'" class="px-5 py-2.5 rounded-xl font-semibold transition-all whitespace-nowrap">{{ $category->name }}</button>
            @endforeach
        </div>

        <!-- Menu Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($menuItems as $item)
                <div x-show="selectedCategory === 'all' || selectedCategory === {{ $item->category_id }}" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="glass-card rounded-2xl overflow-hidden card-hover group">
                <div class="relative h-44 bg-white/5 overflow-hidden">
                    @if($item->image)
                        <img src="{{ $item->imageUrl() }}" alt="{{ $item->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-white/20">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                            </svg>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4 gap-2">
                        <button onclick="openEditMenuModal({{ json_encode($item) }})" class="glass text-white p-2.5 rounded-lg hover:bg-violet-600 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/>
                            </svg>
                        </button>
                        <form action="{{ route('manager.menu.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="glass text-rose-400 p-2.5 rounded-lg hover:bg-rose-500 hover:text-white transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                    <div class="absolute top-3 right-3 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $item->is_available ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/20 text-rose-400 border border-rose-500/20' }}">
                        {{ $item->is_available ? 'Available' : 'Sold Out' }}
                    </div>
                </div>
                <div class="p-5">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="text-lg font-bold text-white">{{ $item->name }}</h4>
                        <span class="font-bold text-violet-400">Tsh {{ number_format($item->price) }}</span>
                    </div>
                    <p class="text-sm text-white/40 mb-4 line-clamp-2">{{ $item->description }}</p>
                    <div class="flex items-center justify-between pt-4 border-t border-white/5">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white/40">
                                <path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z"/><circle cx="7.5" cy="7.5" r=".5" fill="currentColor"/>
                            </svg>
                            <span class="text-[11px] font-medium text-white/40">{{ $item->category->name }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white/40">
                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                            </svg>
                            <span class="text-[11px] font-medium text-white/40">{{ $item->preparation_time ?? '15' }} min</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full glass-card py-16 text-center rounded-2xl">
                <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-white/5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white/20">
                        <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">No services or products found</h3>
                <p class="text-white/40">Start by adding your first service or product to the catalog.</p>
            </div>
        @endforelse

        <!-- Add New Card -->
        <button onclick="openAddMenuModal()" class="glass rounded-2xl border border-dashed border-white/20 flex flex-col items-center justify-center p-8 hover:border-violet-500 hover:bg-violet-500/10 transition-all group min-h-[340px]">
            <div class="w-14 h-14 bg-white/5 rounded-xl flex items-center justify-center group-hover:bg-violet-500/20 group-hover:scale-110 transition-all mb-4 border border-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white/40 group-hover:text-violet-400">
                    <path d="M5 12h14"/><path d="M12 5v14"/>
                </svg>
            </div>
            <span class="font-semibold text-white/40 group-hover:text-violet-400 uppercase tracking-wider text-sm">{{ config('salon.add_service_item') }}</span>
        </button>
    </div>

    <!-- Menu Modal -->
    <div id="menuModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-6">
        <div class="bg-surface-900 w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden border border-white/10 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 id="modalTitle" class="text-xl font-bold text-white tracking-tight">{{ config('salon.add_service_item') }}</h3>
                        <p class="text-sm font-medium text-white/40">Enter item details below</p>
                    </div>
                    <button onclick="closeMenuModal()" class="p-2 hover:bg-white/10 rounded-xl transition-all text-white/40 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                        </svg>
                    </button>
                </div>

                <form id="menuForm" action="{{ route('manager.menu.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">Item name</label>
                            <input type="text" name="name" id="menuName" required placeholder="e.g. Silk press, hair oil" 
                                   class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">Category</label>
                            <select name="category_id" id="menuCategoryId" required 
                                    class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">Price (Tsh)</label>
                            <input type="number" name="price" id="menuPrice" required placeholder="e.g. 15000" 
                                   class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">Duration (min)</label>
                            <input type="number" name="preparation_time" id="menuPrepTime" placeholder="e.g. 15" 
                                   class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">Description</label>
                            <textarea name="description" id="menuDescription" rows="4" placeholder="Describe this service or product…" 
                                      class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all resize-none"></textarea>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">{{ config('salon.service_menu_image') }}</label>
                            <div class="relative group">
                                <input type="file" name="image" id="menuImage" class="hidden" onchange="previewImage(this)">
                                <div onclick="document.getElementById('menuImage').click()" class="w-full h-28 bg-white/5 border border-dashed border-white/20 rounded-xl flex flex-col items-center justify-center cursor-pointer group-hover:border-violet-500 transition-all overflow-hidden">
                                    <img id="imagePreview" class="hidden w-full h-full object-cover">
                                    <div id="uploadPlaceholder" class="flex flex-col items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white/30 group-hover:text-violet-400">
                                            <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/><path d="M12 12v9"/><path d="m16 16-4-4-4 4"/>
                                        </svg>
                                        <span class="text-[10px] font-medium text-white/40 mt-2">Click to upload</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="availabilityToggle" class="hidden flex items-center gap-3 p-3 bg-white/5 rounded-xl">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_available" id="menuAvailable" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-white/20 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                            </label>
                            <span class="text-sm font-medium text-white/60">Available</span>
                        </div>
                    </div>

                    <div class="col-span-full mt-4">
                        <button type="submit" class="w-full bg-gradient-to-r from-violet-600 to-cyan-600 text-white py-3.5 rounded-xl font-semibold hover:shadow-lg hover:shadow-violet-500/25 transition-all">
                            Save service / product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Categories Modal -->
    <div id="categoriesModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-6">
        <div class="bg-surface-900 w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden border border-white/10 flex flex-col max-h-[90vh]">
            <div class="p-6 border-b border-white/5 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold text-white tracking-tight">Manage Categories</h3>
                    <p class="text-sm font-medium text-white/40">Add or edit catalog categories</p>
                </div>
                <button onclick="closeCategoriesModal()" class="p-2 hover:bg-white/10 rounded-xl transition-all text-white/40 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                    </svg>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto">
                <!-- Add Category Form -->
                <form action="{{ route('manager.categories.store') }}" method="POST" enctype="multipart/form-data" class="mb-6 glass p-5 rounded-xl">
                    @csrf
                    <h4 class="text-lg font-bold text-white mb-4">Add New Category</h4>
                    <div class="flex gap-3 items-start">
                        <div class="flex-1 space-y-3">
                            <input type="text" name="name" required placeholder="Category Name" 
                                   class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                            <input type="file" name="image" class="w-full text-sm text-white/60 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-600 file:text-white hover:file:bg-violet-500 transition-all">
                        </div>
                        <button type="submit" class="bg-gradient-to-r from-violet-600 to-cyan-600 text-white p-3 rounded-xl shadow-lg hover:shadow-violet-500/25 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14"/><path d="M12 5v14"/>
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Categories List -->
                <div class="space-y-3">
                    @foreach($categories as $category)
                        <div class="flex items-center justify-between p-4 glass rounded-xl hover:bg-white/5 transition-all group">
                            <div class="flex items-center gap-4">
                                @if($category->image)
                                    <img src="{{ $category->imageUrl() }}" class="w-12 h-12 rounded-xl object-cover">
                                @else
                                    <div class="w-12 h-12 bg-white/5 rounded-xl flex items-center justify-center border border-white/10">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white/40">
                                            <path d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z"/><path d="m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65"/><path d="m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65"/>
                                        </svg>
                                    </div>
                                @endif
                                <span class="font-semibold text-white text-lg">{{ $category->name }}</span>
                            </div>
                            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button onclick="editCategory({{ json_encode($category) }})" class="p-2 text-white/60 hover:text-violet-400 hover:bg-white/5 rounded-lg transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/>
                                    </svg>
                                </button>
                                <form action="{{ route('manager.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Delete this category? This will fail if it has items.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-rose-400 hover:bg-rose-500/10 rounded-lg transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div id="editCategoryModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[110] hidden flex items-center justify-center p-6">
        <div class="bg-surface-900 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden border border-white/10">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white">Edit Category</h3>
                    <button onclick="closeEditCategoryModal()" class="p-2 hover:bg-white/10 rounded-xl transition-all text-white/40 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                        </svg>
                    </button>
                </div>
                
                <form id="editCategoryForm" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">Category Name</label>
                        <input type="text" name="name" id="editCategoryName" required 
                               class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                    </div>
                    
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">New Image (Optional)</label>
                        <input type="file" name="image" class="w-full text-sm text-white/60 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-600 file:text-white hover:file:bg-violet-500 transition-all">
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-violet-600 to-cyan-600 text-white py-3.5 rounded-xl font-semibold hover:shadow-lg hover:shadow-violet-500/25 transition-all">
                        Update Category
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAddMenuModal() {
            document.getElementById('modalTitle').innerText = @json(config('salon.add_service_item'));
            document.getElementById('menuForm').action = '{{ route("manager.menu.store") }}';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('menuName').value = '';
            document.getElementById('menuDescription').value = '';
            document.getElementById('menuPrice').value = '';
            document.getElementById('menuPrepTime').value = '';
            document.getElementById('imagePreview').classList.add('hidden');
            document.getElementById('uploadPlaceholder').classList.remove('hidden');
            document.getElementById('availabilityToggle').classList.add('hidden');
            
            document.getElementById('menuModal').classList.remove('hidden');
            document.getElementById('menuModal').classList.add('flex');
        }

        function openEditMenuModal(item) {
            document.getElementById('modalTitle').innerText = @json(config('salon.edit_service_item'));
            document.getElementById('menuForm').action = `/manager/menu/${item.id}`;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('menuName').value = item.name;
            document.getElementById('menuDescription').value = item.description || '';
            document.getElementById('menuPrice').value = item.price;
            document.getElementById('menuPrepTime').value = item.preparation_time || '';
            document.getElementById('menuCategoryId').value = item.category_id;
            document.getElementById('menuAvailable').checked = item.is_available;
            document.getElementById('availabilityToggle').classList.remove('hidden');

            if (item.image) {
                document.getElementById('imagePreview').src = `/serve-storage/${item.image}`;
                document.getElementById('imagePreview').classList.remove('hidden');
                document.getElementById('uploadPlaceholder').classList.add('hidden');
            } else {
                document.getElementById('imagePreview').classList.add('hidden');
                document.getElementById('uploadPlaceholder').classList.remove('hidden');
            }
            
            document.getElementById('menuModal').classList.remove('hidden');
            document.getElementById('menuModal').classList.add('flex');
        }

        function closeMenuModal() {
            document.getElementById('menuModal').classList.add('hidden');
            document.getElementById('menuModal').classList.remove('flex');
        }

        function openCategoriesModal() {
            document.getElementById('categoriesModal').classList.remove('hidden');
            document.getElementById('categoriesModal').classList.add('flex');
        }

        function closeCategoriesModal() {
            document.getElementById('categoriesModal').classList.add('hidden');
            document.getElementById('categoriesModal').classList.remove('flex');
        }

        function editCategory(category) {
            document.getElementById('editCategoryForm').action = `/manager/categories/${category.id}`;
            document.getElementById('editCategoryName').value = category.name;
            
            document.getElementById('editCategoryModal').classList.remove('hidden');
            document.getElementById('editCategoryModal').classList.add('flex');
        }

        function closeEditCategoryModal() {
            document.getElementById('editCategoryModal').classList.add('hidden');
            document.getElementById('editCategoryModal').classList.remove('flex');
        }

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                    document.getElementById('imagePreview').classList.remove('hidden');
                    document.getElementById('uploadPlaceholder').classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    </div>
</x-manager-layout>
