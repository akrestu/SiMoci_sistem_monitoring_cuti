<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test API Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Test API Karyawan</h2>
        
        <div class="card mt-3">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <span>Tes Langsung API Search By NIK</span>
                    <button id="testDirectApi" class="btn btn-sm btn-warning">Test API Langsung</button>
                </div>
            </div>
            <div class="card-body">
                <div class="input-group mb-3">
                    <input type="text" id="nikInput" class="form-control" placeholder="Masukkan NIK">
                    <button class="btn btn-primary" type="button" id="searchBtn">Cari</button>
                </div>
                
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5>Hasil:</h5>
                        <div>
                            <span id="statusCode" class="badge bg-secondary"></span>
                            <span id="responseTime" class="badge bg-secondary ms-2"></span>
                        </div>
                    </div>
                    <pre id="result" class="border bg-light p-3" style="min-height: 200px; overflow: auto;"></pre>
                    
                    <div class="alert alert-info mt-3">
                        <strong>Request URL:</strong> <span id="requestUrl"></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">Periksa Headers</div>
            <div class="card-body">
                <pre id="headers" class="border bg-light p-3" style="min-height: 100px;"></pre>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Search button event
            document.getElementById('searchBtn').addEventListener('click', function() {
                const nik = document.getElementById('nikInput').value.trim();
                
                if (!nik) {
                    alert('Masukkan NIK terlebih dahulu');
                    return;
                }
                
                searchByNik(nik);
            });
            
            // Enter key on input
            document.getElementById('nikInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('searchBtn').click();
                }
            });
            
            // Direct test with test data
            document.getElementById('testDirectApi').addEventListener('click', function() {
                // Try a few different NIKs to increase chance of finding data
                const testNiks = ['1001', '1002', '1003', '123', '001'];
                
                let resultHtml = '<div class="alert alert-warning">Testing multiple NIKs:</div>';
                
                for (const nik of testNiks) {
                    resultHtml += `<div class="mt-2"><strong>Testing NIK: ${nik}</strong></div>`;
                    
                    // Make an immediate synchronous request for testing
                    const xhr = new XMLHttpRequest();
                    xhr.open('GET', '/api/karyawans/search?nik=' + encodeURIComponent(nik), false); // Synchronous for testing
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.send();
                    
                    resultHtml += `<div>Status: ${xhr.status}</div>`;
                    resultHtml += `<div>Response: ${xhr.responseText}</div>`;
                    resultHtml += '<hr>';
                }
                
                document.getElementById('result').innerHTML = resultHtml;
            });
            
            function searchByNik(nik) {
                document.getElementById('result').innerHTML = 'Loading...';
                document.getElementById('statusCode').innerHTML = '';
                document.getElementById('responseTime').innerHTML = '';
                
                const url = '/api/karyawans/search?nik=' + encodeURIComponent(nik);
                document.getElementById('requestUrl').textContent = url;
                
                const startTime = performance.now();
                
                // Create XHR request
                const xhr = new XMLHttpRequest();
                xhr.open('GET', url, true);
                xhr.setRequestHeader('Accept', 'application/json');
                
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        const endTime = performance.now();
                        const duration = Math.round(endTime - startTime);
                        
                        document.getElementById('statusCode').innerHTML = 'Status: ' + xhr.status;
                        document.getElementById('responseTime').innerHTML = 'Time: ' + duration + 'ms';
                        
                        // Display all headers
                        const headers = xhr.getAllResponseHeaders();
                        document.getElementById('headers').textContent = headers;
                        
                        try {
                            // Try to parse as JSON
                            const data = JSON.parse(xhr.responseText);
                            document.getElementById('result').textContent = JSON.stringify(data, null, 2);
                            
                            // Additional info about results
                            if (Array.isArray(data)) {
                                if (data.length === 0) {
                                    document.getElementById('result').innerHTML += '\n\n<div class="alert alert-warning mt-3">No results found</div>';
                                } else {
                                    document.getElementById('result').innerHTML += `\n\n<div class="alert alert-success mt-3">Found ${data.length} result(s)</div>`;
                                }
                            }
                        } catch (e) {
                            // If not JSON, just show the text
                            document.getElementById('result').textContent = 'Error parsing JSON: ' + e.message + '\n\nRaw response:\n' + xhr.responseText;
                        }
                    }
                };
                
                xhr.send();
            }
        });
    </script>
</body>
</html> 