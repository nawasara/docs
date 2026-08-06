<?php

namespace Nawasara\Docs\Support;

/**
 * Penjelasan dan contoh per domain API.
 *
 * Ditulis tangan, tidak diturunkan dari kode: yang dijelaskan di sini adalah
 * KENAPA sebuah endpoint ada dan apa yang sengaja tidak dikembalikan — hal
 * yang tidak bisa dibaca dari tabel route. Daftar endpoint dan scope-nya
 * sendiri tetap datang dari runtime (lihat ApiCatalog), jadi yang bisa basi
 * di sini hanya narasinya, bukan datanya.
 */
class ApiExamples
{
    /**
     * @return array<string, array{
     *   title:string, summary:string, icon:string,
     *   notes:array<int,string>,
     *   withheld:array<int,string>,
     *   examples:array<int,array{label:string, request:string, response:string}>
     * }>
     */
    public function all(): array
    {
        return [
            'registry' => [
                'title' => 'Registry',
                'icon' => 'lucide-building-2',
                'summary' => 'Data master organisasi: daftar OPD, aset yang dimilikinya, dan siapa bertugas di mana. '
                    .'Ini yang paling berguna untuk berbagi data — dua aplikasi bisa memakai daftar dinas yang sama '
                    .'alih-alih masing-masing menyimpan salinan yang lambat laun berbeda.',
                'notes' => [
                    'Tautkan data lewat `code` OPD dan `keycloak_id` orang, bukan `id` baris — keduanya bertahan meski nama atau username berubah.',
                    'Scope keanggotaan dipisah dari OPD dan aset karena ia memetakan orang ke organisasi, bukan data organisasi.',
                ],
                'withheld' => [
                    '`notes` aset — catatan operator bebas isi; tanpa aturan apa yang boleh ditulis di sana, tidak ada jaminan isinya aman keluar.',
                    '`ticket_ref` dan `external_id` — rujukan internal dan id di sistem pihak ketiga.',
                ],
                'examples' => [
                    [
                        'label' => 'Daftar OPD',
                        'request' => 'GET /api/v1/registry/opd?q=kominfo',
                        'response' => <<<'JSON'
                            {
                              "data": [
                                {
                                  "code": "KOMINFO",
                                  "name": "Dinas Kominfo Statistik",
                                  "address": null,
                                  "phone": null,
                                  "email": null,
                                  "assets_count": 118,
                                  "members_count": 5
                                }
                              ],
                              "meta": { "total": 1, "per_page": 100, "current_page": 1, "last_page": 1 }
                            }
                            JSON,
                    ],
                    [
                        'label' => 'Aset milik satu OPD',
                        'request' => 'GET /api/v1/registry/assets?opd=KOMINFO&type=subdomain&per_page=2',
                        'response' => <<<'JSON'
                            {
                              "data": [
                                {
                                  "id": 224,
                                  "type": "subdomain",
                                  "type_label": "Subdomain",
                                  "identifier": "teleport.ponorogo.go.id",
                                  "status": "active",
                                  "status_label": "Aktif",
                                  "source": "cloudflare",
                                  "opd": { "code": "KOMINFO", "name": "Dinas Kominfo Statistik" },
                                  "penanggung_jawab": { "name": "PRINGGO JUNI SAPUTRO A.Md.Kom.", "nip": "199506142020121004" },
                                  "registered_at": "2026-04-14T00:00:00+00:00",
                                  "discovered_at": null
                                }
                              ],
                              "meta": { "total": 118, "per_page": 2, "current_page": 1, "last_page": 59 }
                            }
                            JSON,
                    ],
                    [
                        'label' => 'Anggota sebuah OPD',
                        'request' => 'GET /api/v1/registry/memberships?opd=KOMINFO',
                        'response' => <<<'JSON'
                            {
                              "data": [
                                {
                                  "keycloak_id": "65f5c671-bd3b-4a78-ae03-cd99ef1441bd",
                                  "name": "PRINGGO JUNI SAPUTRO A.Md.Kom.",
                                  "nip": "199506142020121004",
                                  "opd": { "code": "KOMINFO", "name": "Dinas Kominfo Statistik" },
                                  "aktif": true
                                }
                              ],
                              "meta": { "total": 5, "per_page": 50, "current_page": 1, "last_page": 1 }
                            }
                            JSON,
                    ],
                ],
            ],

            'keycloak' => [
                'title' => 'Keycloak',
                'icon' => 'lucide-users',
                'summary' => 'Direktori pegawai. Dipakai aplikasi lain untuk mencari orang dan mengisi data '
                    .'kepegawaian otomatis, alih-alih meminta pengguna mengetik ulang namanya sendiri.',
                'notes' => [
                    'Dilayani dari snapshot lokal yang disegarkan tiap jam, bukan dari realm langsung — Keycloak tidak jadi titik kegagalan tunggal bagi setiap konsumen.',
                    'Simpan `id` (UUID Keycloak), bukan `username`: username bisa berubah, UUID tidak.',
                ],
                'withheld' => [
                    '`whatsapp_number` — nomor pribadi.',
                    'Blob `attributes` mentah — isinya bisa bertambah lewat konfigurasi Keycloak saja, jadi mengeksposnya berarti mengekspos apa pun yang ditambahkan nanti.',
                    '`required_actions`, status TOTP, dan sesi — itu status keamanan akun; bocor berarti memberi peta siapa yang belum memasang 2FA.',
                ],
                'examples' => [
                    [
                        'label' => 'Cari pegawai',
                        'request' => 'GET /api/v1/keycloak/users?q=budi&per_page=1',
                        'response' => <<<'JSON'
                            {
                              "data": [
                                {
                                  "id": "8b085300-dc0d-497a-9f4a-47a6de80444f",
                                  "username": "198106102003122002",
                                  "name": "DIAN SULISTYOWATI WATIK SH",
                                  "first_name": "DIAN",
                                  "last_name": "SULISTYOWATI WATIK SH",
                                  "nip": "198106102003122002",
                                  "email": "diansulistyowati681@gmail.com",
                                  "email_verified": false,
                                  "enabled": true,
                                  "created_at": "2025-10-09T03:30:09+00:00"
                                }
                              ],
                              "meta": { "total": 1, "per_page": 1, "current_page": 1, "last_page": 1 }
                            }
                            JSON,
                    ],
                    [
                        'label' => 'Cari lewat NIP (username)',
                        'request' => 'GET /api/v1/keycloak/users/by-username/198106102003122002',
                        'response' => "{\n  \"data\": { \"id\": \"8b085300-…\", \"nip\": \"198106102003122002\", \"name\": \"DIAN SULISTYOWATI WATIK SH\", \"enabled\": true }\n}",
                    ],
                ],
            ],

            'zoom' => [
                'title' => 'Zoom',
                'icon' => 'lucide-video',
                'summary' => 'Jadwal rapat dan daftar rekaman. Untuk aplikasi yang perlu menampilkan agenda '
                    .'rapat tanpa mengirim orang ke Nawasara.',
                'notes' => [
                    'Dilayani dari snapshot lokal — memanggil Zoom per request akan menghabiskan kuota rate limit akun untuk trafik orang lain.',
                    '`join_url` dan `password` hanya muncul bila token membawa scope `zoom.meeting.join`, yang terpisah dari `zoom.meeting.read`. Aplikasi yang menampilkan agenda tidak perlu ikut memegang kunci masuk rapat.',
                    'Default `window=upcoming`. Pakai `window=all` untuk menyertakan rapat yang sudah lewat.',
                ],
                'withheld' => [
                    '`start_url` — dalam keadaan apa pun, termasuk dengan scope join. Tautan itu memulai rapat SEBAGAI host, jadi ia kendali penuh, bukan sekadar akses masuk.',
                    '`download_url`, `play_url`, `file_url` rekaman — tautan langsung ke isi rapat internal. Yang perlu menonton diarahkan lewat Nawasara, di mana aksesnya tercatat.',
                ],
                'examples' => [
                    [
                        'label' => 'Rapat mendatang',
                        'request' => 'GET /api/v1/zoom/meetings?window=upcoming&per_page=1',
                        'response' => <<<'JSON'
                            {
                              "data": [
                                {
                                  "meeting_id": "88427359283",
                                  "topic": "Rapat Koordinasi Bulanan",
                                  "agenda": null,
                                  "start_time": "2026-08-12T02:00:00+00:00",
                                  "duration_minutes": 60,
                                  "timezone": "Asia/Jakarta",
                                  "status": "waiting",
                                  "type": 2,
                                  "is_recurring": false,
                                  "host": { "user_id": "abc123", "email": "kominfo@ponorogo.go.id", "name": "Admin Kominfo" },
                                  "recordings_count": 0,
                                  "created_at": "2026-08-01T04:12:00+00:00"
                                }
                              ],
                              "meta": { "total": 28, "per_page": 1, "current_page": 1, "last_page": 28 }
                            }
                            JSON,
                    ],
                    [
                        'label' => 'Dengan scope zoom.meeting.join',
                        'request' => 'GET /api/v1/zoom/meetings/88427359283',
                        'response' => <<<'JSON'
                            {
                              "data": {
                                "meeting_id": "88427359283",
                                "topic": "Rapat Koordinasi Bulanan",
                                "start_time": "2026-08-12T02:00:00+00:00",
                                "join_url": "https://us02web.zoom.us/j/88427359283?pwd=…",
                                "password": "123456"
                              }
                            }
                            JSON,
                    ],
                ],
            ],

            'secscan' => [
                'title' => 'Secscan',
                'icon' => 'lucide-shield-alert',
                'summary' => 'Keamanan: insiden dari agent, temuan situs terindikasi judol atau deface, '
                    .'status agent, statistik agregat, dan daftar IP terblokir.',
                'notes' => [
                    'Ini satu-satunya domain dengan endpoint TULIS: block dan unblock IP, masing-masing dengan scope terpisah supaya "boleh block" bisa dipisahkan dari "boleh unblock".',
                    'Endpoint block menghormati flag `dry_run` global — token tidak bisa mem-bypass mode dry-run.',
                    'Statistik memakai perhitungan yang sama dengan email digest harian, jadi angkanya tidak bisa berbeda.',
                ],
                'withheld' => [
                    '`evidence` insiden — baris log mentah yang bisa memuat token di query string, username SSH yang dicoba, atau payload serangan apa adanya. Hanya nama host sasaran yang diambil.',
                    '`evidence` temuan — memuat daftar akun admin WordPress beserta emailnya.',
                    'Detail agent: `api_key_hash`, `ip_local`, `hostname`, versi dan daftar plugin — gabungannya adalah daftar belanja bagi penyerang yang mencari versi rentan.',
                ],
                'examples' => [
                    [
                        'label' => 'Statistik 7 hari',
                        'request' => 'GET /api/v1/secscan/stats?days=7',
                        'response' => <<<'JSON'
                            {
                              "data": {
                                "total": 42,
                                "by_severity": { "critical": 3, "high": 12, "medium": 27 },
                                "by_type": { "sqli_attempt": 18, "brute_force_ssh": 14 },
                                "top_ips": [ { "ip": "203.0.113.99", "count": 18, "score": 95 } ],
                                "top_hosts": [ { "host": "dinaskesehatan.ponorogo.go.id", "count": 12 } ],
                                "blocked": 5, "blocked_active": 23,
                                "agents_online": 8, "agents_total": 9
                              },
                              "meta": { "from": "2026-07-30T…", "to": "2026-08-06T…" }
                            }
                            JSON,
                    ],
                    [
                        'label' => 'Insiden kritis',
                        'request' => 'GET /api/v1/secscan/incidents?severity=critical&per_page=1',
                        'response' => <<<'JSON'
                            {
                              "data": [
                                {
                                  "incident_id": "inc8b32d6a30",
                                  "type": "sqli_attempt",
                                  "type_label": "SQL Injection",
                                  "severity": "critical",
                                  "score": 95,
                                  "source_ip": "203.0.113.99",
                                  "occurrences": 7,
                                  "mitre_technique": "T1190",
                                  "mitre_name": "Exploit Public-Facing Application",
                                  "targets": ["dinaskesehatan.ponorogo.go.id"],
                                  "blocked": true,
                                  "agent": { "agent_id": "srv01", "name": "Server Dinkes" }
                                }
                              ],
                              "meta": { "total": 3, "per_page": 1, "current_page": 1, "last_page": 3 }
                            }
                            JSON,
                    ],
                    [
                        'label' => 'Block IP (tulis)',
                        'request' => "POST /api/v1/secscan/ip-blocks\nContent-Type: application/json\n\n{ \"ip\": \"203.0.113.99\", \"reason\": \"manual\" }",
                        'response' => "{\n  \"data\": { \"ip\": \"203.0.113.99\", \"status\": \"active\", \"enforced\": true, \"dry_run\": false }\n}",
                    ],
                ],
            ],

            'cctv' => [
                'title' => 'CCTV',
                'icon' => 'lucide-cctv',
                'summary' => 'Daftar kamera publik beserta lokasi dan statusnya, untuk aplikasi yang '
                    .'menampilkannya di peta.',
                'notes' => [
                    'Identifier publik memakai `slug`, bukan id database — aman muncul di URL.',
                ],
                'withheld' => [
                    'IP perangkat, port, path RTSP, dan kredensial kamera.',
                ],
                'examples' => [
                    [
                        'label' => 'Daftar kamera',
                        'request' => 'GET /api/v1/cctv/cameras',
                        'response' => <<<'JSON'
                            {
                              "data": [
                                {
                                  "slug": "alun-alun-utara",
                                  "name": "Alun-alun Utara",
                                  "location": "Jl. Alun-alun Utara",
                                  "latitude": -7.8654, "longitude": 111.4692,
                                  "status": "online",
                                  "last_seen_at": "2026-08-06T07:40:00+00:00",
                                  "channel": 1, "codec": "H264"
                                }
                              ],
                              "meta": { "total": 12 }
                            }
                            JSON,
                    ],
                ],
            ],

            'wifi' => [
                'title' => 'WiFi',
                'icon' => 'lucide-wifi',
                'summary' => 'Titik WiFi publik: nama, lokasi, koordinat, dan status. Untuk diplot di peta '
                    .'aplikasi lain.',
                'notes' => [
                    'Pakai `?mappable=1` untuk hanya mengambil titik yang punya koordinat.',
                ],
                'withheld' => [],
                'examples' => [
                    [
                        'label' => 'Titik yang bisa dipetakan',
                        'request' => 'GET /api/v1/wifi/points?mappable=1',
                        'response' => <<<'JSON'
                            {
                              "data": [
                                {
                                  "id": 3,
                                  "name": "Taman Kota",
                                  "location": "Taman Kota Ponorogo",
                                  "latitude": -7.8661, "longitude": 111.4701,
                                  "is_active": true
                                }
                              ],
                              "meta": { "total": 24 }
                            }
                            JSON,
                    ],
                ],
            ],
        ];
    }

    public function for(string $domain): ?array
    {
        return $this->all()[$domain] ?? null;
    }
}
