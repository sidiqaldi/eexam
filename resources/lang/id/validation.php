<?php

use App\Enums\TimeMode;

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute harus diterima.',
    'active_url' => ':attribute bukan URL yang valid.',
    'after' => ':attribute harus setelah :date.',
    'after_or_equal' => ':attribute harus setelah atau sama dengan :date.',
    'alpha' => ':attribute hanya boleh berupa huruf.',
    'alpha_dash' => ':attribute hanya boleh berupa huruf, angka, dash(-) dan underscores(_).',
    'alpha_num' => ':attribute hanya boleh berupa huruf dan angka.',
    'array' => ':attribute harus berupa array.',
    'before' => ':attribute harus sebelum :date.',
    'before_or_equal' => ':attribute harus sebelum atau sama dengan :date.',
    'between' => [
        'numeric' => ':attribute harus di antara :min dan :max.',
        'file' => ':attribute harus di antara :min dan :max kilobytes.',
        'string' => ':attribute harus di antara :min dan :max karakter.',
        'array' => ':attribute harus di antara :min dan :max item.',
    ],
    'boolean' => ':attribute harus berupa true atau false.',
    'confirmed' => ':attribute konfirmasi tidak sesuai.',
    'date' => ':attribute bukan tanggal yang valid.',
    'date_equals' => ':attribute harus sama dengan :date.',
    'date_format' => ':attribute harus sesuai dengan format :format.',
    'different' => ':attribute dan :other harus berbeda.',
    'digits' => ':attribute harus :digits digit.',
    'digits_between' => ':attribute harus di antara :min dan :max digit.',
    'dimensions' => ':attribute memilik dimensi yang tidak sesuai.',
    'distinct' => ':attribute terdapat nilai yang duplikasi.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'ends_with' => ':attribute harus diakhiri dengan salah satu nilai berikut: :values.',
    'exists' => ':attribute yang dipilih tidak valid.',
    'file' => ':attribute harus berupa file.',
    'filled' => ':attribute harus diisi nilai.',
    'gt' => [
        'numeric' => ':attribute harus lebih besar dari :value.',
        'file' => ':attribute harus lebih besar dari :value kilobytes.',
        'string' => ':attribute harus lebih besar dari :value karakter.',
        'array' => ':attribute harus lebih dari :value item.',
    ],
    'gte' => [
        'numeric' => ':attribute harus lebih besar dari atau sama dengan :value.',
        'file' => ':attribute harus lebih besar dari atau sama dengan :value kilobytes.',
        'string' => ':attribute harus lebih besar dari atau sama dengan :value karakter.',
        'array' => ':attribute harus :value item atau lebih.',
    ],
    'image' => ':attribute harus berupa gambar.',
    'in' => ':attribute yang dipilih tidak valid.',
    'in_array' => ':attribute tidak ditemukan di :other.',
    'integer' => ':attribute harus berupa bilangan bulat.',
    'ip' => ':attribute harus berupa IP address yang valid.',
    'ipv4' => ':attribute harus berupa IPv4 address yang valid.',
    'ipv6' => ':attribute harus berupa IPv6 address yang valid.',
    'json' => ':attribute harus berupa JSON string yang valid.',
    'lt' => [
        'numeric' => ':attribute harus kurang dari :value.',
        'file' => ':attribute harus kurang dari :value kilobytes.',
        'string' => ':attribute harus kurang dari :value karakter.',
        'array' => ':attribute harus kurang dari :value item.',
    ],
    'lte' => [
        'numeric' => ':attribute harus kurang dari atau sama dengan :value.',
        'file' => ':attribute harus kurang dari atau sama dengan :value kilobytes.',
        'string' => ':attribute harus kurang dari atau sama dengan :value karakter.',
        'array' => ':attribute tidak lebih dari :value item.',
    ],
    'max' => [
        'numeric' => ':attribute tidak boleh lebih dari :max.',
        'file' => ':attribute tidak boleh lebih dari :max kilobytes.',
        'string' => ':attribute tidak boleh lebih dari :max karakter.',
        'array' => ':attribute tidak boleh lebih dari :max item.',
    ],
    'mimes' => ':attribute harus file dengan tipe: :values.',
    'mimetypes' => ':attribute harus file dengan tipe: :values.',
    'min' => [
        'numeric' => ':attribute tidak boleh kurang dari :min.',
        'file' => ':attribute tidak boleh kurang dari :min kilobytes.',
        'string' => ':attribute tidak boleh kurang dari :min karakter.',
        'array' => ':attribute tidak boleh kurang dari :min item.',
    ],
    'multiple_of' => ':attribute harus kelipatan dari :value',
    'not_in' => ':attribute yang dipilih tidak valid.',
    'not_regex' => ':attribute format tidak valid.',
    'numeric' => ':attribute harus berupa angka.',
    'password' => 'password tidak tepat.',
    'present' => ':attribute kolom isian harus ada.',
    'regex' => ':attribute format tidak valid.',
    'required' => ':attribute wajib diisi.',
    'required_if' => ':attribute wajib diisi ketika :other adalah :value.',
    'required_unless' => ':attribute wajib diisi kecuali :other adalah :values.',
    'required_with' => ':attribute wajib diisi ketika :values ada.',
    'required_with_all' => ':attribute wajib diisi ketika :values ada.',
    'required_without' => ':attribute wajib diisi ketika :values tidak ada.',
    'required_without_all' => ':attribute wajib diisi ketika :values tidak ada.',
    'same' => ':attribute dan :other harus sama.',
    'size' => [
        'numeric' => ':attribute harus :size.',
        'file' => ':attribute harus :size kilobytes.',
        'string' => ':attribute harus :size karakter.',
        'array' => ':attribute harus terdiri dari :size item.',
    ],
    'starts_with' => ':attribute harus diawali dengan salah satu dari: :values.',
    'string' => ':attribute harus berupa deret kata.',
    'timezone' => ':attribute harus berupa zona waktu yang valid.',
    'unique' => ':attribute sudah digunakan.',
    'uploaded' => ':attribute gagal melakukan upload.',
    'url' => ':attribute format tidak valid.',
    'uuid' => ':attribute harus berupa UUID yang valid.',
    'correct_value' => 'Harus ada satu jawaban benar.',
    'no_questions' => 'Ada sesi yang belum memiliki pertanyaan.',
    'no_sections' => 'Ujian ini belum memiliki sesi.',
    'need_question' => 'Ujian ini belum cukup memiliki soal agar peserta dapat lulus.',
    'participate' => 'Anda telah mengikuti ujian ini.',
    'section_time_limit' => 'Batas waktu di tiap sesi tidak boleh melebihi batas waktu pada config utama.',
    'question_time_limit' => 'Batas waktu di tiap soal tidak boleh melebihi batas waktu pada config utama.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name' => 'Nama',
        'description' => 'Deskripsi',
        'code' => 'kode',
        'time_limit' => 'Batas waktu',
        'time_mode' => 'Pengaturan waktu',
        'question_order' => 'Urutan soal',
        'answer_order' => 'Urutan jawaban',
        'ranking_status' => 'Tampilan ranking',
        'result_status' => 'Tampilan hasil',
        'question_value' => 'Pertanyaan',
        'question_image' => 'Gambar',
        'question_title' => 'Judul',
        'options' => 'Jawaban',
        'question' => 'Pertanyaan',
        'instruction' => 'Instruksi',
    ],

    'values' => [
        'time_mode' => [
            TimeMode::TimeLimit => 'Batas waktu',
            TimeMode::PerSection => 'Tiap Sesi',
            // TimeMode::PerQuestion => 'Tiap Soal',
        ],
    ],

];
