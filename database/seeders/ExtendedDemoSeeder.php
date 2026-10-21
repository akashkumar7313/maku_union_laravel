<?php

namespace Database\Seeders;

use App\Models\ActionCard;
use App\Models\AuditLog;
use App\Models\Banner;
use App\Models\Branch;
use App\Models\ContactMessage;
use App\Models\Gallery;
use App\Models\Grievance;
use App\Models\GrievanceDocument;
use App\Models\GrievanceUpdate;
use App\Models\Member;
use App\Models\MembershipPayment;
use App\Models\MembershipRenewal;
use App\Models\MissionCard;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Notice;
use App\Models\TeamMember;
use App\Models\Translation;
use App\Models\TranslationUsage;
use App\Models\User;
use App\Models\WorkerCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Seeder for a fully populated demo dataset.
 *
 * Complements DemoSeeder by filling the tables it leaves sparse (translations,
 * translation usage, grievance documents, audit trail) and by adding volume
 * across every transactional table. Every write uses a deterministic unique
 * key, so the seeder can be re-run without duplicating rows.
 */
class ExtendedDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->warn('ExtendedDemoSeeder उत्पादन परिवेश में नहीं चलाया जाता।');

            return;
        }

        $staff = $this->seedStaff();
        $members = $this->seedMembers($staff);
        $this->seedPayments($members, $staff);
        $this->seedGrievances($members, $staff);
        $this->seedNews($staff);
        $this->seedActionCards();
        $this->seedMissionCards();
        $this->seedNotices();
        $this->seedTeam();
        $this->seedBranches();
        $this->seedGallery();
        $this->seedBanners();
        $this->seedContactMessages();
        $this->seedTranslations();
        $this->seedTranslationUsage();
        $this->seedAuditLogs($staff, $members);

        $this->command?->info('विस्तृत डेमो डेटा तैयार: '.count($members).' अतिरिक्त सदस्य, अधिकारी, भुगतान, शिकायतें, समाचार, अनुवाद एवं ऑडिट लॉग।');
    }

    /**
     * @return array<int, User>
     */
    private function seedStaff(): array
    {
        $password = env('OFFICER_PASSWORD') ?: env('ADMIN_PASSWORD') ?: 'Maku@2026';

        $rows = [
            ['कला प्रसाद यादव', 'officer1@maku.local', 'अधिकारी', '9876504001', true],
            ['प्रकाश चौहान', 'officer2@maku.local', 'अधिकारी', '9876504002', true],
            ['सुनील कुमार', 'officer3@maku.local', 'अधिकारी', '9876504003', true],
            ['ममता देवी', 'officer4@maku.local', 'अधिकारी', '9876504004', true],
            ['अरुण मिश्र', 'auditor@maku.local', 'अधिकारी', '9876504005', true],
            ['नीरज सिंह', 'officer5@maku.local', 'अधिकारी', '9876504006', false],
        ];

        $staff = [];

        foreach ($rows as [$name, $email, $role, $phone, $active]) {
            $staff[] = User::query()->updateOrCreate(['email' => $email], [
                'name' => $name,
                'password' => $password,
                'role' => $role,
                'phone' => $phone,
                'is_active' => $active,
                'email_verified_at' => now()->subMonths(6),
                'last_login_at' => $active ? now()->subHours(random_int(1, 72)) : null,
            ]);
        }

        return $staff;
    }

    /**
     * @param  array<int, User>  $staff
     * @return array<int, Member>
     */
    private function seedMembers(array $staff): array
    {
        $male = ['रामलाल', 'मोहन लाल', 'संतोष', 'अशोक', 'विजय शंकर', 'राजेश', 'सुनील', 'प्रकाश', 'दिनेश', 'रमेश', 'जय सिंह', 'बहादुर', 'त्रिगुना', 'शिवनाथ', 'किशन', 'योगेश', 'हरिकृष्ण', 'दयाराम', 'सत्यम', 'नरेश'];
        $female = ['सुनीता', 'कमला', 'रेखा', 'सीमा', 'मीना', 'कविता', 'शांति', 'गीता', 'पूजा', 'राधा', 'सरिता', 'नंदिनी', 'लक्ष्मी', 'अनिता', 'ज्योति'];
        $surnames = ['यादव', 'प्रजापति', 'कुमार', 'सिंह', 'पासी', 'चौहान', 'वर्मा', 'तिवारी', 'गुप्ता', 'मिश्र', 'रजवार', 'निषाद', 'मल्ल', 'ठाकुर'];
        $blocks = ['चुनार', 'लालगंज', 'अहरौरा', 'राजगढ़', 'पटेखा', 'सिंगरौली', 'मझ़िया', 'बरगई', 'धनहा', 'कोशी'];
        $categories = WorkerCategory::query()->pluck('id')->all();

        // Status mix mirrors a realistic union register.
        $statuses = array_merge(
            array_fill(0, 26, 'active'),
            array_fill(0, 8, 'pending'),
            array_fill(0, 6, 'expired'),
            array_fill(0, 4, 'suspended'),
            array_fill(0, 2, 'rejected')
        );

        $members = [];

        foreach ($statuses as $index => $status) {
            $isFemale = $index % 3 === 0;
            $first = $isFemale ? $female[$index % count($female)] : $male[$index % count($male)];
            $surname = $surnames[($index * 5) % count($surnames)];
            $name = $isFemale && ! str_contains($first, 'देवी') ? $first.' देवी' : $first.' '.$surname;
            $phone = '98765'.str_pad((string) (10000 + $index + 1), 5, '0', STR_PAD_LEFT);

            $joinedOn = now()->subDays(400 - ($index * 7))->startOfDay();
            $validFrom = $joinedOn->copy()->addDays(1);
            $validUntil = $validFrom->copy()->addYear();

            $member = Member::query()->firstOrNew(['phone' => $phone]);

            $member->fill([
                'name' => $name,
                'guardian_name' => ($index % 2 === 0 ? 'श्री ' : 'श्रीमती ').$surname.' '.['सिंह', 'प्रसाद', 'लाल', 'कुमार'][$index % 4],
                'email' => $index % 3 === 0 ? strtolower($index.'member@example.com') : null,
                'gender' => $isFemale ? 'महिला' : 'पुरुष',
                'dob' => now()->subYears(22 + ($index % 25))->subDays($index % 300)->toDateString(),
                'address' => 'ग्राम/मोहल्ला '.['पिपरी', 'सरैया', 'कलंजा', 'डंगरिया', 'बरवा', 'खजरा', 'भैरहा', 'नरहरपुर'][$index % 8].', पोस्ट '.['राजगढ़', 'चुनार', 'लालगंज', 'अहरौरा'][$index % 4].', मिर्जापुर',
                'district' => 'मिर्जापुर',
                'block' => $blocks[$index % count($blocks)],
                'worker_category_id' => $categories[$index % max(count($categories), 1)] ?? null,
                'photo' => 'demo/real/category-'.(($index % 5) + 1).'.jpg',
                'id_document' => 'demo/real/gallery-'.(($index % 8) + 1).'.jpg',
                'aadhaar_number' => '9'.str_pad((string) (12345678901 + $index * 137), 11, '0', STR_PAD_LEFT),
                'joined_on' => $joinedOn->toDateString(),
                'valid_from' => $validFrom->toDateString(),
                'valid_until' => $validUntil->toDateString(),
                'status' => $status,
                'notes' => $status === 'suspended' ? 'दस्तावेज़ सत्यापन में अस्पष्टता; कार्यालय में दोबारा संपर्क करें।' : null,
                'created_by' => $staff[$index % count($staff)]?->id ?? null,
            ]);

            if (! $member->exists) {
                $member->membership_no = 'TMP-'.$phone;
            }

            $member->save();

            $member->forceFill([
                'membership_no' => sprintf('%s-%d-%05d', config('maku.membership.prefix'), $joinedOn->year, $member->getKey()),
                'created_at' => $joinedOn,
                'updated_at' => $joinedOn->copy()->addDays(2),
            ])->save();

            $members[] = $member;
        }

        return $members;
    }

    /**
     * @param  array<int, Member>  $members
     * @param  array<int, User>  $staff
     */
    private function seedPayments(array $members, array $staff): void
    {
        $year = (int) now()->format('Y');
        $gateways = ['cashfree', 'sandbox', 'upi', 'cash'];
        $statuses = ['paid', 'paid', 'paid', 'paid', 'paid', 'paid', 'pending', 'pending', 'failed', 'refunded'];

        foreach ($members as $index => $member) {
            $isRenewal = $index % 4 === 0;
            $gateway = $gateways[$index % count($gateways)];
            $status = $statuses[$index % count($statuses)];

            $amount = (float) (($isRenewal ? config('maku.membership.renewal_fee') : config('maku.membership.fee')) ?: 100);
            $fee = $gateway === 'cashfree' ? round($amount * 0.02, 2) : 0.0;
            $tax = round($fee * 0.18, 2);

            $created = Carbon::parse($member->joined_on)->copy()->addDays($isRenewal ? 330 : 2)->setTime(11, 20);
            $sequence = $index + 1;

            $payment = MembershipPayment::query()->updateOrCreate(
                ['order_no' => sprintf('ORD-EXT-%d-%04d', $year, $sequence)],
                [
                    'member_id' => $member->id,
                    'receipt_no' => $status === 'paid' ? sprintf('RCPT-%d-8%05d', $year, $sequence) : null,
                    'type' => $isRenewal ? 'renewal' : 'new',
                    'amount' => $amount,
                    'fee_amount' => $fee,
                    'tax_amount' => $tax,
                    'total_amount' => round($amount + $fee + $tax, 2),
                    'currency' => 'INR',
                    'gateway' => $gateway,
                    'gateway_order_id' => $status === 'paid' ? 'ext_order_'.$sequence : null,
                    'gateway_payment_id' => $status === 'paid' ? 'ext_pay_'.$sequence : null,
                    'utr' => in_array($gateway, ['upi', 'cash'], true) && $status === 'paid' ? 'UTR'.(800000000 + $sequence) : null,
                    'receipt_path' => in_array($status, ['pending', 'failed'], true) ? 'demo/real/gallery-3.jpg' : null,
                    'status' => $status,
                    'paid_at' => $status === 'paid' ? $created->copy()->addMinutes(18) : null,
                    'payload' => ['note' => 'विस्तृत डेमो भुगतान प्रविष्टि', 'source' => 'ExtendedDemoSeeder'],
                ]
            );

            $payment->forceFill([
                'created_at' => $created,
                'updated_at' => $created->copy()->addMinutes(18),
            ])->save();

            if ($status === 'paid') {
                $validFrom = Carbon::parse($member->valid_from ?? $member->joined_on);
                $validUntil = Carbon::parse($member->valid_until ?? $validFrom->copy()->addYear());

                MembershipRenewal::query()->updateOrCreate(
                    ['member_id' => $member->id, 'payment_id' => $payment->id],
                    [
                        'valid_from' => $validFrom->toDateString(),
                        'valid_until' => $validUntil->toDateString(),
                        'amount' => $amount,
                        'created_by' => $staff[$index % count($staff)]?->id ?? null,
                    ]
                );
            }
        }
    }

    /**
     * @param  array<int, Member>  $members
     * @param  array<int, User>  $staff
     */
    private function seedGrievances(array $members, array $staff): void
    {
        $rows = [
            ['ठेकेदार ने जॉब कार्ड जारी करने से मना किया', 'काम पूरा होने के बावजूद ठेकेदार जॉब कार्ड देने से इनकार कर रहा है। मज़दूरी का हिसाब भी नहीं दिया जा रहा।', 'urgent', 'in_progress', 'मैदान निरीक्षण के लिए टीम भेजी गई है।'],
            ['पीएम किसान किस्त नहीं मिली', 'खाते में पैसा आया था, पर किस्त के नाम पर रकम जमा नहीं हुई। बैंक से जाँच कराई जा रही है।', 'high', 'in_progress', 'बैंक शाखा से संपर्क किया गया है।'],
            ['स्कूल की फ़ीस में छूट नहीं मिली', 'संगठन द्वारा बताए गए छूट दिखाने के बावजूद स्कूल प्रबंधन ने छूट नहीं दी।', 'normal', 'resolved', 'स्कूल प्रबंधन से समझौता हो गया और छूट लागू कर दी गई।'],
            ['कार्यस्थल पर पेयजल की सुविधा नहीं', 'लम्बे समय से कार्यस्थल पर पेयजल और शौचालय की व्यवस्था नहीं है।', 'normal', 'pending', 'शिकायत सूची में शामिल कर ली गई है।'],
            ['ठेके पर मज़दूर बंद-बंद करके बुलाया जाता है', 'हर दिन बुलाने के बाद मज़दूर को काम से हटा दिया जाता है और फिर अगले दिन फिर बुलाया जाता है।', 'high', 'in_progress', 'श्रम विभाग से शिकायत स्थानांतरित की गई है।'],
            ['मज़दूरी की दर बढ़ा दी गई', 'पहले तय दर से आधी दर पर मज़दूरी दी जा रही है।', 'urgent', 'rejected', 'समझौते की प्रक्रिया में विवाद सिद्ध नहीं हो पाया।'],
            ['सिलाई मज़दूरों का पैसे पैदा करने वालों को नहीं मिला', 'कारखाना बंद होने पर मज़दूरों को बेरोज़गारी भत्ता नहीं मिला।', 'high', 'in_progress', 'कारखाना मालिक से भुगतान की माँग पत्र जारी किया गया है।'],
            ['श्रम विभाग में शिकायत करने पर टाल दिया जाता है', 'दो बार आवेदन देने पर भी अधिकारी द्वारा प्राथमिकता से नहीं लिया गया।', 'normal', 'resolved', 'उपरोक्त अधिकारी से सम्पर्क कर शिकायत दर्ज करा दी गई।'],
            ['आवासीय मज़दूर की रोज़गार गारंटी कार्ड नहीं', 'मनरेगा कार्ड बनवाने के लिए कई बार कार्यालय गए, कार्ड अभी तक नहीं मिला।', 'normal', 'pending', 'ग्राम पंचायत से जवाब माँगा गया है।'],
            ['सफ़ाई कर्मचारियों की सेवा अवधि का लाभ नहीं', '10 वर्ष की सेवा के बावजूद पेंशन का प्रावधान नहीं मिल पा रहा।', 'high', 'in_progress', 'कार्यालय में विकलांग सत्यापन के लिए दस्तावेज़ जमा किए गए हैं।'],
            ['महिला श्रमिक के साथ अनुचित व्यवहार', 'कार्यस्थल पर अनुचित व्यवहार की शिकायत की गई है।', 'urgent', 'in_progress', 'महिला समिति को सूचित किया गया है।'],
            ['बारिश में छत के नीचे काम करने को मजबूर', 'बारिश के दिन भी छत की सुरक्षा के बिना काम करने पर मजबूर किया जा रहा है।', 'normal', 'closed', 'कार्यस्थल पर अस्थायी छत की व्यवस्था कर दी गई है।'],
            ['बेगार मज़दूरों का शुल्क नहीं मिला', 'तीन महीने से बेगार मज़दूरों को काम के दिन का पैसा नहीं दिया गया।', 'high', 'resolved', 'शुल्क जारी कर दिया गया और आगे के लिए सावधानी सूचित की गई।'],
            ['निर्माण स्थल पर बिजली की व्यवस्था नहीं', 'निर्माण स्थल पर मशीन चलाने के लिए बिजली की व्यवस्था नहीं होती।', 'normal', 'pending', 'स्थानीय विद्युत विभाग को सूचना दी गई है।'],
            ['ई-पहचान पत्र में श्रमिक का नाम गलत', 'ई-पहचान पत्र में नाम की स्पेलिंग गलत है, जिससे भुगतान रुक गया है।', 'normal', 'resolved', 'ई-पहचान पत्र में सुधार कर दिया गया।'],
            ['मज़दूरी के बदले खाना दिया जाता है', 'मज़दूरी की जगह अनाज दिया जा रहा है, जिससे महीने का काम नहीं चल पाता।', 'urgent', 'in_progress', 'निरीक्षण दल द्वारा मामला लिया गया है।'],
            ['काम के घंटे का रिकॉर्ड नहीं है', 'काम के घंटे का कोई रिकॉर्ड नहीं होने से मज़दूरों का हिसाब गलत बनता है।', 'normal', 'pending', 'ठेकेदार से डायरी माँगी गई है।'],
            ['श्रम कार्ड निरस्त होने का कारण बताया नहीं', 'कार्ड निरस्त कर दिया गया, कारण स्पष्ट नहीं किया गया।', 'high', 'rejected', 'पुनः आवेदन की प्रक्रिया शुरू कर दी गई है।'],
            ['सरकारी योजना का लाभ नहीं मिला', 'आवेदन स्वीकृत होने के बावजूद योजना का लाभ नहीं मिला।', 'normal', 'resolved', 'संबंधित विभाग से लाभ दिला दिया गया।'],
            ['नक़द मज़दूरी का भुगतान नहीं हुआ', 'काम पूरा होने के बावजूद नक़द मज़दूरी का भुगतान नहीं किया गया।', 'high', 'in_progress', 'शिकायत कार्यालय अधिकारी के समक्ष प्रस्तुत की गई।'],
            ['शिविर में बैनर लगाने की अनुमति नहीं मिली', 'शिविर के लिए बैनर लगाने की अनुमति प्रशासन से नहीं मिली।', 'low', 'closed', 'स्थानीय नगर पालिका से समन्वय कर समाधान किया गया।'],
            ['मज़दूरों के लिए पेयजल टैंकर नहीं', 'गर्मी में पेयजल टैंकर की व्यवस्था नहीं हो पाई।', 'normal', 'resolved', 'नगर पालिका से टैंकर उपलब्ध करा दिया गया।'],
            ['मज़दूर अपना पुराना जॉब कार्ड नहीं ला पाया', 'नया जॉब कार्ड बनवाने पर पुराना जॉब कार्ड जमा करना अनिवार्य बताया गया जो गुमब हो चुका है।', 'low', 'pending', 'नवीनीकरण के लिए शाखा में संपर्क करने को कहा गया है।'],
        ];

        $disk = Storage::disk(config('maku.uploads.disk'));

        foreach ($rows as $index => [$subject, $description, $priority, $status, $note]) {
            $member = $members[($index * 3) % count($members)];
            $created = now()->subDays(60 - $index * 2)->setTime(9, 45);
            $caseNo = sprintf('%s-%d-%05d', config('maku.grievance.prefix'), (int) $created->format('Y'), 1001 + $index);

            $grievance = Grievance::query()->updateOrCreate(['case_no' => $caseNo], [
                'member_id' => $member->id,
                'name' => $member->name,
                'phone' => $member->phone,
                'email' => $member->email,
                'worker_category_id' => $member->worker_category_id,
                'subject' => $subject,
                'description' => $description,
                'status' => $status,
                'priority' => $priority,
                'assigned_to' => $status === 'pending' ? null : $staff[$index % count($staff)]?->id,
                'resolved_at' => in_array($status, ['resolved', 'closed'], true) ? $created->copy()->addDays(11) : null,
                'resolution_note' => in_array($status, ['resolved', 'closed'], true) ? $note : null,
                'ip' => '127.0.0.1',
            ]);

            $grievance->forceFill(['created_at' => $created, 'updated_at' => $created->copy()->addDays(2)])->save();

            $timeline = [
                ['pending', 'शिकायत दर्ज की गई।', true, 0, null],
                ['in_progress', 'शिकायत की जाँच प्रारंभ कर दी गई है।', true, 2, $staff[($index + 1) % count($staff)]?->id],
            ];

            if (in_array($status, ['resolved', 'closed'], true)) {
                $timeline[] = [$status, $note, true, 9, $staff[($index + 2) % count($staff)]?->id];
            }

            if ($index % 4 === 0) {
                $timeline[] = ['in_progress', 'आंतरिक टिप्पणी: सम्बन्धित पक्ष से वार्ता की गई।', false, 4, $staff[$index % count($staff)]?->id];
            }

            foreach ($timeline as [$stage, $text, $public, $afterDays, $userId]) {
                GrievanceUpdate::query()->updateOrCreate(
                    ['grievance_id' => $grievance->id, 'status' => $stage, 'note' => $text],
                    [
                        'user_id' => $userId,
                        'is_public' => $public,
                    ]
                )->forceFill(['created_at' => $created->copy()->addDays($afterDays)])->save();
            }

            $documentCount = $index % 3 === 0 ? 3 : ($index % 3 === 1 ? 2 : 1);

            for ($doc = 1; $doc <= $documentCount; $doc++) {
                $path = 'demo/real/gallery-'.((($index + $doc) % 8) + 1).'.jpg';

                GrievanceDocument::query()->updateOrCreate(
                    ['grievance_id' => $grievance->id, 'original_name' => 'शिकायत-'.($index + 1).'-दस्तावेज़-'.$doc.'.jpg'],
                    [
                        'path' => $path,
                        'mime' => 'image/jpeg',
                        'size' => $disk->exists($path) ? $disk->size($path) : 0,
                        'uploaded_by' => $staff[$index % count($staff)]?->id ?? null,
                    ]
                )->forceFill(['created_at' => $created->copy()->addDays(1)])->save();
            }
        }
    }

    /**
     * @param  array<int, User>  $staff
     */
    private function seedNews(array $staff): void
    {
        $categories = NewsCategory::query()->pluck('id', 'name')->all();

        $rows = [
            ['मनरेगा मज़दूरों के लिए नया जॉब कार्ड शिविर', 'manrega-naya-job-card-shivir', 'श्रमिक समाचार', 'published', 186],
            ['संगठन ने 500 श्रमिकों को सुरक्षा किट वितरित की', '500-shramikon-ko-suraksha-kit', 'संगठन समाचार', 'published', 243],
            ['बेगार मज़दूरों को मासिक मज़दूरी दिलाने की माँग', 'begar-majdoor-masiki-majduri', 'श्रमिक समाचार', 'published', 129],
            ['श्रम विभाग की सहायता से 12 निर्माण स्थलों पर निरीक्षण', '12-nirman-sthal-par-niriksha', 'संगठन समाचार', 'published', 87],
            ['पेंशन दिवस पर वरिष्ठ श्रमिकों का सम्मान', 'pension-divas-shramik-sammman', 'सूचनाएँ', 'published', 205],
            ['महिला श्रमिकों के लिए स्व-रोज़गार समूह', 'mahila-shramik-swashro-jogar', 'संगठन समाचार', 'published', 112],
            ['वर्षा से पहले कार्यस्थलों की सुरक्षा जाँच शुरू', 'varsha-se-pehle-suraksha-janch', 'सूचनाएँ', 'published', 76],
            ['शिकायत निवारण के 30 दिनों में 68 प्रतिशत समाधान', '30-dinom-me-68-percent-samadhan', 'श्रमिक समाचार', 'published', 158],
            ['कार्यालय में नया डिजिटल सदस्यता सेवा केंद्र', 'naya-digital-sadasyata-kendra', 'संगठन समाचार', 'published', 94],
            ['चिनारी शाखा का उद्घाटन', 'chinari-shakha-udghat', 'संगठन समाचार', 'published', 63],
            ['श्रमिक सुरक्षा प्रशिक्षण के लिए नया केंद्र', 'suraksha-prashikshan-naya-kendra', 'सूचनाएँ', 'published', 71],
            ['अंतर्राष्ट्रीय श्रम दिवस पर कार्यक्रम', 'antarrashtriya-shram-divas', 'संगठन समाचार', 'archived', 340],
            ['नवीनीकरण शिविर की तैयारी बैठक सम्पन्न', 'naveenikaran-shivir-taiyari-baithak', 'सूचनाएँ', 'published', 52],
            ['राज मिस्त्रियों को प्रशिक्षण कार्यशाला', 'raj-mistriyon-ko-prashikshan', 'श्रमिक समाचार', 'published', 88],
            ['मुआवज़ा वितरण में तेजी', 'muavaza-vitaran-teji', 'श्रमिक समाचार', 'published', 132],
            ['असंगठित श्रमिकों के लिए डिजिटल पहचान पत्र', 'digital-pehchan-patra', 'संगठन समाचार', 'draft', 0],
            ['कालीन बुनकरों के लिए कच्चा माल सहायता', 'kaleen-bunakron-ka-kachha-mal', 'श्रमिक समाचार', 'published', 109],
            ['संगठन की वार्षिक सामान्य सभा आयोजित', 'varshik-sammanya-sabha', 'संगठन समाचार', 'published', 197],
        ];

        foreach ($rows as $index => [$title, $slug, $categoryName, $status, $views]) {
            $published = now()->subDays(3 + $index * 2)->setTime(11, 0);

            $news = News::query()->updateOrCreate(['slug' => $slug], [
                'news_category_id' => $categories[$categoryName] ?? null,
                'author_id' => $staff[$index % count($staff)]?->id ?? null,
                'title' => $title,
                'excerpt' => 'मिर्जापुर असंगठित कामगार यूनियन की ओर से श्रमिकों हेतु आयोजित कार्यक्रम एवं सूचना।',
                'body' => '<p>'.$title.' विषय पर संगठन द्वारा कार्यक्रम आयोजित किया गया। इसमें श्रमिकों, पदाधिकारियों तथा स्थानीय प्रशासन के प्रतिनिधियों ने भाग लिया।</p><p>संगठन श्रमिकों के मज़दूरी, सामाजिक सुरक्षा एवं सुरक्षित कार्यस्थल के अधिकारों के लिए निरंतर संघर्षरत है। कार्यालय से संपर्क कर अधिक जानकारी प्राप्त करें।</p>',
                'image' => 'demo/real/news-'.(($index % 7) + 1).'.jpg',
                'status' => $status,
                'published_at' => $status === 'draft' ? null : $published,
            ]);

            $news->forceFill(['views' => $views, 'created_at' => $published->copy()->subHours(3)])->save();
        }
    }

    private function seedActionCards(): void
    {
        $rows = [
            ['योजना सहायता केंद्र', 'मनरेगा, पेंशन एवं कल्याण योजनाओं में सहयोग के लिए निःशुल्क मार्गदर्शन', 'purple', 'bi-universal-access', '/contact', 10],
            ['शिकायत स्थिति ट्रैक करें', 'केस नंबर और मोबाइल से अपनी शिकायत की वर्तमान स्थिति देखें', 'teal', 'bi-search', '/grievance/track', 11],
            ['मज़दूरी सहायता', 'बकाया मज़दूरी दिलाने के लिए कार्यालय में शिकायत करें', 'red', 'bi-cash-coin', '/grievance', 12],
            ['श्रमिक जागरूकता', 'श्रम कानून और योजनाओं की जानकारी के लिए निःशुल्क जागरूकता सत्र', 'indigo', 'bi-journal-text', '/news', 13],
        ];

        foreach ($rows as [$title, $description, $color, $icon, $url, $order]) {
            ActionCard::query()->updateOrCreate(['title' => $title], [
                'description' => $description,
                'color' => $color,
                'icon' => $icon,
                'url' => $url,
                'sort_order' => $order,
                'is_active' => true,
            ]);
        }
    }

    private function seedMissionCards(): void
    {
        $rows = [
            ['सुरक्षित कार्यस्थल', 'हर कार्यस्थल पर सुरक्षा उपकरण और प्रशिक्षण सुनिश्चित करना।', 'bi-shield-fill-check', 10],
            ['समान मज़दूरी', 'कार्य के प्रकार और कौशल के आधार पर समान और उचित मज़दूरी दिलाना।', 'bi-currency-rupee', 11],
            ['सरकारी योजनाओं तक पहुँच', 'श्रमिकों को पात्रता, आवेदन और लाभ दिलाने में सहयोग करना।', 'bi-bank', 12],
            ['एकता और आपसी सहयोग', 'सभी कार्यक्षेत्रों के श्रमिकों को एक संगठन में जोड़कर मज़बूती से लड़ना।', 'bi-people-fill', 13],
        ];

        foreach ($rows as [$title, $description, $icon, $order]) {
            MissionCard::query()->updateOrCreate(['title' => $title], [
                'description' => $description,
                'icon' => $icon,
                'sort_order' => $order,
                'is_active' => true,
            ]);
        }
    }

    private function seedNotices(): void
    {
        $rows = [
            ['नवीनीकरण शुल्क में समय-सीमा', 'अवधि समाप्त होने वाले सदस्यों से अनुरोध है कि 30 दिन के भीतर नवीनीकरण कर लें।', '/membership/lookup', 5],
            ['रात्रिका सहायता केंद्र का समय बदला गया', 'केंद्र अब प्रतिदिन प्रातः 8 बजे से रात्रि 9 बजे तक खुला है।', null, 12],
            ['सदस्यता शुल्क जमा करने के नए तरीके', 'अब QR कोड और UPI के माध्यम से भी सीधे जमा किया जा सकता है।', '/membership/register', 20],
            ['हेल्पलाइन नंबर पर अतिरिक्त सुबह की सेवा', 'प्रतिदिन प्रातः 9 बजे से 1 बजे तक अतिरिक्त कॉल सुविधा उपलब्ध है।', null, 34],
            ['नए सदस्यों का स्वागत कार्यक्रम', 'नवीनीकरण के बाद सभी नए सदस्यों का स्वागत कार्यक्रम आयोजित किया जाएगा।', '/news', 48],
            ['पुरानी सूचना: कार्यालय बंद', 'गुरुवार को कार्यालय बंद रहेगा।', null, 70],
        ];

        foreach ($rows as $index => [$title, $body, $url, $daysAgo]) {
            $notice = Notice::query()->updateOrCreate(['title' => $title], [
                'body' => $body,
                'url' => $url,
                'starts_at' => now()->subDays($daysAgo),
                'ends_at' => $index === count($rows) - 1 ? now()->subDays($daysAgo - 2) : now()->addDays(20 + $index * 4),
                'is_active' => true,
            ]);

            $notice->forceFill(['created_at' => now()->subDays($daysAgo)])->save();
        }
    }

    private function seedTeam(): void
    {
        $rows = [
            ['श्री हरिकृष्ण यादव', 'सह-अध्यक्ष'],
            ['श्रीमती सरिता देवी', 'महिला समिति संयोजक'],
            ['श्री दिनेश प्रजापति', 'कोषाध्यक्ष सहायक'],
            ['श्री योगेश मिश्र', 'कार्यक्रम समन्वयक'],
            ['श्रीमती पूजा सिंह', 'प्रचार समिति संयोजक'],
            ['श्री त्रिगुना पासी', 'क्षेत्रीय समन्वयक'],
        ];

        foreach ($rows as $index => [$name, $position]) {
            TeamMember::query()->updateOrCreate(['name' => $name], [
                'position' => $position,
                'photo' => 'demo/real/team-'.(($index % 8) + 1).'.jpg',
                'sort_order' => 20 + $index,
                'is_active' => true,
            ]);
        }
    }

    private function seedBranches(): void
    {
        $rows = [
            ['पटेखा शाखा', 'बाजार चौक, पटेखा, मिर्जापुर - 231401', '9876502004', 'pathekha@maku.local'],
            ['सिंगरौली शाखा', 'जिला परिषद मार्ग, सिंगरौली, मिर्जापुर - 231302', '9876502005', 'singrauli@maku.local'],
            ['मझ़िया शाखा', 'शाही पुल, मझ़िया, मिर्जापुर - 231305', '9876502006', 'majhia@maku.local'],
            ['बरगई शाखा', 'कला कारखाना रोड, बरगई, मिर्जापुर - 231201', '9876502007', 'bargai@maku.local'],
        ];

        foreach ($rows as $index => [$name, $address, $phone, $email]) {
            Branch::query()->updateOrCreate(['name' => $name], [
                'address' => $address,
                'phone' => $phone,
                'email' => $email,
                'map_url' => 'https://maps.google.com/?q='.urlencode($name),
                'sort_order' => 10 + $index,
                'is_active' => true,
            ]);
        }
    }

    private function seedGallery(): void
    {
        $rows = [
            ['श्रमिक जागरूकता रैली, मझ़िया', 'demo/real/gallery-2.jpg'],
            ['नवीनीकरण शिविर, पटेखा', 'demo/real/gallery-4.jpg'],
            ['प्रशिक्षण कार्यशाला, सिंगरौली', 'demo/real/gallery-6.jpg'],
            ['स्वास्थ्य जाँच शिविर, बरगई', 'demo/real/gallery-1.jpg'],
            ['सम्मान समारोह, मुख्य कार्यालय', 'demo/real/gallery-3.jpg'],
            ['कॉलेज सहायता केंद्र भ्रमण', 'demo/real/gallery-5.jpg'],
            ['श्रमिक चेतना संवाद सत्र', 'demo/real/gallery-7.jpg'],
            ['नवीन पदाधिकारी स्वागत समारोह', 'demo/real/gallery-8.jpg'],
            ['मज़दूर दिवस कार्यक्रम', 'demo/real/category-2.jpg'],
            ['योजना जागरूकता अभियान', 'demo/real/category-4.jpg'],
            ['पीएम किसान जागरूकता शिविर', 'demo/real/category-5.jpg'],
            ['संगठन कार्यालय भ्रमण', 'demo/real/category-6.jpg'],
        ];

        foreach ($rows as $index => [$title, $file]) {
            Gallery::query()->updateOrCreate(['title' => $title], [
                'type' => 'image',
                'file' => $file,
                'sort_order' => 30 + $index,
                'is_active' => true,
            ]);
        }

        Gallery::query()->updateOrCreate(['title' => 'सदस्यता अभियान (वीडियो)'], [
            'type' => 'video',
            'file' => null,
            'video_url' => 'https://www.youtube.com/watch?v=aqz-KE-bpKQ',
            'sort_order' => 60,
            'is_active' => true,
        ]);
    }

    private function seedBanners(): void
    {
        $rows = [
            ['हर श्रमिक का अधिकार, हर महीने मज़दूरी', 'मज़दूरी का समय, सुरक्षा और सम्मान', 'MAKU Union का संकल्प', 'सदस्य बनें', '/membership/register', 'demo/banner-3.svg', 10],
            ['शिकायत का समाधान, आपकी आवाज़ के साथ', 'केस नंबर से स्थिति देखें', 'आपकी शिकायत हमारी ज़िम्मेदारी', 'शिकायत दर्ज करें', '/grievance', 'demo/banner-4.svg', 11],
            ['योजनाओं का लाभ पाने में मदद', 'मनरेगा, पेंशन और कल्याण योजनाएँ', 'निःशुल्क जानकारी एवं सहयोग', 'जानकारी लें', '/worker-categories', 'demo/banner-5.svg', 12],
        ];

        foreach ($rows as [$title, $highlight, $subtitle, $button, $url, $image, $order]) {
            $banner = Banner::query()->updateOrCreate(['title' => $title], [
                'highlight' => $highlight,
                'subtitle' => $subtitle,
                'button_text' => $button,
                'button_url' => $url,
                'image' => $image,
                'is_active' => true,
                'sort_order' => $order,
            ]);

            $banner->forceFill(['created_at' => now()->subDays($order * 2)])->save();
        }
    }

    private function seedContactMessages(): void
    {
        $rows = [
            ['गुलाब सिंह', 'gulab@example.com', '9876505001', 'सदस्यता शुल्क जमा नहीं हो पाया', 'UPI से भुगतान किया था पर रसीद नहीं मिली। जाँच करवाने की कृपा करें।', true, 1],
            ['शांति देवी', 'shanti@example.com', '9876505002', 'ई-पहचान पत्र में नाम सुधार', 'नाम की स्पेलिंग गलत हो गई है। कृपया सहयोग करें।', false, 2],
            ['रमेश गुप्ता', 'ramesh@example.com', '9876505003', 'शिकायत की प्रगति', 'मेरी शिकायत का केस नंबर MAKU-GRV-2026-00901 है। कृपया अवस्था बताएँ।', true, 3],
            ['कविता यादव', 'kavita2@example.com', '9876505004', 'सदस्यता कार्ड में पता सुधार', 'पता बदल गया है, कार्ड में सुधार करना है।', false, 4],
            ['सोनू पासी', 'sonu@example.com', '9876505005', 'काम के घंटे का रिकॉर्ड', 'हमारे हिस्से का मज़दूरी हिसाब गलत बनाया गया है।', true, 6],
            ['आरती देवी', 'aarti@example.com', '9876505006', 'स्वयंसेवक कैसे बनें', 'संगठन में किस प्रकार से स्वयंसेवक के रूप में जुड़ सकते हैं?', false, 7],
            ['महेश यादव', null, '9876505007', 'छूट के लिए आवेदन कैसे करें', 'बच्चों की पढ़ाई की छूट के लिए क्या दस्तावेज़ चाहिए?', false, 9],
            ['रेखा वर्मा', 'rekha@example.com', '9876505008', 'शिविर की तारीख', 'आने वाले स्वास्थ्य जाँच शिविर की तारीख क्या है?', true, 11],
            ['अनिल मिश्र', 'anil@example.com', '9876505009', 'नई शाखा की जानकारी', 'क्या मेरे गाँव में भी शाखा खुलने की संभावना है?', false, 13],
            ['प्रभा देवी', 'prabha@example.com', '9876505010', 'पेंशन फ़ाइल में कमी', 'पेंशन के लिए फ़ाइल में कौन-से दस्तावेज़ चाहिए?', true, 15],
            ['सुशील कुमार', 'sushil@example.com', '9876505011', 'सदस्यता की जाँच', 'मेरी सदस्यता सक्रिय नहीं दिख रही है।', false, 18],
            ['निशा यादव', 'nisha@example.com', '9876505012', 'महिला समिति से जुड़ना', 'महिला श्रमिक समिति में सक्रिय भागीदारी कैसे लें?', true, 21],
            ['हरेश चौहान', 'haresh@example.com', '9876505013', 'मज़दूरी नहीं मिली', 'ठेकेदार ने महीने की मज़दूरी नहीं दी। कृपया मदद करें।', false, 24],
            ['कविता श्रीवास्तव', 'kavita.s@example.com', '9876505014', 'वेबसाइट में गलती', 'सदस्यता फ़ॉर्म में जन्म तिथि का विकल्प सही नहीं भरा जा रहा।', true, 27],
            ['दीपक रजवार', 'deepak@example.com', '9876505015', 'सदस्यता की वैधता', 'कब तक सदस्यता वैध है? कृपया जानकारी दें।', false, 31],
        ];

        foreach ($rows as [$name, $email, $phone, $subject, $message, $read, $daysAgo]) {
            $model = ContactMessage::query()->updateOrCreate(['phone' => $phone, 'subject' => $subject], [
                'name' => $name,
                'email' => $email,
                'message' => $message,
                'ip' => '127.0.0.1',
                'read_at' => $read ? now()->subDays($daysAgo)->addHours(4) : null,
            ]);

            $model->forceFill(['created_at' => now()->subDays($daysAgo)])->save();
        }
    }

    private function seedTranslations(): void
    {
        $rows = [
            ['home.mission.title', 'हमारा उद्देश्य', true],
            ['home.mission.subtitle', 'श्रमिकों के अधिकारों की रक्षा', true],
            ['home.categories.title', 'कार्यक्षेत्र', true],
            ['home.categories.subtitle', 'अपने कार्यक्षेत्र के अनुसार जानकारी देखें', true],
            ['home.news.title', 'ताज़ा समाचार', true],
            ['home.gallery.title', 'गैलरी', true],
            ['home.contact.title', 'संपर्क करें', false],
            ['membership.form.subtitle', 'कुछ ही मिनटों में सदस्य बनें', true],
            ['membership.form.gender', 'लिंग', true],
            ['membership.form.category', 'कार्यक्षेत्र', true],
            ['membership.lookup.title', 'सदस्यता जाँचें', true],
            ['membership.lookup.submit', 'जाँच करें', true],
            ['membership.card.title', 'सदस्यता कार्ड', true],
            ['membership.card.valid', 'वैधता', false],
            ['membership.receipt.title', 'भुगतान रसीद', false],
            ['membership.renew.title', 'सदस्यता नवीनीकरण', true],
            ['grievance.form.title', 'शिकायत दर्ज करें', true],
            ['grievance.form.description', 'शिकायत का विवरण लिखें', true],
            ['grievance.track.title', 'शिकायत की स्थिति देखें', true],
            ['grievance.track.case_no', 'केस नंबर', true],
            ['news.list.title', 'समाचार', true],
            ['news.readmore', 'पूरा पढ़ें', false],
            ['contact.form.message', 'आपका संदेश', true],
            ['footer.quicklinks', 'त्वरित लिंक', false],
            ['footer.contact', 'संपर्क', false],
            ['common.download', 'डाउनलोड करें', false],
            ['common.submit', 'जमा करें', true],
            ['common.cancel', 'रद्द करें', false],
        ];

        foreach ($rows as [$key, $value, $reviewed]) {
            Translation::query()->updateOrCreate(['locale' => 'hi', 'key' => $key], [
                'value' => $value,
                'source' => 'manual',
                'reviewed' => $reviewed,
            ]);
        }
    }

    private function seedTranslationUsage(): void
    {
        for ($offset = 5; $offset >= 0; $offset--) {
            $month = now()->subMonths($offset)->format('Y-m');
            $characters = 1200 + ($offset * 430) + (($offset * 137) % 900);
            $calls = 18 + ($offset * 7) + (($offset * 11) % 23);

            TranslationUsage::query()->updateOrCreate(['provider' => 'google', 'month' => $month], [
                'characters' => $characters,
                'calls' => $calls,
            ])->forceFill(['created_at' => now()->subMonths($offset)->endOfMonth()])->save();
        }
    }

    /**
     * @param  array<int, User>  $staff
     * @param  array<int, Member>  $members
     */
    private function seedAuditLogs(array $staff, array $members): void
    {
        // The audit trail has no natural unique key, so this seeder's own rows are
        // cleared first to keep repeated runs from duplicating entries.
        AuditLog::query()->where('user_agent', 'ExtendedDemoSeeder')->delete();

        $actions = [
            'auth.login', 'auth.logout', 'member.created', 'member.updated', 'member.status_updated',
            'member.payment_recorded', 'payment.paid', 'payment.verified', 'payment.utr_submitted',
            'payment.cashfree_return', 'payment.receipt_uploaded', 'membership.registered',
            'membership.renewal_initiated', 'grievance.created', 'grievance.updated',
            'grievance.document_uploaded', 'grievance.document_deleted', 'contact_message.received',
            'contact_message.read', 'contact_message.deleted', 'news.created', 'news.updated',
            'news.deleted', 'news_category.created', 'page.updated', 'menu.created', 'menu.updated',
            'menu.deleted', 'banner.created', 'banner.updated', 'action_card.created', 'action_card.updated',
            'mission_card.created', 'gallery.created', 'gallery.updated', 'gallery.deleted',
            'notice.created', 'notice.updated', 'team_member.created', 'team_member.updated',
            'branch.created', 'worker_category.created', 'translation.updated', 'settings.updated',
            'user.created', 'user.updated', 'auth.login_failed',
        ];

        $auditableTypes = [
            'App\\Models\\Member', 'App\\Models\\MembershipPayment', 'App\\Models\\Grievance',
            'App\\Models\\News', 'App\\Models\\Gallery', 'App\\Models\\Banner', 'App\\Models\\Page',
            'App\\Models\\MenuItem', 'App\\Models\\ContactMessage', 'App\\Models\\Translation',
            'App\\Models\\Notice', 'App\\Models\\TeamMember', 'App\\Models\\Branch',
            'App\\Models\\WorkerCategory', 'App\\Models\\User', 'App\\Models\\ActionCard',
            'App\\Models\\MissionCard',
        ];

        $actions = array_merge($actions, $actions, $actions);

        foreach ($actions as $index => $action) {
            $member = $members[$index % count($members)];
            $isFailedLogin = $action === 'auth.login_failed';
            $type = $auditableTypes[$index % count($auditableTypes)];

            $log = AuditLog::query()->create([
                'user_id' => $isFailedLogin ? null : $staff[$index % count($staff)]?->id,
                'action' => $action,
                'auditable_type' => $type,
                'auditable_id' => $index % 40 + 1,
                'old_values' => $index % 3 === 0 ? ['status' => 'pending', 'source' => 'demo'] : null,
                'new_values' => $index % 3 === 0 ? ['status' => 'active', 'source' => 'demo'] : null,
                'ip' => '127.0.0.1',
                'user_agent' => 'ExtendedDemoSeeder',
            ]);

            $log->forceFill(['created_at' => now()->subDays($index % 45)->subMinutes($index)])->save();
        }
    }
}
