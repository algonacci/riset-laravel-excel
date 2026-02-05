<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Excel Demo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">
            📊 Laravel Excel Demo
        </h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {!! session('error') !!}
            </div>
        @endif

        <div class="grid md:grid-cols-2 gap-6">
            {{-- Export Section --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">📤 Export Data</h2>
                <p class="text-gray-600 mb-4">Download data Laravel CMS Users dalam format Excel (.xlsx)</p>
                <a href="{{ route('excel.export') }}" 
                   class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition">
                    Download Excel (.xlsx)
                </a>
            </div>

            {{-- Import Section --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">📥 Import Data</h2>
                <p class="text-gray-600 mb-4">Upload file Excel (.xlsx, .xls) untuk import data</p>
                
                <form action="{{ route('excel.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih File Excel
                        </label>
                        <input type="file" name="file" accept=".xlsx,.xls" 
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                               required>
                        <p class="mt-1 text-xs text-gray-500">Format yang didukung: .xlsx, .xls (Excel only)</p>
                    </div>
                    <button type="submit" 
                            class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded transition">
                        Import Data
                    </button>
                </form>

                <div class="mt-4 pt-4 border-t">
                    <a href="{{ route('excel.template') }}" 
                       class="text-sm text-blue-500 hover:text-blue-700 underline">
                        📋 Download Template Excel
                    </a>
                </div>
            </div>
        </div>

        {{-- Info Section --}}
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-3 text-gray-700">ℹ️ Informasi Format</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium text-gray-700">Kolom</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-700">Tipe</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-700">Required</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-700">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <td class="px-4 py-2 font-mono text-sm">name</td>
                            <td class="px-4 py-2">String</td>
                            <td class="px-4 py-2 text-green-600">Ya</td>
                            <td class="px-4 py-2">Nama lengkap user</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-mono text-sm">email</td>
                            <td class="px-4 py-2">Email</td>
                            <td class="px-4 py-2 text-green-600">Ya</td>
                            <td class="px-4 py-2">Email unik</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-mono text-sm">password</td>
                            <td class="px-4 py-2">String</td>
                            <td class="px-4 py-2 text-gray-500">Opsional</td>
                            <td class="px-4 py-2">Min 6 karakter (default: password123)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
