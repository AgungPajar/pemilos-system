<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PklStudent;
use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PklStudentController extends Controller
{
    /**
     * Display PKL student list along with import/export utilities.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $studentsQuery = PklStudent::with(['token.paslon'])
            ->orderBy('kelas')
            ->orderBy('name');

        if ($search) {
            $studentsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('nis', 'like', '%' . $search . '%')
                    ->orWhere('kelas', 'like', '%' . $search . '%');
            });
        }

        $students = $studentsQuery->paginate(20)->withQueryString();

        $total = PklStudent::count();
        $withToken = PklStudent::whereNotNull('token_id')->count();
        $usedTokens = Token::whereIn('id', PklStudent::whereNotNull('token_id')->pluck('token_id'))
            ->whereNotNull('used_at')
            ->count();

        return view('admin.pkl_students.index', [
            'students' => $students,
            'search' => $search,
            'total' => $total,
            'withToken' => $withToken,
            'usedTokens' => $usedTokens,
        ]);
    }

    /**
     * Store or update a PKL student entry.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $studentId = $request->input('student_id');

        $data = $request->validate([
            'student_id' => ['nullable', 'uuid', 'exists:pkl_students,id'],
            'name' => ['required', 'string', 'max:255'],
            'nis' => [
                'required',
                'string',
                'max:100',
                Rule::unique('pkl_students', 'nis')->ignore($studentId),
            ],
            'jk' => ['nullable', 'string', 'max:10'],
            // removed nisn and tmp_lahir fields
            'tgl_lahir' => ['required', 'date'],
            'kelas' => ['required', 'string', 'max:100'],
        ]);

        $data['nis'] = trim($data['nis']);

        if (! empty($data['jk'])) {
            $data['jk'] = strtoupper(substr($data['jk'], 0, 1));
        }


        DB::transaction(function () use ($studentId, $data) {
            if ($studentId) {
                $student = PklStudent::lockForUpdate()->findOrFail($studentId);
            } else {
                $student = new PklStudent();
            }

            $token = $student->token;

            if (! $token) {
                $token = $this->issueToken();
            }

                $student->fill([
                'name' => trim($data['name']),
                'nis' => $data['nis'],
                'jk' => $data['jk'],
                'tgl_lahir' => Carbon::parse($data['tgl_lahir'])->toDateString(),
                'kelas' => trim($data['kelas']),
                'token_id' => $token->id,
            ]);

            $student->save();
        });

        return redirect()
            ->route('admin.pkl-students.index')
            ->with('success', 'Data siswa PKL berhasil disimpan.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PklStudent  $pklStudent
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(PklStudent $pklStudent)
    {
        if ($pklStudent->token && $pklStudent->token->isUsed()) {
            return redirect()
                ->route('admin.pkl-students.index')
                ->with('error', 'Tidak dapat menghapus siswa PKL yang sudah menggunakan token.');
        }

        if ($pklStudent->token) {
            $pklStudent->token->delete();
        }

        $pklStudent->delete();

        return redirect()
            ->route('admin.pkl-students.index')
            ->with('success', 'Data siswa PKL berhasil dihapus.');
    }

    /**
     * Download CSV template for PKL import.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function template()
    {
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="template_siswa_pkl.xls"',
        ];

        $columns = [
            'nama',
            'nis',
            'jk',
            'tgl_lahir (dd-mm-yyyy)',
            'kelas',
        ];

        $xml = $this->generateSpreadsheetTemplate($columns);

        return response($xml, 200, $headers);
    }

    /**
     * Import PKL student data from CSV.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xls,xlsx,xml'],
        ]);

        $path = $request->file('file')->getRealPath();
        $extension = strtolower($request->file('file')->getClientOriginalExtension());

        if ($extension === 'xlsx') {
            [$header, $rows] = $this->readXlsxRows($path);
        } elseif (in_array($extension, ['xls', 'xml'])) {
            [$header, $rows] = $this->readSpreadsheetRows($path);

            if (! $header) {
                [$header, $rows] = $this->readXlsxRows($path);
            }
        } else {
            [$header, $rows] = $this->readCsvRows($path);
        }

        if (! $header) {
            return redirect()->route('admin.pkl-students.index')->with('error', 'Berkas tidak memiliki header.');
        }

        $normalizedHeader = array_map(function ($value) {
            if ($value === null) {
                return null;
            }

            // Remove UTF-8 BOM and other control characters, normalize whitespace
            $v = $value;

            // Remove BOM (ZERO WIDTH NO-BREAK SPACE / Byte Order Mark)
            $v = preg_replace('/^\x{FEFF}/u', '', $v);

            // Replace NBSP (0xC2A0) with normal space
            $v = str_replace("\xc2\xa0", ' ', $v);

            // Strip other C0/C1 control chars
            $v = preg_replace('/[\x00-\x1F\x7F-\x9F]/u', '', $v);

            // Normalize internal whitespace and trim
            $v = preg_replace('/\s+/u', ' ', trim($v));

            return strtolower($v);
        }, $header);

        $map = [
            'nama' => 'name',
            'nis' => 'nis',
            'jk' => 'jk',
            'tgl_lahir (dd-mm-yyyy)' => 'tgl_lahir',
            'tgl_lahir' => 'tgl_lahir',
            'kelas' => 'kelas',
            'token_code' => 'token_code',
            'token' => 'token_code',
        ];

        // Resolver: try exact map first, then substring heuristics so variations like
        // "tgl lahir (yyyy)" or "tanggal_lahir" are handled.
        $resolveHeader = function (?string $headerKey) use ($map) {
            if (! $headerKey) {
                return null;
            }

            $original = $headerKey;

            // try exact
            if (isset($map[$original])) {
                return $map[$original];
            }

            // clean to alnum + spaces
            $clean = preg_replace('/[^a-z0-9]/', ' ', $original);
            $clean = trim(preg_replace('/\s+/u', ' ', $clean));

            // try map with underscores
            $underscore = str_replace(' ', '_', $clean);
            if (isset($map[$underscore])) {
                return $map[$underscore];
            }

            // heuristics
            if (strpos($clean, 'nis') !== false || strpos($clean, 'nip') !== false) {
                return 'nis';
            }
            // nisn and tmp_lahir removed from schema; ignore
            if (strpos($clean, 'tgl') !== false || strpos($clean, 'tanggal') !== false || strpos($clean, 'lahir') !== false) {
                return 'tgl_lahir';
            }
            if (strpos($clean, 'kelas') !== false) {
                return 'kelas';
            }
            if (strpos($clean, 'token') !== false || strpos($clean, 'kode') !== false) {
                return 'token_code';
            }

            return null;
        };

        // Pre-resolve header fields for faster mapping per-row
        $resolvedHeader = [];
        foreach ($normalizedHeader as $h) {
            $resolvedHeader[] = $resolveHeader($h);
        }

        $created = 0;
        $updated = 0;
        $errors = [];

        foreach ($rows as $row) {
            if (count(array_filter($row)) === 0) {
                continue;
            }

            $rowData = [];
            foreach ($row as $index => $value) {
                // use resolved header field name (e.g., 'name', 'nip')
                $field = $resolvedHeader[$index] ?? null;

                if ($field) {
                    $rowData[$field] = trim($value);
                }
            }

            if (empty($rowData['nis']) || empty($rowData['name']) || empty($rowData['tgl_lahir']) || empty($rowData['kelas'])) {
                $errors[] = 'Baris dengan data ' . json_encode($rowData) . ' dilewati karena data wajib tidak lengkap.';
                continue;
            }

            try {
                $raw = $rowData['tgl_lahir'];

                // If numeric (possible Excel serial), convert from Excel serial date (Excel's epoch: 1899-12-30)
                if (is_numeric($raw) && intval($raw) == $raw) {
                    // Excel serial may be integer; convert to DateTime
                    // Excel's day 1 = 1899-12-31 but PHP uses 1970 epoch; compute via DateInterval
                    $base = Carbon::createFromDate(1899, 12, 30)->startOfDay();
                    $birthDate = $base->addDays(intval($raw))->toDateString();
                } else {
                    $raw = trim((string) $raw);

                    // Try common patterns: d-m-Y, d/m/Y, Y-m-d, Y/m/d
                    $formats = ['d-m-Y', 'd/m/Y', 'Y-m-d', 'Y/m/d'];
                    $dt = null;
                    foreach ($formats as $fmt) {
                        try {
                            $dt = Carbon::createFromFormat($fmt, $raw);
                            if ($dt) break;
                        } catch (\Exception $e) {
                            $dt = null;
                        }
                    }

                    if (! $dt) {
                        // Fallback to Carbon's parser
                        $dt = Carbon::parse($raw);
                    }

                    $birthDate = $dt->toDateString();
                }
            } catch (\Throwable $e) {
                $errors[] = 'Tanggal lahir tidak valid untuk NIS ' . ($rowData['nis'] ?? json_encode($rowData));
                continue;
            }

            $gender = $rowData['jk'] ?? null;
            if ($gender !== null) {
                $gender = strtoupper(substr($gender, 0, 1));
            }

            $name = trim($rowData['name']);
            $class = trim($rowData['kelas']);

            $rowData['nis'] = trim($rowData['nis']);
            $preferredTokenCode = $rowData['token_code'] ?? null;

            DB::transaction(function () use ($rowData, $name, $gender, $birthDate, $class, $preferredTokenCode, &$created, &$updated) {
                $existing = PklStudent::lockForUpdate()->where('nis', $rowData['nis'])->first();

                if ($existing) {
                    $token = $existing->token ?: $this->issueToken($preferredTokenCode);

                    $existing->update([
                        'name' => $name,
                        'jk' => $gender,
                        'tgl_lahir' => $birthDate,
                        'kelas' => $class,
                        'token_id' => $token->id,
                    ]);

                    $updated++;
                } else {
                    $token = $this->issueToken($preferredTokenCode);

                    PklStudent::create([
                        'name' => $name,
                        'nis' => trim($rowData['nis']),
                        'jk' => $gender,
                        'tgl_lahir' => $birthDate,
                        'kelas' => $class,
                        'token_id' => $token->id,
                    ]);

                    $created++;
                }
            });
        }

        $message = "{$created} siswa ditambahkan, {$updated} diperbarui.";

        return redirect()
            ->route('admin.pkl-students.index')
            ->with('success', $message)
            ->with('import_errors', $errors);
    }

    /**
     * Generate SpreadsheetML template content.
     *
     * @param  array<int, string>  $columns
     * @return string
     */
    protected function generateSpreadsheetTemplate(array $columns): string
    {
        $cells = collect($columns)->map(function ($column) {
            return sprintf('<Cell><Data ss:Type="String">%s</Data></Cell>', htmlspecialchars($column, ENT_XML1));
        })->implode('');

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <Worksheet ss:Name="Template">
  <Table>
   <Row>
    {$cells}
   </Row>
  </Table>
 </Worksheet>
</Workbook>
XML;
    }

    /**
     * Read rows from a CSV file.
     *
     * @param  string  $path
     * @return array{0: array<int, string>|null, 1: array<int, array<int, string>>}
     */
    protected function readCsvRows(string $path): array
    {
        $handle = fopen($path, 'r');

        if (! $handle) {
            return [null, []];
        }

        $header = fgetcsv($handle) ?: null;
        $rows = [];

        if ($header) {
            while (($row = fgetcsv($handle)) !== false) {
                $rows[] = $row;
            }
        }

        fclose($handle);

        return [$header, $rows];
    }

    /**
     * Read rows from a SpreadsheetML (.xls) file generated by this application.
     *
     * @param  string  $path
     * @return array{0: array<int, string>|null, 1: array<int, array<int, string>>}
     */
    protected function readSpreadsheetRows(string $path): array
    {
        $xml = @simplexml_load_file($path);

        if (! $xml) {
            return [null, []];
        }

        $xml->registerXPathNamespace('ss', 'urn:schemas-microsoft-com:office:spreadsheet');
        $rowsXml = $xml->xpath('//ss:Worksheet[1]/ss:Table/ss:Row');

        if (! $rowsXml || count($rowsXml) === 0) {
            return [null, []];
        }

        $rows = [];

        foreach ($rowsXml as $rowXml) {
            $cells = $rowXml->xpath('ss:Cell/ss:Data');
            $row = [];

            if ($cells) {
                foreach ($cells as $cell) {
                    $row[] = trim((string) $cell);
                }
            }

            $rows[] = $row;
        }

        $header = $rows[0] ?? null;
        $dataRows = array_slice($rows, 1);

        return [$header, $dataRows];
    }

    /**
     * Read rows from an XLSX workbook (first worksheet).
     *
     * @param  string  $path
     * @return array{0: array<int, string>|null, 1: array<int, array<int, string>>}
     */
    protected function readXlsxRows(string $path): array
    {
        $zip = new \ZipArchive();

        if ($zip->open($path) !== true) {
            return [null, []];
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');

        if (! $sheetXml) {
            $zip->close();
            return [null, []];
        }

        $sharedStrings = [];
        if ($sharedXml = $zip->getFromName('xl/sharedStrings.xml')) {
            $strings = @simplexml_load_string($sharedXml);
            if ($strings) {
                $strings->registerXPathNamespace('s', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                foreach ($strings->xpath('//s:si') as $item) {
                    $text = '';
                    if (isset($item->t)) {
                        $text = (string) $item->t;
                    } elseif (isset($item->r)) {
                        foreach ($item->r as $segment) {
                            $text .= (string) $segment->t;
                        }
                    }
                    $sharedStrings[] = $text;
                }
            }
        }

        $sheet = @simplexml_load_string($sheetXml);
        $zip->close();

        if (! $sheet) {
            return [null, []];
        }

        $sheet->registerXPathNamespace('s', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $rowsXml = $sheet->xpath('//s:worksheet/s:sheetData/s:row');

        if (! $rowsXml) {
            return [null, []];
        }

        $rows = [];

        foreach ($rowsXml as $rowXml) {
            $cells = [];

            foreach ($rowXml->c as $cell) {
                $index = $this->columnToIndex((string) $cell['r']);
                $type = (string) $cell['t'];
                $value = '';

                if ($type === 's') {
                    $sharedIndex = (int) $cell->v;
                    $value = $sharedStrings[$sharedIndex] ?? '';
                } elseif ($type === 'inlineStr') {
                    if (isset($cell->is->t)) {
                        $value = (string) $cell->is->t;
                    } elseif (isset($cell->is->r)) {
                        foreach ($cell->is->r as $segment) {
                            $value .= (string) $segment->t;
                        }
                    }
                } else {
                    $value = isset($cell->v) ? (string) $cell->v : '';
                }

                $cells[$index] = trim($value);
            }

            if (! empty($cells)) {
                $row = [];
                $maxIndex = max(array_keys($cells));
                for ($i = 0; $i <= $maxIndex; $i++) {
                    $row[] = $cells[$i] ?? '';
                }
                $rows[] = $row;
            }
        }

        if (empty($rows)) {
            return [null, []];
        }

        $header = $rows[0];
        $dataRows = array_slice($rows, 1);

        return [$header, $dataRows];
    }

    /**
     * Convert column reference (e.g., "A1") to zero-based column index.
     *
     * @param  string  $cellReference
     * @return int
     */
    protected function columnToIndex(string $cellReference): int
    {
        if (! preg_match('/([A-Z]+)[0-9]+/i', $cellReference, $matches)) {
            return 0;
        }

        $letters = strtoupper($matches[1]);
        $index = 0;

        for ($i = 0, $len = strlen($letters); $i < $len; $i++) {
            $index = $index * 26 + (ord($letters[$i]) - ord('A') + 1);
        }

        return $index - 1;
    }

    /**
     * Issue a fresh token for a PKL student.
     *
     * @param  string|null  $preferredCode
     * @return \App\Models\Token
     */
    protected function issueToken(?string $preferredCode = null): Token
    {
        $preferred = $preferredCode ? strtoupper(trim($preferredCode)) : null;

        if ($preferred) {
            $preferred = preg_replace('/[^A-Z0-9]/', '', $preferred);
        }

        if ($preferred && ! Token::where('code', $preferred)->exists()) {
            return Token::create([
                'code' => $preferred,
                'note' => 'Token PKL',
            ]);
        }

        do {
            $code = strtoupper(Str::random(6));
        } while (Token::where('code', $code)->exists());

        return Token::create([
            'code' => $code,
            'note' => 'Token PKL',
        ]);
    }
}
