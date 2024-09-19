<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Redirect;
use Validator;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash; // Untuk melakukan hashing pada password
use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\DataCalonNasabahCreateRequest;
use App\Http\Requests\DataCalonNasabahUpdateRequest;
use App\Repositories\DataCalonNasabahRepository;
use App\Validators\DataCalonNasabahValidator;

use App\Services\SendPushNotification;

use Illuminate\Support\Facades\Log;
use Ixudra\Curl\Facades\Curl;

use App\Entities\DataCalonNasabah;
use App\Entities\User;
use App\Entities\Role;
use App\Entities\Merchant;
use App\Entities\CompOption;

class DataCalonNasabahController extends Controller
{
    /**
     * @var DataCalonNasabahRepository
     */
    protected $repository;

    /**
     * @var DataCalonNasabahValidator
     */
    protected $validator;

    /**
     * DataCalonNasabahController constructor.
     *
     * @param DataCalonNasabahRepository $repository
     * @param DataCalonNasabahValidator $validator
     */
    public function __construct(DataCalonNasabahRepository $repository, DataCalonNasabahValidator $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $jumlah_request = DataCalonNasabah::where('status', 0)->count();
        $jumlah_approve = DataCalonNasabah::where('status', 1)->count();

        return view('apps.calon_nasabah.menu', compact('jumlah_request', 'jumlah_approve'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        $data = DataCalonNasabah::select('*');
        $data = $data->whereIn('status', [2, 3]);

        $user = session()->get('user');

        if ($user) {
            $role_user = $user->role_id;
            $role = Role::find($role_user);
    
            if ($role && $role->name == 'Supervisor Cabang') {
                $branch_id = $user->branchid;
                $data->where('branchid', $branch_id);
            }
        }

        if ($request->has('search')) {
            $data = $data->whereRaw('lower(name) like (?)', ["%{$request->search}%"]);
        }

        $total = $data->count();

        if ($request->has('order_type')) {
            if ($request->get('order_type') == 'asc') {
                if ($request->has('order_by')) {
                    $data->orderBy($request->get('order_by'));
                } else {
                    $data->orderBy('request_time');
                }
            } else {
                if ($request->has('order_by')) {
                    $data->orderBy($request->get('order_by'), 'desc');
                } else {
                    $data->orderBy('request_time', 'desc');
                }
            }
        } else {
            $data->orderBy('request_time', 'asc');
        }

        $data = $data->get();

        foreach ($data as $nasabah) {
            if ($nasabah->status == 2) {
                $nasabah->status_text = 'Accepted';
            } else {
                $nasabah->status_text = 'Rejected';
            }
        }

        $user = session()->get('user');

        return view('apps.calon_nasabah.list')
            ->with('data', $data)
            ->with('username', $user->username);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list_request(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        $data = DataCalonNasabah::select('*');
        $data = $data->where('status', 0);

        $user = session()->get('user');

        if ($user) {
            $role_user = $user->role_id;
            $role = Role::find($role_user);
    
            if ($role && $role->name == 'Customer Service Cabang') {
                $branch_id = $user->branchid;
                $data->where('branchid', $branch_id);
            }
        }

        if ($request->has('search')) {
            $data = $data->whereRaw('lower(name) like (?)', ["%{$request->search}%"]);
        }

        $total = $data->count();

        if ($request->has('order_type')) {
            if ($request->get('order_type') == 'asc') {
                if ($request->has('order_by')) {
                    $data->orderBy($request->get('order_by'));
                } else {
                    $data->orderBy('request_time');
                }
            } else {
                if ($request->has('order_by')) {
                    $data->orderBy($request->get('order_by'), 'desc');
                } else {
                    $data->orderBy('request_time', 'desc');
                }
            }
        } else {
            $data->orderBy('request_time', 'asc');
        }

        $data = $data->get();

        $user = session()->get('user');

        return view('apps.calon_nasabah.list-request')
            ->with('data', $data)
            ->with('username', $user->username);
    }

    public function list_approve(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        $data = DataCalonNasabah::select('*');
        $data = $data->where('status', 1);

        $user = session()->get('user');

        if ($user) {
            $role_user = $user->role_id;
            $role = Role::find($role_user);
    
            if ($role && $role->name == 'Supervisor Cabang') {
                $branch_id = $user->branchid;
                $data->where('branchid', $branch_id);
            }
        }

        if ($request->has('search')) {
            $data = $data->whereRaw('lower(name) like (?)', ["%{$request->search}%"]);
        }

        $total = $data->count();

        if ($request->has('order_type')) {
            if ($request->get('order_type') == 'asc') {
                if ($request->has('order_by')) {
                    $data->orderBy($request->get('order_by'));
                } else {
                    $data->orderBy('request_time');
                }
            } else {
                if ($request->has('order_by')) {
                    $data->orderBy($request->get('order_by'), 'desc');
                } else {
                    $data->orderBy('request_time', 'desc');
                }
            }
        } else {
            $data->orderBy('request_time', 'asc');
        }

        $data = $data->get();

        $user = session()->get('user');

        return view('apps.calon_nasabah.list-approve')
            ->with('data', $data)
            ->with('username', $user->username);
    }

    public function store_cif($id)
{
    DB::beginTransaction();
    try {
        $nasabah = DataCalonNasabah::find($id);
        if ($nasabah) {
            $nama_lengkap = $nasabah->nama_lengkap;
            $nama_alias = $nasabah->nama_alias;
            $ibu_kandung = $nasabah->ibu_kandung;
            $tempat_lahir = $nasabah->tempat_lahir;
            $tgl_lahir = $nasabah->tgl_lahir;
            $jenis_kelamin = $nasabah->jenis_kelamin;
            $agama = $nasabah->agama;
            $status_nikah = $nasabah->status_nikah;
            $alamat = $nasabah->alamat;
            $rt = $nasabah->rt;
            $rw = $nasabah->rw;
            $kecamatan = $nasabah->kecamatan;
            $kelurahan = $nasabah->kelurahan;
            $kab_kota = $nasabah->kab_kota;
            $provinsi = $nasabah->provinsi;
            $kode_pos = $nasabah->kode_pos;
            $status_penduduk = $nasabah->status_penduduk;
            $kewarganegaraan = $nasabah->kewarganegaraan;
            $no_telp = $nasabah->no_telp;
            $no_hp = $nasabah->no_hp;
            $npwp = $nasabah->npwp;
            $jenis_identitas = $nasabah->jenis_identitas;
            $no_identitas = $nasabah->no_identitas;
            $golongan_darah = $nasabah->golongan_darah;
            $expired_identitas = $nasabah->expired_identitas;
            $pendidikan_terakhir = $nasabah->pendidikan_terakhir;
            $email = $nasabah->email;
            $branchid = $nasabah->branchid;

            if (!$nasabah) {
                throw new \Exception("Nasabah dengan ID $id tidak ditemukan.");
            }

            // Memvalidasi Branch ID sebelum membuat CIF
            if (empty($nasabah->branchid)) {
                throw new \Exception("Branch ID tidak ditemukan untuk nasabah dengan ID $id. Gagal membuat CIF.");
            }

            // Jika nasabah sudah memiliki CIF, batalkan pembuatan CIF baru
            if (!is_null($nasabah->no_cif)) {
                Log::info("Nasabah dengan ID {$nasabah->id} sudah memiliki CIF: {$nasabah->no_cif}");
                return true; // CIF sudah ada, tidak perlu buat lagi
            }

            $terminal = '353471045058692';
            $dateTime = date("YmdHis");

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://108.137.154.8:8080/ARRest/api/");
            $data = json_encode([
                'msg' => [
                    'msg_id' => "$terminal$dateTime",
                    'msg_ui' => "$terminal",
                    'msg_si' => 'CC0004',
                    'msg_dt' => 'lakupandai|' . $no_identitas . '|' . $nama_lengkap . '|' . $nama_alias . '|' . $ibu_kandung . '|' . $tempat_lahir . '|' . $tgl_lahir . '|' . $jenis_kelamin . '|' .
                        $agama . '|' . $status_nikah . '|' . $alamat . '|' . $rt . '|' . $rw . '|' . $kecamatan . '|' . $kelurahan . '|' . $kab_kota . '|' . $provinsi . '|' . $kode_pos .
                        '|' . $status_penduduk . '|' . $kewarganegaraan . '|' . $no_telp . '|' . $no_hp . '|' . $npwp . '|' . $jenis_identitas . '|' . $golongan_darah . '|' . $expired_identitas .
                        '|' . $pendidikan_terakhir . '|' . $email . '|' . $branchid
                ]
            ]);

            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: text/plain'
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

            $output = curl_exec($ch);
            $err = curl_error($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);

            Log::info('cURL Request URL: ' . $info['url']);
            Log::info('cURL Request Data: ' . $data);
            Log::info('cURL Response: ' . $output);

            // Memproses respons dari API
            $responseArray = json_decode($output, true);

            if ($err) {
                Log::error('cURL Error: ' . $err);
                throw new \Exception('Gagal terhubung ke API CIF: ' . $err);
            }

           // Mengecek apakah ada error dalam respon API
        if (isset($responseArray['screen']['title']) && $responseArray['screen']['title'] === 'Gagal') {
            Log::warning("Gagal membuat CIF untuk nasabah ID: {$nasabah->id}, Pesan: " . $responseArray['screen']['comps']['comp'][0]['comp_values']['comp_value'][0]['value']);
            return false;
        }

        // Jika berhasil, menyimpan CIF ke dalam database dan pindahkan ke tabel users
        if (isset($responseArray['screen']['comps']['comp'])) {
            foreach ($responseArray['screen']['comps']['comp'] as $comp) {
                if ($comp['comp_lbl'] === 'No CIF') {
                    $nasabah->no_cif = $comp['comp_values']['comp_value'][0]['value'];
                    $nasabah->save();

                    // Pindahkan ke tabel users setelah CIF disimpan
                    $this->moveToUsersTable($nasabah);
                }
            }
        }

        DB::commit();
        return true;

    }
 } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error in store_cif: ' . $e->getMessage());
        return false;
    }
}

private function moveToUsersTable($nasabah)
{
    // Validasi email sebelum menyimpan
    if (!filter_var($nasabah->email, FILTER_VALIDATE_EMAIL)) {
        Log::error("Invalid email format for nasabah ID {$nasabah->id}: {$nasabah->email}");
        return;
    }

    // Cek apakah user sudah ada di tabel users berdasarkan email
    $existingUser = User::where('email', $nasabah->email)->first();
    if ($existingUser) {
        Log::info("Nasabah dengan email {$nasabah->email} sudah terdaftar di tabel users.");
        return;
    }

    // Membuat user baru di tabel users
    $user = new User();
    $user->fullname = $nasabah->nama_lengkap; // Menggunakan fullname, bukan name
    $user->email = $nasabah->email;
    $user->password = Hash::make('default_password'); // Gunakan password default atau sesuai kebutuhan
    $user->branchid = $nasabah->branchid;
    $user->save();

    // Hapus nasabah dari tabel DataCalonNasabah
    $nasabah->delete();

    Log::info("Nasabah dengan ID {$nasabah->id} telah dipindahkan ke tabel users.");
}



    public function registration_code($id)
    {
        DB::beginTransaction();
        try {
            $nasabah = DataCalonNasabah::find($id);
            if ($nasabah) {
                do {
                    $no_registrasi = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
                    $exists = DataCalonNasabah::where('no_registrasi', $no_registrasi)->exists();
                } while ($exists);

                $nasabah->no_registrasi = $no_registrasi;
                $nasabah->save();
            }
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error generate: ' . $e->getMessage());
            return false;
        }
    }

    public function store_rekening($id)
    {
        DB::beginTransaction();
        try {
            $nasabah = DataCalonNasabah::find($id);
            if ($nasabah) {
                $nama_lengkap = $nasabah->nama_lengkap;
                $no_cif = $nasabah->no_cif;
                $kode_produk = 36;
                $no_registrasi = $nasabah->no_registrasi;
                $branchid = $nasabah->branchid;

                $terminal = '353471045058692';
                $dateTime = date("YmdHis");

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "http://108.137.154.8:8080/ARRest/api/");
                $data = json_encode([
                    'msg' => [
                        'msg_id' => "$terminal$dateTime",
                        'msg_ui' => "$terminal",
                        'msg_si' => 'CRS004',
                        'msg_dt' => 'lakupandai|' . $nama_lengkap . '|' . $no_cif . '|' . $kode_produk . '|' . $no_registrasi . '|' . $branchid
                    ]
                ]);

                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: text/plain'
                ]);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

                $output = curl_exec($ch);
                $err = curl_error($ch);
                $info = curl_getinfo($ch);
                curl_close($ch);

                Log::info('cURL Request URL: ' . $info['url']);
                Log::info('cURL Request Data: ' . $data);
                Log::info('cURL Response: ' . $output);

                $responseArray = json_decode($output, true);

                if ($err) {
                    Log::error('cURL Error: ' . $err);
                } else {
                    $noRekening = null;
                    if (isset($responseArray['screen']['comps']['comp'])) {
                        foreach ($responseArray['screen']['comps']['comp'] as $comp) {
                            if ($comp['comp_lbl'] === 'No Rekening') {
                                $noRekening = $comp['comp_values']['comp_value'][0]['value'];
                                $nasabah->no_rekening = $noRekening;
                            }
                        }
                    }
                    $nasabah->save();
                }

                if (isset($responseArray['screen']['title']) && $responseArray['screen']['title'] === 'Gagal') {
                    return false;
                } else {
                    DB::commit();
                    return true;
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in store_cif: ' . $e->getMessage());
            return false;
        }
    }

    public function approveNasabah($id)
    {
        DB::beginTransaction(); 
        try {
            $nasabah = DataCalonNasabah::find($id);
    
            if (!$nasabah) {
                return Redirect::to('/nasabah/approve')
                    ->with('error', "Data nasabah tidak ditemukan");
            }
    
            // Proses pembuatan nomor registrasi
            if (!$this->registration_code($id)) {
                DB::rollBack();
                return Redirect::to('/nasabah/approve')
                    ->with('error', "Gagal menghasilkan nomor registrasi.");
            }
    
            // Proses pembuatan CIF
            if (!$this->store_cif($id)) {
                DB::rollBack();
                return Redirect::to('/nasabah/approve')
                    ->with('error', "Gagal membuat CIF.");
            }
    
            // Proses pembuatan rekening
            if (!$this->store_rekening($id)) {
                DB::rollBack();
                return Redirect::to('/nasabah/approve')
                    ->with('error', "Gagal membuat rekening.");
            }
    
            // Menyimpan status nasabah setelah CIF dan rekening berhasil dibuat
            $nasabah->status = 2; // Status 2 = Accepted
            $nasabah->reply_time = now();
            $nasabah->save();
    
            // Ambil FCM token dari tabel data_calon_nasabah
            $fcmToken = $nasabah->fcm_token;
    
            // Validasi token FCM
            if ($this->isValidFcmToken($fcmToken)) {
                // Jika FCM token tersedia, kirim notifikasi
                $notificationService = new SendPushNotification();
                $notificationService->sendNotificationToToken($fcmToken, [
                    'title' => 'Pengajuan Disetujui',
                    'message' => "Pengajuan nasabah atas nama {$nasabah->nama_lengkap} telah disetujui oleh supervisor.",
                ]);
            } else {
                Log::warning("FCM token tidak valid atau tidak ditemukan untuk nasabah ID: {$nasabah->id}");
            }
    
            DB::commit();
            return Redirect::to('/nasabah/approve')->with('success', 'Nasabah berhasil disetujui, CIF dan rekening berhasil dibuat.');
    
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in approveNasabah: ' . $e->getMessage());
            return Redirect::to('/nasabah/approve')
                ->with('error', $e->getMessage());
        }
    }
    
    private function isValidFcmToken($fcmToken)
    {
        return !empty($fcmToken) && preg_match('/^[a-zA-Z0-9\-_:.]+$/', $fcmToken);
    }
    
    public function rejectNasabah($id)
    {
        DB::beginTransaction();
        try {
            $nasabah = DataCalonNasabah::where('id', $id)->first();
            if (!$nasabah) {
                throw new \Exception("nasabah not found");
            }

            $nasabah->status = 3;
            $nasabah->reply_time = now();
            $nasabah->save();

            DB::commit();
            return redirect()->route('nasabah')->with('success', 'Permintaan Berhasil Ditolak.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error : ' . $e->getMessage());
            return Redirect::to('nasabah')
                ->with('error', $e->getMessage());
        }
    }

    public function acceptNasabah($id)
    {
        DB::beginTransaction();
        try {
            $nasabah = DataCalonNasabah::where('id', $id)->first();
            if (!$nasabah) {
                throw new \Exception("nasabah not found");
            }

            $nasabah->status = 1;
            $nasabah->save();

            DB::commit();
            return redirect()->route('nasabah')->with('success', 'Permintaan Berhasil Diterima.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error : ' . $e->getMessage());
            return Redirect::to('nasabah')
                ->with('error', $e->getMessage());
        }
    }

    public function detailRequest($id)
    {
        $nasabah = DataCalonNasabah::find($id);
        if ($nasabah) {
            $jenis_kelamin = CompOption::where('comp_id', 'CIF05')->get();
            $agama = CompOption::where('comp_id', 'CIF06')->get();
            $status_nikah = CompOption::where('comp_id', 'CIF07')->get();
            $status_penduduk = CompOption::where('comp_id', 'CIF16')->get();
            $kewarganegaraan = CompOption::where('comp_id', 'CIF17')->get();
            $jenis_identitas = CompOption::where('comp_id', 'CIF21')->get();
            $pendidikan_terakhir = CompOption::where('comp_id', 'CIF25')->get();
            $kab_kota = CompOption::where('comp_id', 'CIF13')->get();
            $provinsi = CompOption::where('comp_id', 'CIF14')->get();
            $golongan_darah = CompOption::where('comp_id', 'CIF23')->get();

            return view('apps.calon_nasabah.detail-request')
                ->with('nasabah', $nasabah)
                ->with('jenis_kelamin', $jenis_kelamin)
                ->with('agama', $agama)
                ->with('status_nikah', $status_nikah)
                ->with('status_penduduk', $status_penduduk)
                ->with('kewarganegaraan', $kewarganegaraan)
                ->with('jenis_identitas', $jenis_identitas)
                ->with('pendidikan_terakhir', $pendidikan_terakhir)
                ->with('kab_kota', $kab_kota)
                ->with('provinsi', $provinsi)
                ->with('golongan_darah', $golongan_darah);
        } else {
            return Redirect::to('nasabah_request')
                ->with('error', 'Data not found');
        }
    }
    public function detailApprove($id)
    {
        $nasabah = DataCalonNasabah::find($id);
        if ($nasabah) {
            $jenis_kelamin = CompOption::where('comp_id', 'CIF05')->get();
            $agama = CompOption::where('comp_id', 'CIF06')->get();
            $status_nikah = CompOption::where('comp_id', 'CIF07')->get();
            $status_penduduk = CompOption::where('comp_id', 'CIF16')->get();
            $kewarganegaraan = CompOption::where('comp_id', 'CIF17')->get();
            $jenis_identitas = CompOption::where('comp_id', 'CIF21')->get();
            $pendidikan_terakhir = CompOption::where('comp_id', 'CIF25')->get();
            $kab_kota = CompOption::where('comp_id', 'CIF13')->get();
            $provinsi = CompOption::where('comp_id', 'CIF14')->get();
            $golongan_darah = CompOption::where('comp_id', 'CIF23')->get();

            return view('apps.calon_nasabah.detail-approve')
                ->with('nasabah', $nasabah)
                ->with('jenis_kelamin', $jenis_kelamin)
                ->with('agama', $agama)
                ->with('status_nikah', $status_nikah)
                ->with('status_penduduk', $status_penduduk)
                ->with('kewarganegaraan', $kewarganegaraan)
                ->with('jenis_identitas', $jenis_identitas)
                ->with('pendidikan_terakhir', $pendidikan_terakhir)
                ->with('kab_kota', $kab_kota)
                ->with('provinsi', $provinsi)
                ->with('golongan_darah', $golongan_darah);
        } else {
            return Redirect::to('list_approve')
                ->with('error', 'Data not found');
        }
    }

    public function detail($id)
    {
        $nasabah = DataCalonNasabah::find($id);
        if ($nasabah) {
            $jenis_kelamin = CompOption::where('comp_id', 'CIF05')->get();
            $agama = CompOption::where('comp_id', 'CIF06')->get();
            $status_nikah = CompOption::where('comp_id', 'CIF07')->get();
            $status_penduduk = CompOption::where('comp_id', 'CIF16')->get();
            $kewarganegaraan = CompOption::where('comp_id', 'CIF17')->get();
            $jenis_identitas = CompOption::where('comp_id', 'CIF21')->get();
            $pendidikan_terakhir = CompOption::where('comp_id', 'CIF25')->get();
            $kab_kota = CompOption::where('comp_id', 'CIF13')->get();
            $provinsi = CompOption::where('comp_id', 'CIF14')->get();
            $golongan_darah = CompOption::where('comp_id', 'CIF23')->get();

            return view('apps.calon_nasabah.detail')
                ->with('nasabah', $nasabah)
                ->with('jenis_kelamin', $jenis_kelamin)
                ->with('agama', $agama)
                ->with('status_nikah', $status_nikah)
                ->with('status_penduduk', $status_penduduk)
                ->with('kewarganegaraan', $kewarganegaraan)
                ->with('jenis_identitas', $jenis_identitas)
                ->with('pendidikan_terakhir', $pendidikan_terakhir)
                ->with('kab_kota', $kab_kota)
                ->with('provinsi', $provinsi)
                ->with('golongan_darah', $golongan_darah);
        } else {
            return Redirect::to('nasabah_list')
                ->with('error', 'Data not found');
        }
    }

    public function listJson(Request $request, $kode_agen)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        $data = DataCalonNasabah::select('*')
            ->where('kode_agen', $kode_agen)
            ->orderBy('request_time', 'asc');

        $data = $data->get();

        return response()->json([
            'status' => true,
            'data' => $data,
            'username' => $user->username,
        ]);
    }

