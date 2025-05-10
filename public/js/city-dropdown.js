/**
 * City and Station Dropdown Implementation for SiCuti
 * Mengimplementasikan dropdown kota dan stasiun untuk transportasi pesawat dan kereta
 */

document.addEventListener('DOMContentLoaded', function() {
    // Daftar kota untuk pesawat
    const cities = [
        'Palembang', 
        'Medan', 
        'Surabaya', 
        'Makasar', 
        'Jakarta', 
        'Yogyakarta', 
        'Malang', 
        'Balikpapan'
    ];
    
    // Daftar stasiun untuk kereta
    const stations = [
        'Lahat',
        'Palembang',
        'Lubuklinggau'
    ];
    
    // Fungsi untuk mengubah input menjadi select dropdown untuk pesawat
    function setupCityDropdown(inputElement) {
        // Jika input tidak ditemukan, langsung return
        if (!inputElement) return;
        
        // Simpan nilai input sebelumnya jika ada
        const prevValue = inputElement.value;
        
        // Buat element select baru
        const selectEl = document.createElement('select');
        selectEl.className = inputElement.className;
        selectEl.id = inputElement.id;
        selectEl.name = inputElement.name;
        selectEl.required = inputElement.required;
        
        // Tambahkan placeholder option
        const placeholderOption = document.createElement('option');
        placeholderOption.value = '';
        placeholderOption.textContent = '-- Pilih Kota --';
        selectEl.appendChild(placeholderOption);
        
        // Tambahkan opsi-opsi kota
        cities.forEach(city => {
            const option = document.createElement('option');
            option.value = city;
            option.textContent = city;
            
            // Set selected jika sesuai dengan nilai sebelumnya
            if (city === prevValue) {
                option.selected = true;
            }
            
            selectEl.appendChild(option);
        });
        
        // Set Palembang sebagai nilai default untuk rute asal jika tidak ada nilai sebelumnya
        if (!prevValue && inputElement.id.includes('rute_asal')) {
            selectEl.value = 'Palembang';
        }
        
        // Ganti input dengan select
        inputElement.parentNode.replaceChild(selectEl, inputElement);
        
        return selectEl;
    }
    
    // Fungsi untuk mengubah input menjadi select dropdown untuk kereta
    function setupStationDropdown(inputElement, isReturn) {
        // Jika input tidak ditemukan, langsung return
        if (!inputElement) return;
        
        // Simpan nilai input sebelumnya jika ada
        const prevValue = inputElement.value;
        
        // Buat element select baru
        const selectEl = document.createElement('select');
        selectEl.className = inputElement.className;
        selectEl.id = inputElement.id;
        selectEl.name = inputElement.name;
        selectEl.required = inputElement.required;
        
        // Tambahkan placeholder option
        const placeholderOption = document.createElement('option');
        placeholderOption.value = '';
        placeholderOption.textContent = '-- Pilih Stasiun --';
        selectEl.appendChild(placeholderOption);
        
        // Tambahkan opsi-opsi stasiun
        stations.forEach(station => {
            const option = document.createElement('option');
            option.value = station;
            option.textContent = station;
            
            // Set selected jika sesuai dengan nilai sebelumnya
            if (station === prevValue) {
                option.selected = true;
            }
            
            selectEl.appendChild(option);
        });
        
        // Set nilai default berdasarkan apakah ini tiket berangkat atau kembali
        if (!prevValue) {
            if (inputElement.id.includes('rute_asal_pergi') || 
                (inputElement.id.includes('rute_asal') && !inputElement.id.includes('kembali'))) {
                // Untuk tiket berangkat rute asal = Lahat
                selectEl.value = 'Lahat';
            } else if (inputElement.id.includes('rute_tujuan_kembali') || 
                      (inputElement.id.includes('rute_tujuan') && inputElement.id.includes('kembali'))) {
                // Untuk tiket kembali rute tujuan = Lahat
                selectEl.value = 'Lahat';
            }
        }
        
        // Ganti input dengan select
        inputElement.parentNode.replaceChild(selectEl, inputElement);
        
        return selectEl;
    }
    
    // Fungsi untuk setup semua input rute pada form
    function setupAllInputs() {
        // Deteksi apakah halaman memiliki form transportasi
        const hasTransportasiForm = document.querySelectorAll('.transportasi-details, [id*="transportasi_details"]').length > 0;
        
        // Dapatkan semua input rute
        const allInputs = document.querySelectorAll('input[id^="rute_"]');
        
        // Jika tidak ada input rute, keluar dari fungsi
        if (allInputs.length === 0) return;
        
        // Setup berdasarkan jenis transportasi
        const processedInputs = [];
        
        allInputs.forEach(input => {
            // Skip jika input sudah digantikan dengan select
            if (processedInputs.includes(input.id)) return;
            processedInputs.push(input.id);
            
            // Cek apakah input ini ada di dalam form Edit or Detail
            const transportasiContainer = input.closest('[id*="transportasi_details"]') || 
                                        input.closest('.transportasi-details') ||
                                        input.closest('form');
            
            if (!transportasiContainer) return;
            
            // Cek jenis transportasi
            const isPesawat = hasTransportasiAttribute(transportasiContainer, 'Pesawat') || 
                            input.classList.contains('input-pesawat') ||
                            (input.hasAttribute('data-jenis') && input.getAttribute('data-jenis') === 'Pesawat');
            
            const isKereta = hasTransportasiAttribute(transportasiContainer, 'Kereta') || 
                           input.classList.contains('input-kereta') ||
                           (input.hasAttribute('data-jenis') && input.getAttribute('data-jenis') === 'Kereta Api');
            
            // Setup dropdown berdasarkan jenis transportasi
            if (isPesawat) {
                setupCityDropdown(input);
            } else if (isKereta) {
                const isReturn = input.id.includes('kembali');
                setupStationDropdown(input, isReturn);
            }
        });
        
        // Setup event listener untuk auto-swap pada transportasi kereta
        setupAutoSwapForTrain();
    }
    
    // Fungsi untuk memeriksa atribut transportasi
    function hasTransportasiAttribute(container, transportasiType) {
        // Cek ID container
        if (container.id && container.id.includes(transportasiType.toLowerCase())) {
            return true;
        }
        
        // Cek text content
        if (container.textContent && container.textContent.includes(transportasiType)) {
            return true;
        }
        
        // Cek child elements
        const transportasiElement = container.querySelector(`[data-jenis="${transportasiType}"], [data-transportasi="${transportasiType}"]`);
        if (transportasiElement) {
            return true;
        }
        
        // Cek select dropdown untuk jenis transportasi
        const transportasiSelect = container.querySelector('select[id*="transportasi_id"]');
        if (transportasiSelect) {
            const selectedOption = transportasiSelect.options[transportasiSelect.selectedIndex];
            if (selectedOption && selectedOption.text && selectedOption.text.includes(transportasiType)) {
                return true;
            }
            
            // Loop semua option untuk mencari teks yang cocok
            for (let i = 0; i < transportasiSelect.options.length; i++) {
                if (transportasiSelect.options[i].text && transportasiSelect.options[i].text.includes(transportasiType)) {
                    return true;
                }
            }
        }
        
        // Cek header atau label
        const headerOrLabel = container.querySelector('.card-header, label');
        if (headerOrLabel && headerOrLabel.textContent && headerOrLabel.textContent.includes(transportasiType)) {
            return true;
        }
        
        return false;
    }
    
    // Fungsi untuk setup auto-swap value untuk transportasi kereta
    function setupAutoSwapForTrain() {
        // Cari semua checkbox auto-swap
        const autoSwapCheckboxes = document.querySelectorAll('.auto-swap');
        
        autoSwapCheckboxes.forEach(checkbox => {
            const form = checkbox.closest('form') || checkbox.closest('.transportasi-details');
            if (!form) return;
            
            const transportasiId = checkbox.dataset.transportasiId;
            const formIndex = checkbox.dataset.index || '';
            
            // Tentukan ID untuk input berdasarkan apakah ini di form dinamis atau tidak
            const idPrefix = formIndex ? `_${formIndex}_` : '_';
            
            // Cari select rute (yang sebelumnya input)
            const ruteAsalPergi = form.querySelector(`select[id*="rute_asal_pergi${idPrefix}${transportasiId}"]`);
            const ruteTujuanPergi = form.querySelector(`select[id*="rute_tujuan_pergi${idPrefix}${transportasiId}"]`);
            const ruteAsalKembali = form.querySelector(`select[id*="rute_asal_kembali${idPrefix}${transportasiId}"]`);
            const ruteTujuanKembali = form.querySelector(`select[id*="rute_tujuan_kembali${idPrefix}${transportasiId}"]`);
            
            // Jika salah satu select tidak ditemukan, keluar
            if (!ruteAsalPergi || !ruteTujuanPergi || !ruteAsalKembali || !ruteTujuanKembali) return;
            
            // Untuk transportasi kereta, set nilai default jika kosong
            const isKereta = hasTransportasiAttribute(form, 'Kereta');
            if (isKereta) {
                if (!ruteAsalPergi.value) ruteAsalPergi.value = 'Lahat';
                if (!ruteTujuanKembali.value) ruteTujuanKembali.value = 'Lahat';
                
                // Auto-update rute kembali berdasarkan rute pergi
                if (ruteTujuanPergi.value && !ruteAsalKembali.value) {
                    ruteAsalKembali.value = ruteTujuanPergi.value;
                }
            }
            
            // Set up event listener untuk auto-swap
            function updateReturnValue() {
                if (checkbox.checked) {
                    ruteAsalKembali.value = ruteTujuanPergi.value;
                    ruteTujuanKembali.value = ruteAsalPergi.value;
                }
            }
            
            // Hapus event listener lama jika ada
            ruteAsalPergi.removeEventListener('change', updateReturnValue);
            ruteTujuanPergi.removeEventListener('change', updateReturnValue);
            
            // Tambahkan event listener baru
            ruteAsalPergi.addEventListener('change', updateReturnValue);
            ruteTujuanPergi.addEventListener('change', updateReturnValue);
            
            // Initial update
            if (checkbox.checked) {
                updateReturnValue();
            }
        });
    }
    
    // Event handler untuk perubahan pada select transportasi
    function handleTransportasiSelectChange() {
        const transportasiSelects = document.querySelectorAll('select[id*="transportasi_id"]');
        
        transportasiSelects.forEach(select => {
            select.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const form = this.closest('form') || this.closest('.transportasi-details');
                if (!form) return;
                
                const isPesawat = selectedOption.text.includes('Pesawat');
                const isKereta = selectedOption.text.includes('Kereta');
                
                // Temukan semua input rute di form ini
                const ruteInputs = form.querySelectorAll('input[id^="rute_"]');
                
                ruteInputs.forEach(input => {
                    // Skip jika input sudah digantikan dengan select
                    if (!input.parentNode) return;
                    
                    // Setup dropdown berdasarkan jenis transportasi
                    if (isPesawat) {
                        const newSelect = setupCityDropdown(input);
                        if (newSelect) {
                            newSelect.classList.add('input-pesawat');
                            newSelect.classList.remove('input-kereta');
                        }
                    } else if (isKereta) {
                        const isReturn = input.id.includes('kembali');
                        const newSelect = setupStationDropdown(input, isReturn);
                        if (newSelect) {
                            newSelect.classList.add('input-kereta');
                            newSelect.classList.remove('input-pesawat');
                        }
                    }
                });
                
                // Re-setup auto-swap
                setupAutoSwapForTrain();
            });
        });
    }
    
    // Jalankan setup awal
    setupAllInputs();
    handleTransportasiSelectChange();
    
    // Tangkap event DOM baru (misalnya dari form dinamis)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                // Cek apakah ada input baru yang ditambahkan
                mutation.addedNodes.forEach(node => {
                    if (node.querySelectorAll) {
                        const hasNewInputs = node.querySelectorAll('input[id^="rute_"]').length > 0;
                        const hasTransportasiSelects = node.querySelectorAll('select[id*="transportasi_id"]').length > 0;
                        
                        if (hasNewInputs || hasTransportasiSelects) {
                            setupAllInputs();
                            handleTransportasiSelectChange();
                        }
                    }
                });
            }
        });
    });
    
    // Observasi perubahan pada body
    observer.observe(document.body, { childList: true, subtree: true });
});