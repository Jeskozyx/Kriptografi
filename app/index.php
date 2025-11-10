<?php
session_start();
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Kriptografi & Steganografi | Rail Fence + XOR & AES + Blowfish & IDEA + LSB + Salsa20</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .file-upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            background: #f9fafb;
        }
        .file-upload-area:hover {
            border-color: #3b82f6;
            background: #f0f9ff;
        }
        .file-upload-area.dragover {
            border-color: #3b82f6;
            background: #dbeafe;
        }
        .dark .file-upload-area {
            background: #374151;
            border-color: #4b5563;
        }
        .dark .file-upload-area:hover {
            border-color: #60a5fa;
            background: #1e3a8a;
        }
        .tab-active-indicator {
            position: relative;
        }
        .tab-active-indicator::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 3px;
            background: currentColor;
            border-radius: 3px 3px 0 0;
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-200 min-h-screen">
    <header class="bg-white dark:bg-gray-800 shadow-md">
        <div class="container mx-auto max-w-7xl p-4 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-blue-600 dark:text-blue-500">Sistem Kriptografi & Steganografi</h1>
                <p class="text-gray-600 dark:text-gray-400">Rail Fence + XOR & AES + Blowfish & IDEA + LSB + Salsa20</p>
            </div>
            <?php if ($isLoggedIn): ?>
                <div class="flex items-center gap-3">
                    <?php if (!empty($username)): ?>
                        <span class="text-sm text-gray-600 dark:text-gray-300">Halo, <?php echo htmlspecialchars($username); ?></span>
                    <?php endif; ?>
                    <a href="../app/logout.php" class="px-4 py-2 text-white bg-red-600 hover:bg-red-700 rounded-md text-sm transition-colors">Logout</a>
                </div>
            <?php else: ?>
                <div class="flex items-center gap-3">
                    <a href="../app/login.php" class="px-4 py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-md text-sm transition-colors">Login</a>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <main class="container mx-auto max-w-7xl p-4 md:p-6">
        <div x-data="{ 
            tab: 'teks',
            salsaMode: 'encrypt',
            isDragging: false
        }" class="w-full bg-white rounded-lg shadow-xl dark:bg-gray-800 overflow-hidden">
            
            <!-- Tab Navigation -->
            <div class="flex border-b border-gray-200 dark:border-gray-700 overflow-x-auto bg-gray-50 dark:bg-gray-900">
                <button @click="tab = 'teks'" 
                        :class="{ 
                            'border-blue-500 text-blue-600 dark:text-blue-400 tab-active-indicator': tab === 'teks', 
                            'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300': tab !== 'teks' 
                        }"
                        class="flex-shrink-0 py-4 px-6 text-center font-medium border-b-2 focus:outline-none transition-colors duration-200 flex items-center gap-2">
                    <span>🔐</span>
                    <span>Enkripsi Teks</span>
                </button>
                <button @click="tab = 'file'" 
                        :class="{ 
                            'border-blue-500 text-blue-600 dark:text-blue-400 tab-active-indicator': tab === 'file', 
                            'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300': tab !== 'file' 
                        }"
                        class="flex-shrink-0 py-4 px-6 text-center font-medium border-b-2 focus:outline-none transition-colors duration-200 flex items-center gap-2">
                    <span>💾</span>
                    <span>Enkripsi File</span>
                </button>
                <button @click="tab = 'dekripsi'" 
                        :class="{ 
                            'border-blue-500 text-blue-600 dark:text-blue-400 tab-active-indicator': tab === 'dekripsi', 
                            'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300': tab !== 'dekripsi' 
                        }"
                        class="flex-shrink-0 py-4 px-6 text-center font-medium border-b-2 focus:outline-none transition-colors duration-200 flex items-center gap-2">
                    <span>🧩</span>
                    <span>Dekripsi File</span>
                </button>
                <button @click="tab = 'salsa20'" 
                        :class="{ 
                            'border-blue-500 text-blue-600 dark:text-blue-400 tab-active-indicator': tab === 'salsa20', 
                            'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300': tab !== 'salsa20' 
                        }"
                        class="flex-shrink-0 py-4 px-6 text-center font-medium border-b-2 focus:outline-none transition-colors duration-200 flex items-center gap-2">
                    <span>🔄</span>
                    <span>Database</span>
                </button>
                <button @click="tab = 'stegano'" 
                        :class="{ 
                            'border-blue-500 text-blue-600 dark:text-blue-400 tab-active-indicator': tab === 'stegano', 
                            'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300': tab !== 'stegano' 
                        }"
                        class="flex-shrink-0 py-4 px-6 text-center font-medium border-b-2 focus:outline-none transition-colors duration-200 flex items-center gap-2">
                    <span>🖼️</span>
                    <span>Steganografi</span>
                </button>
            </div>

            <div class="p-6">
                <!-- 🔐 Enkripsi Teks -->
                <div x-show="tab === 'teks'" x-cloak>
                    <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-100">🔐 Enkripsi Teks Super</h2>
                    <form action="../enkripsi/teks_enkripsi.php" method="POST" class="space-y-6">
                        <div>
                            <label for="judul_teks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Judul Teks</label>
                            <input type="text" id="judul_teks" name="judul" placeholder="Masukkan judul teks..." required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors">
                        </div>
                        <div>
                            <label for="teks_rahasia" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Teks Rahasia</label>
                            <textarea id="teks_rahasia" name="teks" rows="6" placeholder="Masukkan teks rahasia..." required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors resize-vertical"></textarea>
                        </div>
                        <button type="submit" 
                                class="w-full px-5 py-4 text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-colors font-semibold text-lg">
                            🚀 Proses Enkripsi
                        </button>
                    </form>
                    
                    <div class="mt-6 text-center">
                        <a href="../enkripsi/teks_dekripsi.php" class="inline-flex items-center text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium text-lg">
                            🔓 Buka Halaman Dekripsi Teks
                        </a>
                    </div>
                </div>

                <!-- 💾 Enkripsi File -->
                <div x-show="tab === 'file'" x-cloak>
                    <h3 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-100">💾 Enkripsi File (AES + Blowfish)</h3>
                    <form action="../enkripsi/file_enkripsi.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                        <div>
                            <label for="judul_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Judul File</label>
                            <input type="text" id="judul_file" name="judul" placeholder="Masukkan judul file..." required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors">
                        </div>
                        <div>
                            <label for="kunci_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kunci Manual</label>
                            <input type="text" id="kunci_file" name="kunci" placeholder="Masukkan kunci manual" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors">
                        </div>
                        <div>
                            <label for="file_upload" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">File</label>
                            <div class="file-upload-area" 
                                 @dragover="isDragging = true" 
                                 @dragleave="isDragging = false"
                                 @drop="isDragging = false"
                                 :class="{ 'dragover': isDragging }">
                                <input id="file_upload" name="file" type="file" required
                                       class="hidden"
                                       @change="handleFileSelect">
                                <label for="file_upload" class="cursor-pointer">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-600 dark:text-gray-300">Klik untuk upload atau drag & drop</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">File apa saja (MAX. 10MB)</p>
                                </label>
                            </div>
                        </div>
                        <button type="submit" 
                                class="w-full px-5 py-4 text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-colors font-semibold text-lg">
                            🔒 Enkripsi File
                        </button>
                    </form>
                </div>

                <!-- 🧩 Dekripsi File -->
                <div x-show="tab === 'dekripsi'" x-cloak>
                    <h3 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-100">🧩 Dekripsi File (AES + Blowfish)</h3>
                    <div x-data="{ mode: 'judul' }" class="space-y-6">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Pilih Metode Dekripsi:</p>
                            <div class="flex flex-wrap gap-4">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="mode_dekripsi" x-model="mode" value="judul" class="text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Berdasarkan Judul</span>
                                </label>
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="mode_dekripsi" x-model="mode" value="file" class="text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Upload File Terenkripsi</span>
                                </label>
                            </div>
                        </div>

                        <form action="../enkripsi/file_dekripsi.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                            <template x-if="mode === 'judul'">
                                <div>
                                    <label for="id_judul" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih File Terenkripsi</label>
                                    <select id="id_judul" name="id_judul"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors">
                                        <?php
                                        include '../config/config.php';
                                        $result = $conn->query("SELECT id, judul, path_enkripsi FROM file_rahasia ORDER BY id DESC");
                                        if ($result && $result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                $judul_tampil = '';
                                                if (!empty($row['judul'])) {
                                                    $judul_tampil = $row['judul'];
                                                } elseif (!empty($row['path_enkripsi'])) {
                                                    $judul_tampil = basename($row['path_enkripsi']);
                                                } else {
                                                    $judul_tampil = 'Tanpa Judul';
                                                }
                                                $label = $row['id'] . ' - ' . $judul_tampil;
                                                echo "<option value='{$row['id']}'>" . htmlspecialchars($label) . "</option>";
                                            }
                                        } else {
                                            echo "<option disabled>Tidak ada file terenkripsi</option>";
                                        }
                                        $conn->close();
                                        ?>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-2 dark:text-gray-400">Pilih file berdasarkan ID dan judul dari database.</p>
                                </div>
                            </template>

                            <template x-if="mode === 'file'">
                                <div>
                                    <label for="file_terenkripsi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Upload File Terenkripsi</label>
                                    <div class="file-upload-area" 
                                         @dragover="isDragging = true" 
                                         @dragleave="isDragging = false"
                                         @drop="isDragging = false"
                                         :class="{ 'dragover': isDragging }">
                                        <input id="file_terenkripsi" name="file_terenkripsi" type="file" accept=".enc" 
                                               class="hidden"
                                               @change="handleFileSelect">
                                        <label for="file_terenkripsi" class="cursor-pointer">
                                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                            </svg>
                                            <p class="text-lg font-medium text-gray-600 dark:text-gray-300">Klik untuk upload file terenkripsi</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">File dengan format .enc</p>
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2 dark:text-gray-400">Upload file hasil enkripsi (mis. enc_namafile.ext).</p>
                                </div>
                            </template>

                            <div>
                                <label for="kunci_dekripsi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kunci Dekripsi</label>
                                <input type="text" id="kunci_dekripsi" name="kunci" placeholder="Masukkan kunci yang sama dengan saat enkripsi" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors">
                            </div>

                            <button type="submit" 
                                    class="w-full px-5 py-4 text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:focus:ring-offset-gray-800 transition-colors font-semibold text-lg">
                                🔓 Dekripsi File
                            </button>
                        </form>
                    </div>
                </div>

                <!-- 🔄 Salsa20 Encryption/Decryption -->
                <div x-show="tab === 'salsa20'" x-cloak>
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2">🔄 Enkripsi & Dekripsi Database (Salsa20)</h3>
                        <p class="text-gray-600 dark:text-gray-400">Gunakan algoritma Salsa20 untuk enkripsi dan dekripsi file dengan kecepatan tinggi dan keamanan yang kuat.</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                        <div class="flex space-x-4">
                            <button @click="salsaMode = 'encrypt'" 
                                    :class="salsaMode === 'encrypt' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-600 text-gray-700 dark:text-gray-300'"
                                    class="flex-1 py-3 px-4 rounded-lg font-medium transition-colors duration-200 border border-blue-600">
                                🔐 Enkripsi
                            </button>
                            <button @click="salsaMode = 'decrypt'" 
                                    :class="salsaMode === 'decrypt' ? 'bg-green-600 text-white' : 'bg-white dark:bg-gray-600 text-gray-700 dark:text-gray-300'"
                                    class="flex-1 py-3 px-4 rounded-lg font-medium transition-colors duration-200 border border-green-600">
                                🔓 Dekripsi
                            </button>
                        </div>
                    </div>

                    <div x-show="salsaMode === 'encrypt'">
                        <h4 class="text-xl font-semibold mb-4 text-blue-600 dark:text-blue-400">Enkripsi File Database (Salsa20)</h4>
                        <form action="../enkripsi/enkripsi_db.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih File:</label>
                                <div class="file-upload-area" 
                                     @dragover="isDragging = true" 
                                     @dragleave="isDragging = false"
                                     @drop="isDragging = false"
                                     :class="{ 'dragover': isDragging }">
                                    <input id="file-salsa-encrypt" name="file" type="file" required
                                           class="hidden"
                                           @change="handleFileSelect">
                                    <label for="file-salsa-encrypt" class="cursor-pointer">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-600 dark:text-gray-300">Klik untuk upload file</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">File apa saja akan diubah menjadi .enc</p>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label for="key-salsa-encrypt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Masukkan Kunci (Password):</label>
                                <input type="password" name="key" id="key-salsa-encrypt" required 
                                       placeholder="Buat kunci yang kuat untuk enkripsi"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors">
                            </div>

                            <button type="submit" 
                                    class="w-full px-5 py-4 text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-colors font-semibold text-lg">
                                🔒 Enkripsi & Download (.enc)
                            </button>
                        </form>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-3 text-center">File akan dienkripsi dan diunduh dalam format <b>.enc</b></p>
                    </div>

                    <div x-show="salsaMode === 'decrypt'">
                        <h4 class="text-xl font-semibold mb-4 text-green-600 dark:text-green-400">Dekripsi File Database (.enc)</h4>
                        <form action="../enkripsi/dekripsi_db.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih File (.enc):</label>
                                <div class="file-upload-area" 
                                     @dragover="isDragging = true" 
                                     @dragleave="isDragging = false"
                                     @drop="isDragging = false"
                                     :class="{ 'dragover': isDragging }">
                                    <input id="file-salsa-decrypt" name="file" type="file" accept=".enc" required
                                           class="hidden"
                                           @change="handleFileSelect">
                                    <label for="file-salsa-decrypt" class="cursor-pointer">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-600 dark:text-gray-300">Klik untuk upload file .enc</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Hanya file dengan ekstensi .enc</p>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label for="key-salsa-decrypt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Masukkan Kunci (Password):</label>
                                <input type="password" name="key" id="key-salsa-decrypt" required 
                                       placeholder="Gunakan kunci yang sama dengan saat enkripsi"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors">
                            </div>

                            <button type="submit" 
                                    class="w-full px-5 py-4 text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:focus:ring-offset-gray-800 transition-colors font-semibold text-lg">
                                🔓 Dekripsi & Download
                            </button>
                        </form>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-3 text-center">Pastikan kunci yang dimasukkan benar untuk proses dekripsi</p>
                    </div>
                </div>

                <!-- 🖼️ Steganografi -->
                <div x-show="tab === 'stegano'" x-cloak>
                    <?php
                    // Tidak perlu session atau config database
                    include '../enkripsi/crypto_functions.php'; 

                    $message = ''; 
                    $hasil_stegano = '';
                    $tab_aktif = 'sembunyi'; // Tab default

                    // === LOGIKA STEGANOGRAFI (IDEA + LSB) ===
                    if (isset($_POST['proses_stegano'])) {
                        
                        // Pastikan folder uploads ada dan bisa ditulisi
                        $upload_dir = "uploads/";
                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0777, true); // 0777 untuk izin penuh
                        }

                        if (isset($_FILES['stegano_image']) && $_FILES['stegano_image']['error'] == 0) {
                            $input_tmp_path = $_FILES['stegano_image']['tmp_name'];
                            $input_filename = basename($_FILES['stegano_image']['name']);
                            
                            // Mode 1: Sembunyikan Pesan (Encode)
                            if ($_POST['aksi_stegano'] == 'sembunyi' && !empty($_POST['stegano_message'])) {
                                $tab_aktif = 'sembunyi';
                                $secret_message = $_POST['stegano_message'];
                                
                                // Panggil fungsi LSB (yang memanggil Python)
                                $result = lsb_hide_message($input_tmp_path, $secret_message);
                                
                                if ($result['status'] == 'sukses') {
                                    $message = '<div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-900 dark:text-green-300">SUKSES! Pesan terenkripsi (IDEA) dan disembunyikan (LSB).</div>';
                                    $hasil_stegano = "<p>Download gambar stego Anda: <a href='{$result['file']}' download='{$result['filename']}' class='text-blue-500 hover:underline font-bold'>{$result['filename']}</a></p><p class='text-xs text-gray-500 mt-2'>Upload file ini lagi di tab 'Ekstrak Pesan' untuk didekripsi.</p>";
                                } else {
                                    $message = '<div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-900 dark:text-red-300">GAGAL: ' . htmlspecialchars($result['message']) . '</div>';
                                }
                            
                            // Mode 2: Ekstrak Pesan (Decode)
                            } elseif ($_POST['aksi_stegano'] == 'ekstrak') {
                                $tab_aktif = 'ekstrak';
                                $extracted_message = lsb_extract_message($input_tmp_path);
                                $message = '<div class="p-4 mb-4 text-sm text-blue-700 bg-blue-100 rounded-lg dark:bg-blue-900 dark:text-blue-300">Proses Ekstraksi Selesai.</div>';
                                $hasil_stegano = "<p>Pesan Ditemukan (LSB + IDEA):</p><pre class='w-full p-4 mt-1 bg-gray-100 rounded-md overflow-x-auto dark:bg-gray-700 dark:text-gray-200'>" . htmlspecialchars($extracted_message) . "</pre>";
                            } else if ($_POST['aksi_stegano'] == 'sembunyi' && empty($_POST['stegano_message'])) {
                                $message = '<div class="p-4 mb-4 text-sm text-yellow-700 bg-yellow-100 rounded-lg dark:bg-yellow-900 dark:text-yellow-300">Pesan rahasia tidak boleh kosong saat menyembunyikan.</div>';
                            }
                        } else {
                            $message = '<div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-900 dark:text-red-300">Upload file gambar gagal atau tidak ada file dipilih.</div>';
                        }
                    }
                    ?>

                    <?php echo $message; // Tampilkan notifikasi global ?>

                    <div x-data="{ tab_stegano: '<?php echo $tab_aktif; ?>' }" class="w-full">
                        
                        <div class="flex border-b border-gray-200 dark:border-gray-700 overflow-x-auto bg-gray-50 dark:bg-gray-900 rounded-t-lg">
                            <button @click="tab_stegano = 'sembunyi'" :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400 font-semibold tab-active-indicator': tab_stegano === 'sembunyi', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300': tab_stegano !== 'sembunyi' }"
                                    class="flex-shrink-0 py-4 px-6 text-center font-medium border-b-2 focus:outline-none transition-colors duration-200">
                                Sembunyikan Pesan (Encode)
                            </button>
                            <button @click="tab_stegano = 'ekstrak'" :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400 font-semibold tab-active-indicator': tab_stegano === 'ekstrak', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300': tab_stegano !== 'ekstrak' }"
                                    class="flex-shrink-0 py-4 px-6 text-center font-medium border-b-2 focus:outline-none transition-colors duration-200">
                                Ekstrak Pesan (Decode)
                            </button>
                        </div>

                        <div class="mt-6">
                            
                            <div x-show="tab_stegano === 'sembunyi'" x-cloak>
                                <h3 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100">Sembunyikan Pesan (IDEA + LSB)</h3>
                                <p class="text-sm text-gray-600 mb-6 dark:text-gray-400">Upload gambar asli (cover) dan masukkan pesan rahasia Anda. Pesan akan dienkripsi dengan IDEA lalu disisipkan ke LSB gambar.</p>
                                
                                <form method="POST" action="#stegano" enctype="multipart/form-data" class="space-y-6">
                                    <input type="hidden" name="aksi_stegano" value="sembunyi">
                                    <div>
                                        <label for="stegano_img_1" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih Gambar Asli</label>
                                        <div class="file-upload-area" 
                                             @dragover="isDragging = true" 
                                             @dragleave="isDragging = false"
                                             @drop="isDragging = false"
                                             :class="{ 'dragover': isDragging }">
                                            <input id="stegano_img_1" name="stegano_image" type="file" accept="image/png,image/jpeg" required
                                                   class="hidden"
                                                   @change="handleFileSelect">
                                            <label for="stegano_img_1" class="cursor-pointer">
                                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <p class="text-lg font-medium text-gray-600 dark:text-gray-300">Klik untuk upload gambar</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">PNG atau JPG (MAX. 5MB)</p>
                                            </label>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="stegano_message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pesan Rahasia</label>
                                        <textarea id="stegano_message" name="stegano_message" rows="4" required
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors resize-vertical"
                                               placeholder="Masukkan pesan rahasia yang ingin disembunyikan dalam gambar"></textarea>
                                    </div>
                                    <button type="submit" name="proses_stegano" 
                                            class="w-full px-5 py-4 text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-colors font-semibold text-lg">
                                        🖼️ Sembunyikan Pesan & Download Gambar
                                    </button>
                                </form>
                                
                                <?php if ($tab_aktif == 'sembunyi' && !empty($hasil_stegano)): ?>
                                    <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Hasil Proses:</label>
                                        <div class="w-full p-4 mt-1 bg-white dark:bg-gray-800 rounded-md overflow-x-auto border border-blue-200 dark:border-blue-800">
                                            <?php echo $hasil_stegano; // Ini akan menampilkan link download ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div x-show="tab_stegano === 'ekstrak'" x-cloak>
                                <h3 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100">Ekstrak Pesan (LSB + IDEA)</h3>
                                <p class="text-sm text-gray-600 mb-6 dark:text-gray-400">Upload file gambar .png yang Anda download sebelumnya (misal: stego_namafile.png) untuk mengekstrak dan mendekripsi pesan rahasianya.</p>
                                
                                <form method="POST" action="#stegano" enctype="multipart/form-data" class="space-y-6">
                                    <input type="hidden" name="aksi_stegano" value="ekstrak">
                                    <div>
                                        <label for="stegano_img_2" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih Gambar Stego</label>
                                        <div class="file-upload-area" 
                                             @dragover="isDragging = true" 
                                             @dragleave="isDragging = false"
                                             @drop="isDragging = false"
                                             :class="{ 'dragover': isDragging }">
                                            <input id="stegano_img_2" name="stegano_image" type="file" accept="image/png" required
                                                   class="hidden"
                                                   @change="handleFileSelect">
                                            <label for="stegano_img_2" class="cursor-pointer">
                                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <p class="text-lg font-medium text-gray-600 dark:text-gray-300">Klik untuk upload gambar stego</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">PNG (MAX. 5MB)</p>
                                            </label>
                                        </div>
                                    </div>
                                    <button type="submit" name="proses_stegano" 
                                            class="w-full px-5 py-4 text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:focus:ring-offset-gray-800 transition-colors font-semibold text-lg">
                                        🔍 Ekstrak Pesan Rahasia
                                    </button>
                                </form>
                                
                                <?php if ($tab_aktif == 'ekstrak' && !empty($hasil_stegano)): ?>
                                    <div class="mt-8 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Hasil Ekstraksi:</label>
                                        <div class="w-full p-4 mt-1 bg-white dark:bg-gray-800 rounded-md overflow-x-auto border border-green-200 dark:border-green-800">
                                            <?php echo $hasil_stegano; // Ini akan menampilkan pesan yang diekstrak ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
            © <?= date("Y") ?> Sistem Kriptografi | Rail Fence + XOR & AES + Blowfish & IDEA + LSB + Salsa20
        </div>
    </main>

    <script>
        function handleFileSelect(event) {
            const fileInput = event.target;
            const fileName = fileInput.files[0]?.name;
            if (fileName) {
                const label = fileInput.parentElement.querySelector('label');
                const originalHTML = label.innerHTML;
                label.innerHTML = `
                    <svg class="w-8 h-8 mx-auto mb-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <p class="text-sm font-medium text-green-600 dark:text-green-400">File dipilih:</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">${fileName}</p>
                `;
                
                // Reset after 3 seconds
                setTimeout(() => {
                    label.innerHTML = originalHTML;
                }, 3000);
            }
        }
    </script>
</body>
</html>