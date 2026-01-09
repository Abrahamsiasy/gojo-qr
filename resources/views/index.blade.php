<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>QR Gen - Free QR Code Generator</title>
    <meta name="description" content="Create beautiful, customizable QR codes instantly. 100% free, no signup required.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black min-h-screen text-white">
    <div class="max-w-7xl mx-auto px-4 py-8 md:py-12">
        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-5xl md:text-6xl font-bold bg-gradient-to-r from-cyan-400 to-teal-400 bg-clip-text text-transparent mb-3">
                QR Gen
            </h1>
            <p class="text-gray-300 text-xl mb-4">Create beautiful, customizable QR codes instantly.</p>
            <p class="text-cyan-400 font-semibold text-lg mb-6">100% free, no signup required.</p>
            
            <!-- Badges -->
            <div class="flex justify-center gap-3 flex-wrap">
                <span class="px-4 py-2 bg-green-500/20 border border-green-500/50 rounded-full text-sm text-green-400">Free Forever</span>
                <span class="px-4 py-2 bg-gray-800 border border-gray-700 rounded-full text-sm text-gray-300">No Watermark</span>
                <span class="px-4 py-2 bg-gray-800 border border-gray-700 rounded-full text-sm text-gray-300">High Resolution</span>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-6">
            <!-- Left Panel: Controls -->
            <div class="bg-gray-900 rounded-2xl p-6 md:p-8 border border-gray-800">
                <form id="qrForm" class="space-y-6">
                    @csrf
                    
                    <!-- QR Code Type Selection with Examples -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-3">QR Code Type</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 mb-3" id="typeSelector">
                            <button type="button" data-type="url" class="type-option active p-3 bg-cyan-500/20 border-2 border-cyan-500 rounded-lg hover:bg-cyan-500/30 transition-all text-left group">
                                <div class="flex items-center gap-2 mb-1">
                                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-white">URL</span>
                                </div>
                                <p class="text-xs text-gray-400 group-hover:text-gray-300 truncate">https://example.com</p>
                            </button>
                            <button type="button" data-type="text" class="type-option p-3 bg-gray-800 border-2 border-gray-700 rounded-lg hover:border-gray-600 transition-all text-left group">
                                <div class="flex items-center gap-2 mb-1">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-300">Text</span>
                                </div>
                                <p class="text-xs text-gray-500 group-hover:text-gray-400 truncate">Hello, World!</p>
                            </button>
                            <button type="button" data-type="email" class="type-option p-3 bg-gray-800 border-2 border-gray-700 rounded-lg hover:border-gray-600 transition-all text-left group">
                                <div class="flex items-center gap-2 mb-1">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-300">Email</span>
                                </div>
                                <p class="text-xs text-gray-500 group-hover:text-gray-400 truncate">user@example.com</p>
                            </button>
                            <button type="button" data-type="phone" class="type-option p-3 bg-gray-800 border-2 border-gray-700 rounded-lg hover:border-gray-600 transition-all text-left group">
                                <div class="flex items-center gap-2 mb-1">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-300">Phone</span>
                                </div>
                                <p class="text-xs text-gray-500 group-hover:text-gray-400 truncate">+1234567890</p>
                            </button>
                            <button type="button" data-type="sms" class="type-option p-3 bg-gray-800 border-2 border-gray-700 rounded-lg hover:border-gray-600 transition-all text-left group">
                                <div class="flex items-center gap-2 mb-1">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-300">SMS</span>
                                </div>
                                <p class="text-xs text-gray-500 group-hover:text-gray-400 truncate">+1234567890</p>
                            </button>
                            <button type="button" data-type="wifi" class="type-option p-3 bg-gray-800 border-2 border-gray-700 rounded-lg hover:border-gray-600 transition-all text-left group">
                                <div class="flex items-center gap-2 mb-1">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-300">WiFi</span>
                                </div>
                                <p class="text-xs text-gray-500 group-hover:text-gray-400 truncate">Network:password</p>
                            </button>
                            <button type="button" data-type="vcard" class="type-option p-3 bg-gray-800 border-2 border-gray-700 rounded-lg hover:border-gray-600 transition-all text-left group">
                                <div class="flex items-center gap-2 mb-1">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-300">vCard</span>
                                </div>
                                <p class="text-xs text-gray-500 group-hover:text-gray-400 truncate">Contact info</p>
                            </button>
                        </div>
                        <input type="hidden" name="type" id="type" value="url">
                        
                        <!-- Example Preview Box -->
                        <div id="typeExampleBox" class="mt-3 p-3 bg-gray-800/50 border border-gray-700 rounded-lg">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-16 h-16 bg-white p-2 rounded-lg" id="typeExampleQR">
                                    <!-- Example QR code will be generated here -->
                                    <div class="w-full h-full bg-gray-200 rounded flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-gray-400 mb-1" id="typeExampleLabel">Example:</p>
                                    <p class="text-sm text-white font-mono" id="typeExampleContent">https://example.com</p>
                                    <p class="text-xs text-gray-500 mt-1" id="typeExampleDesc">Scans and opens the URL in a browser</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Content Input -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2" id="contentLabel">Website URL</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                            <input 
                                type="text" 
                                name="content" 
                                id="content" 
                                required
                                placeholder="https://gojotech.et"
                                value="https://gojotech.et"
                                class="w-full pl-10 pr-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all"
                            >
                        </div>
                        <p class="mt-1 text-xs text-gray-500" id="contentHint">Enter the website URL you want to encode</p>
                    </div>

                    <!-- Style Selection -->
                    <div>
                        <div class="flex gap-2 mb-4">
                            <button type="button" id="presetsTab" class="px-4 py-2 bg-cyan-500 text-white rounded-lg text-sm font-medium transition-all">
                                Presets
                            </button>
                            <button type="button" id="customTab" class="px-4 py-2 bg-gray-800 text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-700 transition-all">
                                Custom
                            </button>
                        </div>

                        <!-- Presets -->
                        <div id="presetsPanel" class="grid grid-cols-3 gap-3">
                            <button type="button" data-preset="cyan" class="preset-btn p-4 border-2 border-cyan-500 bg-cyan-500/10 rounded-lg hover:bg-cyan-500/20 transition-all">
                                <div class="w-full aspect-square bg-white border-2 border-cyan-500 rounded"></div>
                                <p class="text-xs text-center mt-2 text-gray-300">Cyan</p>
                            </button>
                            <button type="button" data-preset="classic" class="preset-btn active p-4 border-2 border-gray-600 rounded-lg hover:border-gray-500 transition-all">
                                <div class="w-full aspect-square bg-white border-2 border-black rounded"></div>
                                <p class="text-xs text-center mt-2 text-gray-300">Classic</p>
                            </button>
                            <button type="button" data-preset="purple" class="preset-btn p-4 border-2 border-gray-600 rounded-lg hover:border-gray-500 transition-all">
                                <div class="w-full aspect-square bg-white border-2 border-purple-500 rounded"></div>
                                <p class="text-xs text-center mt-2 text-gray-300">Purple</p>
                            </button>
                            <button type="button" data-preset="emerald" class="preset-btn p-4 border-2 border-gray-600 rounded-lg hover:border-gray-500 transition-all">
                                <div class="w-full aspect-square bg-white border-2 border-emerald-500 rounded"></div>
                                <p class="text-xs text-center mt-2 text-gray-300">Emerald</p>
                            </button>
                            <button type="button" data-preset="rose" class="preset-btn p-4 border-2 border-gray-600 rounded-lg hover:border-gray-500 transition-all">
                                <div class="w-full aspect-square bg-white border-2 border-rose-500 rounded"></div>
                                <p class="text-xs text-center mt-2 text-gray-300">Rose</p>
                            </button>
                            <button type="button" data-preset="inverted" class="preset-btn p-4 border-2 border-gray-600 rounded-lg hover:border-gray-500 transition-all">
                                <div class="w-full aspect-square bg-black border-2 border-white rounded"></div>
                                <p class="text-xs text-center mt-2 text-gray-300">Inverted</p>
                            </button>
                        </div>

                        <!-- Custom Colors -->
                        <div id="customPanel" class="hidden space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">QR Color</label>
                                <div class="flex items-center gap-3">
                                    <input type="color" name="color" id="color" value="#00ffff" class="w-12 h-12 rounded-lg border-2 border-gray-700 cursor-pointer">
                                    <input type="text" id="colorHex" value="#00ffff" class="flex-1 px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Background</label>
                                <div class="flex items-center gap-3">
                                    <input type="color" name="background_color" id="backgroundColor" value="#ffffff" class="w-12 h-12 rounded-lg border-2 border-gray-700 cursor-pointer">
                                    <input type="text" id="bgColorHex" value="#ffffff" class="flex-1 px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Logo Upload with Toggle -->
                    <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-300">Add Logo to QR Code</label>
                                <p class="text-xs text-gray-500 mt-1">Place your logo in the center of the QR code</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="enableLogo" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-cyan-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cyan-500"></div>
                            </label>
                        </div>
                        <div id="logoUploadSection">
                            <label for="logo" class="flex items-center justify-center w-full px-4 py-3 bg-gray-800 border-2 border-dashed border-gray-700 rounded-lg cursor-pointer hover:border-cyan-500 transition-all">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-sm text-gray-300">Click to upload logo</span>
                                <input type="file" name="logo" id="logo" accept="image/*" class="hidden">
                            </label>
                            <div id="logoPreview" class="hidden mt-3">
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <img id="logoPreviewImg" src="" alt="Logo" class="w-20 h-20 object-cover rounded-lg border-2 border-cyan-500 shadow-lg">
                                        <button type="button" id="removeLogo" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">×</button>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs text-gray-400">Logo will be placed in the center</p>
                                        <p class="text-xs text-cyan-400 mt-1">✓ Logo enabled</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Size Slider -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-300">Size</label>
                            <span id="sizeValue" class="text-sm font-medium text-cyan-400">512px</span>
                        </div>
                        <input type="range" name="size" id="size" min="200" max="1000" value="512" step="10" class="w-full h-2 bg-gray-800 rounded-lg appearance-none cursor-pointer accent-cyan-500">
                    </div>

                    <!-- Hidden fields -->
                    <input type="hidden" name="format" value="png">
                    <input type="hidden" name="logo_size" value="0.3">
                    <input type="hidden" name="error_correction" value="H">

                    <!-- Action Buttons -->
                    <div class="flex gap-3 pt-4">
                        <button 
                            type="submit" 
                            id="downloadBtn"
                            class="flex-1 bg-gradient-to-r from-cyan-500 to-teal-500 hover:from-cyan-600 hover:to-teal-600 text-white font-semibold py-3 px-6 rounded-lg transition-all flex items-center justify-center gap-2 shadow-lg shadow-cyan-500/50"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Download PNG
                        </button>
                        <button 
                            type="button" 
                            id="copyBtn"
                            class="px-6 py-3 bg-gray-800 hover:bg-gray-700 text-white font-semibold rounded-lg transition-all flex items-center justify-center gap-2 border border-gray-700"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Copy
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Panel: Preview -->
            <div class="bg-gray-900 rounded-2xl p-6 md:p-8 border border-gray-800">
                <div>
                    <h2 class="text-xl font-semibold text-white mb-1">Preview</h2>
                    <p class="text-sm text-gray-400 mb-6">Your QR code updates in real-time</p>
                    
                    <div class="bg-white p-8 rounded-xl flex items-center justify-center min-h-[400px]" id="qrPreviewContainer">
                        <div id="qrImageContainer" class="max-w-full">
                            <!-- QR code will appear here -->
                        </div>
                    </div>
                    
                    <p class="text-xs text-gray-500 mt-6 text-center">Scan with any QR reader to test. Higher error correction ensures reliability.</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-12 text-sm text-gray-500">
            <p>Made with ❤️ for the internet</p>
            <p class="mt-1">Free & open source QR code generator</p>
        </div>
    </div>

    <script>
        // Color presets
        const presets = {
            cyan: { color: '#00ffff', background: '#ffffff' },
            classic: { color: '#000000', background: '#ffffff' },
            purple: { color: '#9333ea', background: '#ffffff' },
            emerald: { color: '#10b981', background: '#ffffff' },
            rose: { color: '#f43f5e', background: '#ffffff' },
            inverted: { color: '#ffffff', background: '#000000' }
        };

        // Initialize
        let currentQrUrl = null;
        let previewTimeout = null;

        // QR Type configurations with examples
        const typeConfigs = {
            url: { 
                label: 'Website URL', 
                placeholder: 'https://lovable.dev', 
                hint: 'Enter the website URL you want to encode', 
                example: 'https://lovable.dev',
                exampleLabel: 'Example URL:',
                exampleDesc: 'Scans and opens the URL in a browser',
                icon: '🔗'
            },
            text: { 
                label: 'Text Content', 
                placeholder: 'Hello, World!', 
                hint: 'Enter the text you want to encode', 
                example: 'Hello, World!',
                exampleLabel: 'Example Text:',
                exampleDesc: 'Displays the text when scanned',
                icon: '📝'
            },
            email: { 
                label: 'Email Address', 
                placeholder: 'contact@example.com', 
                hint: 'Enter the email address', 
                example: 'contact@example.com',
                exampleLabel: 'Example Email:',
                exampleDesc: 'Opens email composer with this address',
                icon: '📧'
            },
            sms: { 
                label: 'Phone Number for SMS', 
                placeholder: '+1234567890', 
                hint: 'Enter the phone number for SMS (with country code)', 
                example: '+1234567890',
                exampleLabel: 'Example SMS:',
                exampleDesc: 'Opens SMS app to send message to this number',
                icon: '💬'
            },
            phone: { 
                label: 'Phone Number', 
                placeholder: '+1234567890', 
                hint: 'Enter the phone number (with country code)', 
                example: '+1234567890',
                exampleLabel: 'Example Phone:',
                exampleDesc: 'Initiates a call to this number',
                icon: '📞'
            },
            wifi: { 
                label: 'WiFi Network', 
                placeholder: 'NetworkName:password:WPA', 
                hint: 'Format: SSID:Password:SecurityType (e.g., MyWiFi:password123:WPA)', 
                example: 'MyWiFi:password123:WPA',
                exampleLabel: 'Example WiFi:',
                exampleDesc: 'Connects to WiFi network automatically',
                icon: '📶'
            },
            vcard: { 
                label: 'Contact Details', 
                placeholder: 'John Doe|+1234567890|john@example.com|Company', 
                hint: 'Format: Name|Phone|Email|Organization (separated by |)', 
                example: 'John Doe|+1234567890|john@example.com|Company Inc',
                exampleLabel: 'Example Contact:',
                exampleDesc: 'Saves contact information to phone',
                icon: '👤'
            }
        };

        // Generate example QR code for selected type
        async function generateExampleQR(type) {
            const config = typeConfigs[type];
            const exampleQRContainer = document.getElementById('typeExampleQR');
            
            // Show loading
            exampleQRContainer.innerHTML = '<div class="w-full h-full bg-gray-200 rounded flex items-center justify-center"><div class="animate-spin rounded-full h-6 w-6 border-2 border-gray-400 border-t-transparent"></div></div>';
            
            try {
                const formData = new FormData();
                formData.append('type', type);
                formData.append('content', config.example);
                formData.append('size', '120');
                formData.append('format', 'png');
                formData.append('error_correction', 'H');
                formData.append('color', '#000000');
                formData.append('background_color', '#ffffff');
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                
                const response = await fetch('/generate', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                
                if (response.ok) {
                    const blob = await response.blob();
                    const url = URL.createObjectURL(blob);
                    exampleQRContainer.innerHTML = `<img src="${url}" alt="Example QR" class="w-full h-full object-contain rounded">`;
                } else {
                    exampleQRContainer.innerHTML = '<div class="w-full h-full bg-gray-200 rounded flex items-center justify-center text-xs text-gray-500">Example</div>';
                }
            } catch (error) {
                exampleQRContainer.innerHTML = '<div class="w-full h-full bg-gray-200 rounded flex items-center justify-center text-xs text-gray-500">Example</div>';
            }
        }

        // Type selection handler
        document.querySelectorAll('.type-option').forEach(btn => {
            btn.addEventListener('click', function() {
                const type = this.dataset.type;
                
                // Update active state
                document.querySelectorAll('.type-option').forEach(b => {
                    b.classList.remove('active', 'bg-cyan-500/20', 'border-cyan-500');
                    b.classList.add('bg-gray-800', 'border-gray-700');
                    b.querySelector('svg').classList.remove('text-cyan-400');
                    b.querySelector('svg').classList.add('text-gray-400');
                    b.querySelector('span').classList.remove('text-white');
                    b.querySelector('span').classList.add('text-gray-300');
                });
                
                this.classList.add('active', 'bg-cyan-500/20', 'border-cyan-500');
                this.classList.remove('bg-gray-800', 'border-gray-700');
                this.querySelector('svg').classList.remove('text-gray-400');
                this.querySelector('svg').classList.add('text-cyan-400');
                this.querySelector('span').classList.remove('text-gray-300');
                this.querySelector('span').classList.add('text-white');
                
                // Update form
                document.getElementById('type').value = type;
                const config = typeConfigs[type];
                
                document.getElementById('contentLabel').textContent = config.label;
                document.getElementById('content').placeholder = config.placeholder;
                document.getElementById('content').value = config.example;
                document.getElementById('contentHint').textContent = config.hint;
                
                // Update example box
                document.getElementById('typeExampleLabel').textContent = config.exampleLabel;
                document.getElementById('typeExampleContent').textContent = config.example;
                document.getElementById('typeExampleDesc').textContent = config.exampleDesc;
                
                // Generate example QR code
                generateExampleQR(type);
                
                // Update main preview
                updatePreview();
            });
        });

        // Logo toggle handler
        document.getElementById('enableLogo').addEventListener('change', function() {
            const logoSection = document.getElementById('logoUploadSection');
            const logoInput = document.getElementById('logo');
            
            if (this.checked) {
                logoSection.classList.remove('opacity-50', 'pointer-events-none');
                logoInput.disabled = false;
            } else {
                logoSection.classList.add('opacity-50', 'pointer-events-none');
                logoInput.disabled = true;
                logoInput.value = '';
                document.getElementById('logoPreview').classList.add('hidden');
            }
            updatePreview();
        });

        // Preset/Custom tab switching
        document.getElementById('presetsTab').addEventListener('click', function() {
            document.getElementById('presetsTab').classList.add('bg-cyan-500', 'text-white');
            document.getElementById('presetsTab').classList.remove('bg-gray-800', 'text-gray-300');
            document.getElementById('customTab').classList.remove('bg-cyan-500', 'text-white');
            document.getElementById('customTab').classList.add('bg-gray-800', 'text-gray-300');
            document.getElementById('presetsPanel').classList.remove('hidden');
            document.getElementById('customPanel').classList.add('hidden');
        });

        document.getElementById('customTab').addEventListener('click', function() {
            document.getElementById('customTab').classList.add('bg-cyan-500', 'text-white');
            document.getElementById('customTab').classList.remove('bg-gray-800', 'text-gray-300');
            document.getElementById('presetsTab').classList.remove('bg-cyan-500', 'text-white');
            document.getElementById('presetsTab').classList.add('bg-gray-800', 'text-gray-300');
            document.getElementById('presetsPanel').classList.add('hidden');
            document.getElementById('customPanel').classList.remove('hidden');
        });

        // Preset selection
        document.querySelectorAll('.preset-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.preset-btn').forEach(b => {
                    b.classList.remove('active', 'border-cyan-500', 'bg-cyan-500/10');
                    b.classList.add('border-gray-600');
                });
                this.classList.add('active', 'border-cyan-500', 'bg-cyan-500/10');
                this.classList.remove('border-gray-600');

                const preset = presets[this.dataset.preset];
                document.getElementById('color').value = preset.color;
                document.getElementById('colorHex').value = preset.color;
                document.getElementById('backgroundColor').value = preset.background;
                document.getElementById('bgColorHex').value = preset.background;

                updatePreview();
            });
        });

        // Color picker sync
        document.getElementById('color').addEventListener('input', function() {
            document.getElementById('colorHex').value = this.value;
            updatePreview();
        });

        document.getElementById('colorHex').addEventListener('input', function() {
            if (/^#?[0-9A-Fa-f]{6}$/.test(this.value)) {
                const hex = this.value.startsWith('#') ? this.value : '#' + this.value;
                document.getElementById('color').value = hex;
                this.value = hex;
                updatePreview();
            }
        });

        document.getElementById('backgroundColor').addEventListener('input', function() {
            document.getElementById('bgColorHex').value = this.value;
            updatePreview();
        });

        document.getElementById('bgColorHex').addEventListener('input', function() {
            if (/^#?[0-9A-Fa-f]{6}$/.test(this.value)) {
                const hex = this.value.startsWith('#') ? this.value : '#' + this.value;
                document.getElementById('backgroundColor').value = hex;
                this.value = hex;
                updatePreview();
            }
        });

        // Size slider
        document.getElementById('size').addEventListener('input', function() {
            document.getElementById('sizeValue').textContent = this.value + 'px';
            updatePreview();
        });

        // Content input
        document.getElementById('content').addEventListener('input', function() {
            clearTimeout(previewTimeout);
            previewTimeout = setTimeout(updatePreview, 500);
        });

        // Logo preview
        document.getElementById('logo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Enable logo toggle if a file is selected
                document.getElementById('enableLogo').checked = true;
                document.getElementById('logoUploadSection').classList.remove('opacity-50', 'pointer-events-none');
                this.disabled = false;
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('logoPreviewImg').src = e.target.result;
                    document.getElementById('logoPreview').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
                updatePreview();
            }
        });

        document.getElementById('removeLogo')?.addEventListener('click', function() {
            document.getElementById('logo').value = '';
            document.getElementById('logoPreview').classList.add('hidden');
            document.getElementById('enableLogo').checked = false;
            document.getElementById('logoUploadSection').classList.add('opacity-50', 'pointer-events-none');
            document.getElementById('logo').disabled = true;
            updatePreview();
        });

        // Update preview function
        async function updatePreview() {
            const formData = new FormData(document.getElementById('qrForm'));
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            
            // Remove logo from form if toggle is off
            if (!document.getElementById('enableLogo').checked) {
                formData.delete('logo');
            }
            
            const container = document.getElementById('qrImageContainer');
            container.innerHTML = '<div class="text-gray-400">Generating...</div>';

            try {
                const response = await fetch('/generate', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    const blob = await response.blob();
                    const url = URL.createObjectURL(blob);
                    
                    // Clean up old URL
                    if (currentQrUrl) {
                        URL.revokeObjectURL(currentQrUrl);
                    }
                    currentQrUrl = url;
                    
                    container.innerHTML = `<img src="${url}" alt="QR Code" class="max-w-full h-auto">`;
                } else {
                    const errorData = await response.json().catch(() => ({}));
                    container.innerHTML = '<div class="text-red-400">Error: ' + (errorData.message || 'Failed to generate QR code') + '</div>';
                }
            } catch (error) {
                container.innerHTML = '<div class="text-red-400">Error: ' + error.message + '</div>';
            }
        }

        // Download button
        document.getElementById('downloadBtn').addEventListener('click', function(e) {
            if (currentQrUrl) {
                const a = document.createElement('a');
                a.href = currentQrUrl;
                a.download = 'qrcode.png';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            } else {
                document.getElementById('qrForm').requestSubmit();
            }
        });

        // Copy button
        document.getElementById('copyBtn').addEventListener('click', async function() {
            if (currentQrUrl) {
                try {
                    const response = await fetch(currentQrUrl);
                    const blob = await response.blob();
                    await navigator.clipboard.write([
                        new ClipboardItem({ 'image/png': blob })
                    ]);
                    
                    // Visual feedback
                    const originalText = this.innerHTML;
                    this.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Copied!';
                    this.classList.add('bg-green-600');
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.classList.remove('bg-green-600');
                    }, 2000);
                } catch (error) {
                    alert('Failed to copy image. Please download instead.');
                }
            }
        });

        // Form submission
        document.getElementById('qrForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Remove logo from form if toggle is off
            const formData = new FormData(this);
            if (!document.getElementById('enableLogo').checked) {
                formData.delete('logo');
            }
            
            await updatePreview();
        });

        // Initial preview and example
        generateExampleQR('url');
        updatePreview();
    </script>
</body>
</html>
