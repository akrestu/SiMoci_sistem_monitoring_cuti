@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3>Test Karyawan Search</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="karyawan_id" class="form-label">Pilih Karyawan</label>
                        <select class="form-select select2-ajax" id="karyawan_id" name="karyawan_id" data-placeholder="Cari karyawan berdasarkan nama atau NIK">
                            <option value="">-- Pilih Karyawan --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <h4>Debug Info:</h4>
                        <div id="debug-info" class="alert alert-info">
                            Waiting for selection...
                        </div>
                    </div>
                    <button id="test-direct" class="btn btn-primary">Test Direct API Call</button>
                    <div id="api-result" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        console.log('Test page loaded');
        
        // Initialize Select2
        $('.select2-ajax').select2({
            theme: 'bootstrap-5',
            ajax: {
                url: '/api/karyawans/search',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    console.log('Search params:', params);
                    return {
                        q: params.term || ''
                    };
                },
                processResults: function(data, params) {
                    console.log('API response:', data);
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.nama + ' - ' + item.nik + ' (' + item.departemen + ')',
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 0,  // Allow empty search to return all results
            placeholder: 'Cari karyawan berdasarkan nama atau NIK',
            allowClear: true
        });
        
        // Show selected data in debug info
        $('#karyawan_id').on('change', function() {
            const selectedId = $(this).val();
            const selectedText = $(this).find('option:selected').text();
            
            $('#debug-info').html(`
                <strong>Selected ID:</strong> ${selectedId}<br>
                <strong>Selected Text:</strong> ${selectedText}
            `);
        });
        
        // Test direct API call
        $('#test-direct').on('click', function() {
            $('#api-result').html('<div class="spinner-border spinner-border-sm" role="status"></div> Loading...');
            
            $.ajax({
                url: '/api/karyawans/search',
                method: 'GET',
                data: { q: '' },  // Empty string to get all results
                success: function(data) {
                    console.log('Direct API test response:', data);
                    
                    let html = '<div class="alert alert-success mt-3">';
                    html += `<strong>Found ${data.length} records</strong><br>`;
                    
                    if (data.length > 0) {
                        html += '<ul>';
                        data.forEach(function(item) {
                            html += `<li>${item.nama} - ${item.nik} (${item.departemen})</li>`;
                        });
                        html += '</ul>';
                    } else {
                        html += '<p>No records found!</p>';
                    }
                    
                    html += '</div>';
                    $('#api-result').html(html);
                },
                error: function(xhr, status, error) {
                    console.error('API Error:', xhr, status, error);
                    $('#api-result').html(`<div class="alert alert-danger mt-3">Error: ${error}</div>`);
                }
            });
        });
    });
</script>
@endpush 