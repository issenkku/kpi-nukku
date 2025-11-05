@extends('layouts.app')
@section('title', 'แก้ไขผู้ใช้งาน')
@section('content')
    <div class="user-containers">
        <div class="header-contatainers">แก้ไขผู้ใช้งาน</div>
        <div class="user-form">
            @if ($errors->any())
                <div class="error-message" style="margin-bottom:16px;">
                    <ul style="margin-left:18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group-container">
                    <x-card>
                        <div class="card-title">ข้อมูลส่วนตัว</div>
                        <div class="form-row">
                            <div class="form-group">
                                <x-select name="title" label="คำนำหน้าชื่อ" :options="[
                                    'นาย' => 'นาย',
                                    'นาง' => 'นาง',
                                    'นางสาว' => 'นางสาว',
                                    'ผศ.' => 'ผศ.',
                                    'รศ.' => 'รศ.',
                                    'ศ.' => 'ศ.',
                                ]"
                                    placeholder="-- เลือกคำนำหน้า --" :value="$user->title" />
                                @error('title')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <x-input name="first_name" label="ชื่อจริง" placeholder="กรุณากรอกชื่อจริง"
                                    :value="$user->first_name" :required="true" />
                                @error('first_name')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <x-input name="last_name" label="นามสกุล" placeholder="กรุณากรอกนามสกุล" :value="$user->last_name"
                                    :required="true" />
                                @error('last_name')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <x-input name="email" type="email" label="อีเมล" placeholder="กรุณากรอกอีเมล"
                                    :value="$user->email" :required="true" />
                                @error('email')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <x-input name="phone" label="เบอร์โทรศัพท์" placeholder="กรุณากรอกเบอร์โทรศัพท์"
                                    :value="$user->phone" />
                                @error('phone')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </x-card>
                    <x-card>
                        <div class="card-title">ข้อมูลการทำงาน</div>
                        <div class="form-row">
                            <div class="form-group">
                                <x-select name="department_id" label="หน่วยงาน" :options="$departments" option-value="id"
                                    option-label="name" placeholder="กรุณาเลือกหน่วยงาน" :searchable="true"
                                    :required="true" :value="$user->department_id" />
                                @error('department_id')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <x-input name="positype" label="ประเภทบุคลากร"
                                    placeholder="เช่น พนักงานมหาวิทยาลัย, ข้าราชการ" :value="$user->positype" />
                                @error('positype')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <x-input name="workline" label="สายงาน" placeholder="เช่น สนับสนุน, วิชาการ"
                                    :value="$user->workline" />
                                @error('workline')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <x-input name="posi" label="ตำแหน่ง" placeholder="เช่น นักวิชาการคอมพิวเตอร์"
                                    :value="$user->posi" />
                                @error('posi')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <x-input name="level" label="ระดับ" placeholder="เช่น ปฏิบัติการ, ชำนาญการ, เชี่ยวชาญ"
                                    :value="$user->level" />
                                @error('level')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </x-card>

                    <x-card>
                        <div class="card-title">ความปลอดภัย</div>
                        <div class="form-row">
                            <div class="form-group">
                                <x-input name="password" type="password" label="รหัสผ่านใหม่ (ถ้าไม่เปลี่ยน ปล่อยว่าง)"
                                    placeholder="ใส่รหัสผ่านใหม่ (อย่างน้อย 8 ตัวอักษร)" />
                                @error('password')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <x-input name="password_confirmation" type="password" label="ยืนยันรหัสผ่านใหม่"
                                    placeholder="พิมพ์รหัสผ่านใหม่อีกครั้ง" />
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                @php $currentRole = old('role', $user->getRoleNames()->first()); @endphp
                                <x-select name="role" label="บทบาท (Role)" :options="collect($roles)->mapWithKeys(fn($r) => [$r => $r])->toArray()"
                                    placeholder="กรุณาเลือกบทบาท" :required="true"
                                    :value="$currentRole" />
                                @error('role')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <x-select name="status" label="สถานะระบบ" :options="[
                                    '1' => 'Active',
                                    '0' => 'Inactive',
                                ]" :value="$user->status ? 1 : 0"
                                    placeholder="เลือกสถานะ" :required="true" />
                                @error('status')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </x-card>
                    <div class="form-actions">
                        <a href="{{ route('users.index') }}" class="btn btn-outline"><i data-lucide="arrow-left"
                                class="btn-icon"></i> ยกเลิก</a>
                        <button type="submit" class="btn btn-primary"><i data-lucide="save" class="btn-icon"></i>
                            บันทึก</button>
                    </div>

                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .user-containers {
            width: 100%;
            /* max-width: 1500px; */
            margin: 0 auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .header-contatainers {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px 20px;
            font-weight: 700;
            font-size: 30px;
            background: linear-gradient(90deg, #a9c6ff 0%, #fff3d4 100%);
            color: #222;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }

        .form-row {
            display: flex;
            gap: 30px;
        }

        .form-group {
            flex: 1;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: normal;
        }

        .required {
            color: red;
        }

        .error-message {
            color: #f44336;
            font-size: 14px;
            margin-top: 5px;
        }

        .form-actions {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 30px;
        }

        .form-group-container {
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .card-title {
            font-size: 18px;
            color: var(--blue-default);
            margin: 0 0 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
            padding-left: 10px;
        }

        .card-title::before {
            content: "";
            width: 4px;
            height: 20px;
            border-radius: 8px;
            background: var(--blue-default);
            position: absolute;
            left: 0;
            top: 2px;
            opacity: .25;
        }

        /* ================ Responsive Design ================ */
        /* < 640px (Mobile) */
        @media (max-width: 639px) {
            .user-containers {
                border-radius: 8px;
                margin-bottom: 16px;
            }

            .header-contatainers {
                padding: 12px 16px;
                font-size: 20px;
                border-top-left-radius: 8px;
                border-top-right-radius: 8px;
            }

            .user-form {
                padding: 12px;
            }

            .form-group-container {
                padding: 0;
                gap: 12px;
            }

            .form-row {
                flex-direction: column;
                gap: 12px;
            }

            .form-group {
                width: 100%;
            }

            .card-title {
                font-size: 16px;
                margin-bottom: 12px;
            }

            .form-actions {
                flex-direction: column;
                gap: 12px;
                margin-top: 20px;
            }

            .form-actions .btn {
                width: 100%;
                justify-content: center;
            }

            .error-message {
                font-size: 12px;
            }
        }

        /* 640px–767px (Small Tablet) */
        @media (min-width: 640px) and (max-width: 767px) {
            .user-containers {
                border-radius: 8px;
                margin-bottom: 20px;
            }

            .header-contatainers {
                padding: 12px 18px;
                font-size: 24px;
                border-top-left-radius: 8px;
                border-top-right-radius: 8px;
            }

            .user-form {
                padding: 16px;
            }

            .form-group-container {
                padding: 0;
                gap: 16px;
            }

            .form-row {
                flex-direction: column;
                gap: 16px;
            }

            .form-group {
                width: 100%;
            }

            .card-title {
                font-size: 17px;
                margin-bottom: 14px;
            }

            .form-actions {
                gap: 16px;
                margin-top: 20px;
            }

            .form-actions .btn {
                flex: 1;
                justify-content: center;
            }

            .error-message {
                font-size: 13px;
            }
        }

        /* 768px–1023px (Tablet) */
        @media (min-width: 768px) and (max-width: 1023px) {
            .user-containers {
                border-radius: 10px;
                margin-bottom: 24px;
            }

            .header-contatainers {
                padding: 14px 20px;
                font-size: 26px;
                border-top-left-radius: 10px;
                border-top-right-radius: 10px;
            }

            .user-form {
                padding: 18px;
            }

            .form-group-container {
                padding: 0;
                gap: 18px;
            }

            .form-row {
                gap: 20px;
            }

            .card-title {
                font-size: 17px;
                margin-bottom: 14px;
            }

            .form-actions {
                gap: 18px;
                margin-top: 25px;
            }

            .error-message {
                font-size: 13px;
            }
        }

        /* 1024px–1279px (Desktop) */
        @media (min-width: 1024px) and (max-width: 1279px) {
            .user-containers {
                border-radius: 10px;
                margin-bottom: 28px;
            }

            .header-contatainers {
                padding: 15px 20px;
                font-size: 28px;
            }

            .user-form {
                padding: 20px;
            }

            .form-group-container {
                padding: 0;
                gap: 20px;
            }

            .form-row {
                gap: 24px;
            }

            .card-title {
                font-size: 18px;
                margin-bottom: 16px;
            }

            .form-actions {
                gap: 20px;
                margin-top: 28px;
            }

            .error-message {
                font-size: 14px;
            }
        }

        /* 1280px–1535px (Large Desktop) */
        @media (min-width: 1280px) and (max-width: 1535px) {
            .user-containers {
                border-radius: 10px;
                margin-bottom: 30px;
            }

            .header-contatainers {
                padding: 15px 20px;
                font-size: 30px;
            }

            .user-form {
                padding: 20px;
            }

            .form-group-container {
                padding: 0;
                gap: 20px;
            }

            .form-row {
                gap: 30px;
            }

            .card-title {
                font-size: 18px;
                margin-bottom: 16px;
            }

            .form-actions {
                gap: 20px;
                margin-top: 28px;
            }

            .error-message {
                font-size: 14px;
            }
        }
    </style>
@endpush
