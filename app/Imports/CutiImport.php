<?php

namespace App\Imports;

use App\Models\Cuti;
use App\Models\Karyawan;
use App\Models\JenisCuti;
use App\Models\CutiDetail;
use App\Models\Transportasi;
use App\Models\TransportasiDetail;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;
use Exception;

class CutiImport implements ToArray, WithHeadingRow, WithValidation, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    protected $failures = [];
    protected $imported = [];
    protected $totalRows = 0;
    
    /**
     * @param array $array
     */
    public function array(array $rows)
    {
        $this->totalRows = 0;
        $actualRows = 0;
        
        // Hitung jumlah baris yang benar-benar berisi data
        foreach ($rows as $row) {
            if ($this->isValidRow($row)) {
                $actualRows++;
            }
        }
        
        Log::info('Processing ' . $actualRows . ' actual rows from Excel (total rows: ' . count($rows) . ')');
        
        foreach ($rows as $index => $row) {
            // Periksa apakah baris ini valid
            if (!$this->isValidRow($row)) {
                continue;
            }
            
            $this->totalRows++;
            Log::info('Processing row #' . $this->totalRows . ':', [
                'nik' => $row['nik'] ?? 'NULL',
                'jenis_cuti' => $row['jenis_cuti'] ?? 'NULL'
            ]);
            
            try {
                $cuti = $this->processRow($row);
                if ($cuti) {
                    $this->imported[] = $cuti;
                }
            } catch (Exception $e) {
                Log::error('Error processing row #' . $this->totalRows . ': ' . $e->getMessage());
                $this->failures[] = [
                    'row' => $index + 2, // +2 untuk kompensasi header dan indeks 0
                    'errors' => [$e->getMessage()]
                ];
            }
        }
    }
    
    /**
     * Periksa apakah baris data valid untuk diproses
     */
    protected function isValidRow($row)
    {
        if (empty($row['nik']) || empty($row['jenis_cuti']) || 
            empty($row['tanggal_mulai']) || empty($row['tanggal_selesai']) || 
            empty($row['alasan'])) {
            return false;
        }
        return true;
    }
    
    /**
     * Proses satu baris data menjadi model Cuti
     */
    protected function processRow(array $row)
    {
        // Cari ID karyawan berdasarkan NIK
        $karyawan = Karyawan::where('nik', $row['nik'])->first();
        if (!$karyawan) {
            throw new Exception("Karyawan dengan NIK {$row['nik']} tidak ditemukan");
        }
        
        // Cari ID jenis cuti berdasarkan nama jenis
        $jenisCuti = JenisCuti::where('nama_jenis', $row['jenis_cuti'])->first();
        if (!$jenisCuti) {
            throw new Exception("Jenis cuti {$row['jenis_cuti']} tidak ditemukan");
        }
        
        // Parse tanggal dengan benar (mendukung baik format dd/mm/yyyy maupun format Excel)
        $tanggalMulai = $this->parseExcelDate($row['tanggal_mulai']);
        $tanggalSelesai = $this->parseExcelDate($row['tanggal_selesai']);
        
        if (!$tanggalMulai || !$tanggalSelesai) {
            throw new Exception("Format tanggal tidak valid");
        }
        
        // Hitung lama hari
        $lamaHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;
        
        // Tentukan nilai memo_kompensasi_status dan info terkaitnya
        $memoKompensasiStatus = null;
        $memoKompensasiNomor = null;
        $memoKompensasiTanggal = null;
        
        // Periksa input "perlu_memo_kompensasi" dari Excel
        if (isset($row['perlu_memo_kompensasi']) && !empty($row['perlu_memo_kompensasi'])) {
            $perluMemoValue = strtolower(trim($row['perlu_memo_kompensasi']));
            if (in_array($perluMemoValue, ['ya', 'y', 'true', '1', 'perlu', 'iya'])) {
                // Jika perlu memo kompensasi ditandai YA
                
                // Jika nomor memo sudah diisi, status = true (sudah diajukan)
                if (isset($row['memo_nomor']) && !empty($row['memo_nomor'])) {
                    $memoKompensasiStatus = true;
                    $memoKompensasiNomor = $row['memo_nomor'];
                    
                    // Parse tanggal memo jika ada
                    if (isset($row['memo_tanggal']) && !empty($row['memo_tanggal'])) {
                        $memoKompensasiTanggal = $this->parseExcelDate($row['memo_tanggal']);
                        if ($memoKompensasiTanggal) {
                            $memoKompensasiTanggal = $memoKompensasiTanggal->format('Y-m-d');
                        }
                    }
                    
                    Log::info('Memo kompensasi sudah diajukan dengan nomor: ' . $memoKompensasiNomor);
                } else {
                    // Jika tidak ada nomor memo, status = false (belum diajukan)
                    $memoKompensasiStatus = false;
                    Log::info('Memo kompensasi perlu diajukan, status: belum diajukan');
                }
            }
        }
        
        // Buat model Cuti
        $cuti = new Cuti([
            'karyawan_id' => $karyawan->id,
            'jenis_cuti_id' => $jenisCuti->id,
            'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
            'tanggal_selesai' => $tanggalSelesai->format('Y-m-d'),
            'lama_hari' => $lamaHari,
            'alasan' => $row['alasan'] ?? '',
            'status_cuti' => $row['status_cuti'] ?? 'pending',
            'memo_kompensasi_status' => $memoKompensasiStatus,
            'memo_kompensasi_nomor' => $memoKompensasiNomor,
            'memo_kompensasi_tanggal' => $memoKompensasiTanggal,
        ]);
        
        // Simpan data transportasi sebagai properti terpisah (bukan di model Cuti)
        $transportData = null;
        if (isset($row['transportasi_jenis']) && !empty($row['transportasi_jenis'])) {
            $transportData = [
                'jenis' => $row['transportasi_jenis'],
                'rute_pergi_asal' => $row['transportasi_rute_pergi_asal'] ?? null,
                'rute_pergi_tujuan' => $row['transportasi_rute_pergi_tujuan'] ?? null,
                'rute_kembali_asal' => $row['transportasi_rute_kembali_asal'] ?? null,
                'rute_kembali_tujuan' => $row['transportasi_rute_kembali_tujuan'] ?? null,
            ];
        }
        
        // Bungkus data transportasi dan model cuti sebagai satu objek
        return [
            'model' => $cuti,
            'transport' => $transportData
        ];
    }
    
    /**
     * Parse tanggal dari berbagai format yang mungkin muncul dari Excel
     */
    protected function parseExcelDate($value)
    {
        if (empty($value)) {
            return null;
        }
        
        try {
            // Jika nilai numerik, coba parse sebagai Excel date
            if (is_numeric($value)) {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float)$value));
            }
            
            // Jika string, coba parse sebagai format dd/mm/yyyy
            if (is_string($value)) {
                return Carbon::createFromFormat('d/m/Y', $value);
            }
            
            return null;
        } catch (Exception $e) {
            Log::warning('Failed to parse date: ' . $value . ' - ' . $e->getMessage());
            return null;
        }
    }
    
    public function rules(): array
    {
        return [
            '*.nik' => 'required',
            '*.jenis_cuti' => 'required',
            '*.tanggal_mulai' => 'required',
            '*.tanggal_selesai' => 'required',
            '*.alasan' => 'required',
        ];
    }
    
    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'nik.required' => 'NIK karyawan harus diisi',
            'nik.exists' => 'NIK karyawan tidak ditemukan dalam database',
            'jenis_cuti.required' => 'Jenis cuti harus diisi',
            'jenis_cuti.exists' => 'Jenis cuti tidak ditemukan dalam database',
            'tanggal_mulai.required' => 'Tanggal mulai harus diisi',
            'tanggal_mulai.date_format' => 'Format tanggal mulai harus dd/mm/yyyy',
            'tanggal_selesai.required' => 'Tanggal selesai harus diisi',
            'tanggal_selesai.date_format' => 'Format tanggal selesai harus dd/mm/yyyy',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai',
            'alasan.required' => 'Alasan cuti harus diisi',
            'status_cuti.in' => 'Status cuti harus pending, disetujui, atau ditolak',
            'memo_tanggal.date_format' => 'Format tanggal memo harus dd/mm/yyyy',
            'transportasi_jenis.exists' => 'Jenis transportasi tidak ditemukan dalam database',
        ];
    }
    
    public function import($file)
    {
        try {
            DB::beginTransaction();
            
            // Reset counters before import
            $this->totalRows = 0;
            $this->failures = [];
            $this->imported = [];
            
            // Import data secara langsung dari array
            Excel::import($this, $file);
            
            Log::info("Import completed. Successfully imported: " . count($this->imported) . " records");
            
            // Simpan semua cuti yang diimpor ke database
            foreach ($this->imported as $importItem) {
                // Ekstrak model dan data transportasi
                $cuti = $importItem['model'];
                $transportData = $importItem['transport'];
                
                // Simpan model cuti
                $cuti->save();
                Log::info("Saved Cuti ID " . $cuti->id . " for Karyawan ID " . $cuti->karyawan_id);
                
                // Buat CutiDetail
                CutiDetail::create([
                    'cuti_id' => $cuti->id,
                    'jenis_cuti_id' => $cuti->jenis_cuti_id,
                    'jumlah_hari' => $cuti->lama_hari
                ]);
                
                Log::info("Created CutiDetail for Cuti ID " . $cuti->id . " with JenisCuti ID " . $cuti->jenis_cuti_id);
                
                // Jika ada data transportasi, buat TransportasiDetail
                if ($transportData) {
                    // Cari ID transportasi berdasarkan jenis
                    $transportasi = Transportasi::where('jenis', $transportData['jenis'])->first();
                    if ($transportasi) {
                        Log::info("Found transportasi: " . $transportasi->jenis . " (ID: " . $transportasi->id . ")");
                        
                        // Buat detail transportasi untuk tiket pergi (berangkat)
                        if (!empty($transportData['rute_pergi_asal']) && !empty($transportData['rute_pergi_tujuan'])) {
                            TransportasiDetail::create([
                                'cuti_id' => $cuti->id,
                                'transportasi_id' => $transportasi->id,
                                'jenis_perjalanan' => 'pergi',
                                'rute_asal' => $transportData['rute_pergi_asal'],
                                'rute_tujuan' => $transportData['rute_pergi_tujuan'],
                                'status_pemesanan' => 'belum_dipesan'
                            ]);
                            Log::info("Created Transportasi Detail (pergi) for Cuti ID " . $cuti->id);
                        }
                        
                        // Buat detail transportasi untuk tiket kembali (pulang)
                        if (!empty($transportData['rute_kembali_asal']) && !empty($transportData['rute_kembali_tujuan'])) {
                            TransportasiDetail::create([
                                'cuti_id' => $cuti->id,
                                'transportasi_id' => $transportasi->id,
                                'jenis_perjalanan' => 'kembali',
                                'rute_asal' => $transportData['rute_kembali_asal'],
                                'rute_tujuan' => $transportData['rute_kembali_tujuan'],
                                'status_pemesanan' => 'belum_dipesan'
                            ]);
                            Log::info("Created Transportasi Detail (kembali) for Cuti ID " . $cuti->id);
                        }
                    } else {
                        Log::warning("Transportasi not found: " . $transportData['jenis']);
                    }
                }
            }
            
            DB::commit();
            
            return [
                'success' => true,
                'imported' => count($this->imported),
                'failures' => $this->failures,
                'total_rows' => $this->totalRows
            ];
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Import cuti error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengimpor: ' . $e->getMessage(),
                'error_detail' => $e->getTraceAsString()
            ];
        }
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->failures[] = [
                'row' => $failure->row(),
                'errors' => $failure->errors()
            ];
        }
    }

    public function getImportedCount()
    {
        return count($this->imported);
    }

    public function getFailures()
    {
        return $this->failures;
    }
    
    public function getTotalRows()
    {
        return $this->totalRows;
    }
    
    // Implementasi batch inserts untuk performa lebih baik
    public function batchSize(): int
    {
        return 500;
    }
    
    // Implementasi chunk reading untuk memori lebih efisien
    public function chunkSize(): int
    {
        return 500;
    }
}