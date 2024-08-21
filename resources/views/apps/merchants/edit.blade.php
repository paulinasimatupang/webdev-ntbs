@extends('layouts.master')
@section('main-content')
    <div class="breadcrumb">
                <h1>Edit Merchant</h1>
                <ul>
                    <li><a href="">Selada</a></li>
                   
                </ul>
            </div>
            <div class="separator-breadcrumb border-top"></div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-5">
                        <div class="card-body">
                            <form action="{{route('merchant_update',[$merchant->id])}}" method="POST">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Merchant ID</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{{ $merchant->mid }}" name="mid" placeholder="Merchant ID" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Nama Lengkap</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="fullname" name="fullname" value="{{ $merchant->name }}" placeholder="Nama Lengkap" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Email</label>
                                    <div class="col-sm-10">
                                        <input type="email" class="form-control" value="{{ $merchant->email }}" name="email" placeholder="Email" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Nomor Handphone</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{{ $merchant->phone }}" name="phone" placeholder="Phone" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Alamat</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{{ $merchant->address }}" name="address" placeholder="Address" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Kota</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{{ $merchant->city }}" name="city" placeholder="City" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Nomor Rekening</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="no" name="no" value="{{ $merchant->no }}" placeholder="Nomor Rekening" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Nomor Registrasi</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="no_registrasi" name="no_registrasi" value="{{ $merchant->no_registrasi }}" placeholder="Nomor Registrasi" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Nomor CIF</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{{ $merchant->no_cif }}" name="no_cif" placeholder="Nomor CIF" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Kode Cabang</label>
                                            <div class="col-sm-10">
                                                <select class="form-control" name="branchid">
                                                    <option value="">-- Pilih Kode Cabang --</option>
                                                    <option value="000" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '000' ? 'selected' : '' }}>KANTOR PUSAT</option>
                                                    <option value="001" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '001' ? 'selected' : '' }}>KC ISLAMIC CENTER</option>
                                                    <option value="002" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '002' ? 'selected' : '' }}>KC SELONG PAHLAWAN</option>
                                                    <option value="003" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '003' ? 'selected' : '' }}>KC PRAYA SUDIRMAN</option>
                                                    <option value="004" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '004' ? 'selected' : '' }}>KC SUMBAWA DR. WAHIDIN</option>
                                                    <option value="005" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '005' ? 'selected' : '' }}>KC BIMA SOEKARNO HATTA</option>
                                                    <option value="006" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '006' ? 'selected' : '' }}>KCP MATARAM CAKRANEGARA</option>
                                                    <option value="007" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '007' ? 'selected' : '' }}>KC DOMPU NUSANTARA</option>
                                                    <option value="008" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '008' ? 'selected' : '' }}>KC TANJUNG</option>
                                                    <option value="009" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '009' ? 'selected' : '' }}>KCP ALAS</option>
                                                    <option value="010" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '010' ? 'selected' : '' }}>KCP NARMADA</option>
                                                    <option value="011" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '011' ? 'selected' : '' }}>KCP AIKMEL</option>
                                                    <option value="012" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '012' ? 'selected' : '' }}>KCP SWETA</option>
                                                    <option value="013" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '013' ? 'selected' : '' }}>KC TENTE</option>
                                                    <option value="014" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '014' ? 'selected' : '' }}>KCP BOLO</option>
                                                    <option value="015" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '015' ? 'selected' : '' }}>KCP KOPANG</option>
                                                    <option value="016" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '016' ? 'selected' : '' }}>KCP KERUAK</option>
                                                    <option value="017" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '017' ? 'selected' : '' }}>KC TALIWANG SUDIRMAN</option>
                                                    <option value="018" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '018' ? 'selected' : '' }}>KCP MANGGELEWA</option>
                                                    <option value="019" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '019' ? 'selected' : '' }}>KCP SAPE</option>
                                                    <option value="020" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '020' ? 'selected' : '' }}>KCP PLAMPANG</option>
                                                    <option value="021" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '021' ? 'selected' : '' }}>KC SURABAYA DARMO</option>
                                                    <option value="022" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '022' ? 'selected' : '' }}>KC GERUNG</option>
                                                    <option value="023" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '023' ? 'selected' : '' }}>KCP LUNYUK</option>
                                                    <option value="024" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '024' ? 'selected' : '' }}>KCP LOPOK</option>
                                                    <option value="025" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '025' ? 'selected' : '' }}>KCP PEKAT</option>
                                                    <option value="026" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '026' ? 'selected' : '' }}>KCP PRINGGABAYA</option>
                                                    <option value="027" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '027' ? 'selected' : '' }}>KCP PAGESANGAN</option>
                                                    <option value="028" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '028' ? 'selected' : '' }}>KCP Ampenan</option>
                                                    <option value="029" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '029' ? 'selected' : '' }}>KCP Gunungsari</option>
                                                    <option value="030" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '030' ? 'selected' : '' }}>KCP Mujur</option>
                                                    <option value="501" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '501' ? 'selected' : '' }}>KC MASBAGIK</option>
                                                    <option value="502" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '502' ? 'selected' : '' }}>KCP PASAR TALIWANG</option>
                                                    <option value="503" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '503' ? 'selected' : '' }}>KCP KEMPO</option>
                                                    <option value="504" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '504' ? 'selected' : '' }}>KC SRIWIJAYA MATARAM</option>
                                                    <option value="505" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '505' ? 'selected' : '' }}>KCP PEMENANG</option>
                                                    <option value="506" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '506' ? 'selected' : '' }}>KCP MALUK</option>
                                                    <option value="507" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '507' ? 'selected' : '' }}>KCP KEDIRI</option>
                                                    <option value="508" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '508' ? 'selected' : '' }}>KCP WAWO</option>
                                                    <option value="509" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '509' ? 'selected' : '' }}>KCP UTAN</option>
                                                    <option value="510" {{ old('branchid', isset($merchant) ? $merchant->branchid : '') == '510' ? 'selected' : '' }}>KCP PUJUT</option>
                                                </select>
                                            </div>
                                        </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Produk</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" name="kode_produk">
                                            <option value="36" {{ old('kode_produk') == '36' ? 'selected' : '' }}>BSA Laku Pandai</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label" for="file_ktp">KTP</label>
                                    <div class="col-sm-10">
                                        @if(isset($merchant) && $merchant->file_ktp)
                                            <div class="mb-2">
                                                <a href="{{ asset('storage/' . $merchant->file_ktp) }}" target="_blank">Lihat File KTP</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label" for="file_kk">Kartu Keluarga</label>
                                    <div class="col-sm-10">
                                        @if(isset($merchant) && $merchant->file_kk)
                                            <div class="mb-2">
                                                <a href="{{ asset('storage/' . $merchant->file_kk) }}" target="_blank">Lihat File Kartu Keluarga</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label" for="file_npwp">NPWP</label>
                                    <div class="col-sm-10">
                                        @if(isset($merchant) && $merchant->file_npwp)
                                            <div class="mb-2">
                                                <a href="{{ asset('storage/' . $merchant->file_npwp) }}" target="_blank">Lihat File NPWP</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Activate Merchant</label>
                                    <div class="col-sm-10">
                                        <a href="{{ route('merchant_activate', [$merchant->id]) }}">
                                            <button type="button" class="btn btn-primary" {{ $merchant->status_agen == 1 ? 'disabled' : '' }}>
                                                {{ $merchant->status_agen == 1 ? 'Activated' : 'Activate' }}
                                            </button>
                                        </a>
                                        @if ($merchant->status_agen == 1)
                                            <label class="col-sm-10 col-form-label">Merchant Telah aktif</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Deactivate Merchant</label>
                                    <div class="col-sm-10">
                                    <form action="{{ route('merchant_deactivate', [$merchant->id]) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="btn btn-primary" {{ $merchant->status_agen == 2 ? 'disabled' : '' }}>
                                            {{ $merchant->status_agen == 2 ? 'Deactivated' : 'Deactivate' }}
                                        </button>
                                    </form>
                                        @if ($merchant->status_agen == 2)
                                            <label class="col-sm-10 col-form-label">Merchant Tidak aktif</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12 text-right">
                                        <a href="{{route('merchant')}}">
                                            <button type="button" class="btn btn-primary">Back</button>
                                        </a>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


@endsection

@section('page-js')

@endsection

@section('bottom-js')

    <script src="{{asset('assets/js/form.validation.script.js')}}"></script>

@endsection