    public function send_sms($id)
    {
        DB::beginTransaction();
        try {
            $nasabah = DataCalonNasabah::find($id);
            if ($nasabah) {
                $nama_lengkap = $nasabah->nama_lengkap;
                $no_rek = $nasabah->no_rekening;
                $no_hp = $nasabah->no_hp;
                $reply_time = $nasabah->reply_time;

                $message = "Pembuatan Rekening BSA Berhasil an: {$nama_lengkap}, NoRek : {$no_rek}, No HP: {$no_hp}, Date : {$reply_time}";

                $terminal = '353471045058692';
                $dateTime = date("YmdHis");

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "http://108.137.154.8:8080/ARRest/api/");
                $data = json_encode([
                    'msg' => [
                        'msg_id' => "$terminal$dateTime",
                        'msg_ui' => "$terminal",
                        'msg_si' => 'OTN005',
                        'msg_dt' => 'lakupandai|' . $no_hp . '|' . $nama_lengkap . '|' . $message
                    ]
                ]);

                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: text/plain'
                ]);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

                $output = curl_exec($ch);
                $err = curl_error($ch);
                $info = curl_getinfo($ch);
                curl_close($ch);

                Log::info('cURL Request URL: ' . $info['url']);
                Log::info('cURL Request Data: ' . $data);
                Log::info('cURL Response: ' . $output);

