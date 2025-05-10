<?php

namespace App\Imports;

use App\Models\Karyawan;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Carbon\Carbon;

class KaryawanImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure
{
    protected $failures = [];
    protected $imported = 0;
    protected $updated = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                // Pastikan NIK selalu dalam format string
                $nik = isset($row['nik']) ? (string)$row['nik'] : '';
                
                if (empty($nik)) {
                    continue; // Skip jika NIK kosong
                }
                
                // Konversi format DOH ke YYYY-MM-DD untuk database
                $doh = null;
                if (!empty($row['doh'])) {
                    try {
                        Log::debug('Processing DOH value', [
                            'original' => $row['doh'],
                            'type' => gettype($row['doh'])
                        ]);
                        
                        // Penanganan berbagai kemungkinan format tanggal dari Excel
                        if (is_string($row['doh'])) {
                            // Format string
                            if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $row['doh'])) {
                                // Format dd/mm/yyyy
                                $doh = Carbon::createFromFormat('d/m/Y', $row['doh'])->format('Y-m-d');
                            } elseif (preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $row['doh'])) {
                                // Format yyyy-mm-dd (sudah benar untuk database)
                                $doh = $row['doh'];
                            } else {
                                // Format lain, coba parse dan konversi
                                $doh = Carbon::parse($row['doh'])->format('Y-m-d');
                            }
                        } elseif ($row['doh'] instanceof \DateTime) {
                            // Object DateTime
                            $doh = Carbon::instance($row['doh'])->format('Y-m-d');
                        } elseif (is_numeric($row['doh'])) {
                            // Excel serial date
                            $doh = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['doh']))->format('Y-m-d');
                        }
                        
                        Log::debug('Converted DOH value', ['converted' => $doh]);
                    } catch (Exception $e) {
                        // Jika semua cara gagal, simpan nilai asli (jika string) atau null
                        $doh = is_string($row['doh']) ? $row['doh'] : null;
                        Log::debug('DOH conversion failed', [
                            'error' => $e->getMessage(),
                            'using_original' => $doh
                        ]);
                    }
                }
                
                Log::debug('POH value', [
                    'value' => $row['poh'] ?? 'not set',
                    'type' => isset($row['poh']) ? gettype($row['poh']) : 'null'
                ]);
                
                // Cek apakah karyawan dengan NIK ini sudah ada
                $existingKaryawan = Karyawan::where('nik', $nik)->first();
                
                // Data untuk diisi/update
                $karyawanData = [
                    'nama' => $row['nama'] ?? '',
                    'nik' => $nik,
                    'departemen' => $row['departemen'] ?? '',
                    'jabatan' => $row['jabatan'] ?? '',
                    'doh' => $doh,
                    'poh' => $row['poh'] ?? null,
                    'status' => $row['status'] ?? null,
                    'email' => $row['email'] ?? null,
                ];
                
                Log::debug('Data for update/create', $karyawanData);
                
                if ($existingKaryawan) {
                    // Update jika data sudah ada
                    Log::debug('Updating existing karyawan', [
                        'id' => $existingKaryawan->id,
                        'nik' => $existingKaryawan->nik,
                        'before_doh' => $existingKaryawan->doh,
                        'before_poh' => $existingKaryawan->poh
                    ]);
                    
                    $existingKaryawan->update($karyawanData);
                    
                    // Verify update was successful
                    $refreshedKaryawan = Karyawan::find($existingKaryawan->id);
                    Log::debug('After update', [
                        'id' => $refreshedKaryawan->id,
                        'nik' => $refreshedKaryawan->nik,
                        'after_doh' => $refreshedKaryawan->doh,
                        'after_poh' => $refreshedKaryawan->poh,
                        'success' => ($refreshedKaryawan->doh == $doh && $refreshedKaryawan->poh == $row['poh'])
                    ]);
                    
                    $this->updated++;
                } else {
                    // Create jika data belum ada
                    $newKaryawan = Karyawan::create($karyawanData);
                    Log::debug('Created new karyawan', [
                        'id' => $newKaryawan->id,
                        'nik' => $newKaryawan->nik,
                        'doh' => $newKaryawan->doh,
                        'poh' => $newKaryawan->poh
                    ]);
                    $this->imported++;
                }
                
            } catch (Exception $e) {
                Log::error('Import row error', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'row_data' => $row->toArray()
                ]);
                
                $this->failures[] = [
                    'row' => $row->getIndex() + 2, // +2 karena header dan 0-index
                    'errors' => [$e->getMessage()]
                ];
            }
        }
    }
    
    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'nik' => 'required|max:255',  // Hapus unique validation
            'departemen' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'doh' => 'nullable',
            'poh' => 'nullable|string|max:255',
            'status' => 'nullable|in:Staff,Non Staff',
            'email' => 'nullable|email',  // Hapus unique validation
        ];
    }
    
    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'nik.required' => 'NIK harus diisi',
            'nik.max' => 'NIK maksimal 255 karakter',
            'doh.date' => 'Format DOH harus tanggal yang valid (DD/MM/YYYY)',
            'status.in' => 'Status harus salah satu dari: Staff, Non Staff',
        ];
    }
    
    /**
     * Custom validation
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $rows = $validator->getData();
            
            foreach($rows as $index => $row) {
                // Cek duplikat email jika email diisi dan bukan untuk update data yang sama
                if (!empty($row['email'])) {
                    $existingEmail = Karyawan::where('email', $row['email'])->first();
                    if ($existingEmail) {
                        // Periksa apakah ini untuk karyawan yang berbeda
                        $nik = isset($row['nik']) ? (string)$row['nik'] : '';
                        if ($existingEmail->nik !== $nik) {
                            $validator->errors()->add($index.'.email', 'Email sudah digunakan oleh karyawan lain');
                        }
                    }
                }
                
                // Validasi format tanggal DOH dengan penanganan berbagai format
                if (!empty($row['doh'])) {
                    if (is_string($row['doh'])) {
                        // Jika tanggal dalam format string
                        try {
                            // Coba parse berbagai format tanggal yang mungkin
                            if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $row['doh'])) {
                                // Format dd/mm/yyyy
                                Carbon::createFromFormat('d/m/Y', $row['doh']);
                            } elseif (preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $row['doh'])) {
                                // Format yyyy-mm-dd
                                Carbon::createFromFormat('Y-m-d', $row['doh']);
                            } else {
                                // Coba format lain
                                Carbon::parse($row['doh']);
                            }
                        } catch (Exception $e) {
                            $validator->errors()->add($index.'.doh', 'Format DOH harus tanggal yang valid (DD/MM/YYYY). Error: ' . $e->getMessage());
                        }
                    } elseif ($row['doh'] instanceof \DateTime) {
                        // Jika Excel mengirim sebagai objek DateTime, sudah valid
                    } elseif (is_numeric($row['doh'])) {
                        // Jika Excel mengirim sebagai timestamp (serial number)
                        try {
                            // Konversi Excel serial date ke Carbon
                            // Excel serial date dimulai dari 1-Jan-1900, sedangkan Unix timestamp dari 1-Jan-1970
                            Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['doh']));
                        } catch (Exception $e) {
                            $validator->errors()->add($index.'.doh', 'Format DOH tidak valid (nilai numerik). Error: ' . $e->getMessage());
                        }
                    } else {
                        $validator->errors()->add($index.'.doh', 'Format DOH tidak dikenali. Tipe data: ' . gettype($row['doh']));
                    }
                }
            }
        });
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

    public function import($file)
    {
        try {
            DB::beginTransaction();
            
            Excel::import($this, $file);
            
            DB::commit();
            
            return [
                'success' => true,
                'imported' => $this->imported,
                'updated' => $this->updated,
                'failures' => $this->failures
            ];
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Import error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengimpor: ' . $e->getMessage(),
                'error_detail' => $e->getTraceAsString()
            ];
        }
    }

    public function getImportedCount()
    {
        return $this->imported;
    }
    
    public function getUpdatedCount()
    {
        return $this->updated;
    }

    public function getFailures()
    {
        return $this->failures;
    }
}