                $responseArray = json_decode($output, true);

                if ($err) {
                    Log::error('cURL Error: ' . $err);
                    return response()->json(['error' => 'cURL Error: ' . $err], 500);
                } else {
                    $noRekening = null;
                    if (isset($responseArray['screen']['comps']['comp'])) {
                        foreach ($responseArray['screen']['comps']['comp'] as $comp) {
                            if ($comp['comp_lbl'] === 'No Rekening') {
                                $noRekening = $comp['comp_values']['comp_value'][0]['value'];
                                $nasabah->no_rekening = $noRekening;
                            }
                        }
                    }
                    $nasabah->save();
                }

                if (isset($responseArray['screen']['title']) && $responseArray['screen']['title'] === 'Gagal') {
                    DB::rollBack();
                    return response()->json(['status' => 'failed'], 400);
                } else {
                    DB::commit();
                    return response()->json(['status' => 'success'], 200);
                }
            } else {
                return response()->json(['error' => 'Nasabah not found'], 404);
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in store_cif: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error'], 500);
        }
    }

    public function sendTestNotification(Request $request)
{
    try {
        // Gunakan token FCM manual yang sudah di-set default di service
        $notificationService = new SendPushNotification();
        $notificationService->sendNotification();
        return response()->json(['status' => 'success', 'message' => 'Notifikasi berhasil dikirim']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => 'Gagal mengirim notifikasi'], 500);
    }
}
}
