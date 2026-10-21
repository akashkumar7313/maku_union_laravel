<?php

namespace Database\Seeders;

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
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Notice;
use App\Models\TeamMember;
use App\Models\Translation;
use App\Models\User;
use App\Models\WorkerCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->warn('DemoSeeder उत्पादन परिवेश में नहीं चलाया जाता।');

            return;
        }

        if (Member::query()->exists()) {
            $this->command?->warn('डेमो डेटा पहले से मौजूद है; DemoSeeder छोड़ा गया।');

            return;
        }

        $admin = User::query()->where('role', 'admin')->orderBy('id')->first();
        $officerPassword = env('OFFICER_PASSWORD') ?: env('ADMIN_PASSWORD');

        if ($officerPassword) {
            User::query()->updateOrCreate(['email' => 'officer@maku.local'], [
                'name' => 'कार्यालय अधिकारी',
                'password' => $officerPassword,
                'role' => 'officer',
                'is_active' => true,
            ]);
        }

        $categories = WorkerCategory::query()->pluck('id', 'slug');
        $newsCategories = NewsCategory::query()->pluck('id', 'name');

        $this->makeImages();

        $members = $this->seedMembers($admin, $categories);
        $this->seedPayments($members, $admin);
        $this->seedGrievances($members, $admin, $categories);
        $this->seedNews($newsCategories, $admin);
        $this->seedNotices();
        $this->seedTeam();
        $this->seedBranches();
        $this->seedGallery();
        $this->seedBanners();
        $this->seedContactMessages();
        $this->seedTranslations();
        $this->seedAuditLogs($admin, $members);

        $this->command?->info('डेमो डेटा तैयार: '.count($members).' सदस्य, शिकायतें, समाचार, गैलरी, टीम, शाखाएँ, संदेश और ऑडिट लॉग।');
    }

    private function makeImages(): void
    {
        $disk = Storage::disk(config('maku.uploads.disk'));

        $gallery = [
            ['श्रमिक सभा', '#1f4b3f', '#2f7a5b'],
            ['सदस्यता शिविर', '#7a3f1f', '#d98324'],
            ['जागरूकता रैली', '#203a66', '#3f6fb5'],
            ['श्रमिक सम्मान समारोह', '#5a2b5e', '#a44f9a'],
            ['स्वास्थ्य जाँच शिविर', '#5b2020', '#b33a3a'],
            ['प्रशिक्षण कार्यशाला', '#2d4a1f', '#6f9a3a'],
            ['कार्यालय बैठक', '#3d3d3d', '#7a7a7a'],
            ['मजदूर दिवस', '#5e4a1a', '#c2a23a'],
        ];

        foreach ($gallery as $index => [$title, $from, $to]) {
            $this->putSvg($disk, 'demo/gallery-'.($index + 1).'.svg', $this->svg($title, $from, $to, 1200, 800));
        }

        $news = ['श्रमिक अधिकार', 'सदस्यता अभियान', 'सरकारी योजना', 'सुरक्षा प्रशिक्षण', 'मजदूरी समस्या', 'स्वास्थ्य शिविर', 'संगठन विस्तार'];
        foreach ($news as $index => $title) {
            $this->putSvg($disk, 'demo/news-'.($index + 1).'.svg', $this->svg($title, '#173a5e', '#2f6f9f', 1200, 700));
        }

        $team = ['अध्यक्ष', 'उपाध्यक्ष', 'महामंत्री', 'कोषाध्यक्ष', 'संगठन मंत्री', 'कार्यकारिणी सदस्य', 'क्षेत्रीय समन्वयक', 'मीडिया प्रभारी'];
        foreach ($team as $index => $position) {
            $this->putSvg($disk, 'demo/team-'.($index + 1).'.svg', $this->svg($position, '#4a3b2a', '#9b7b4f', 600, 600));
        }

        foreach (['श्रमिक एकता', 'सदस्यता अभियान', 'श्रमिक अधिकार'] as $index => $title) {
            $this->putSvg($disk, 'demo/banner-'.($index + 1).'.svg', $this->svg($title, '#122a4a', '#2f6f9f', 1600, 700));
        }
    }

    private function seedMembers(?User $admin, $categories): array
    {
        $rows = [
            ['रामलाल यादव', 'श्री रामदीन यादव', '9876501001', 'पुरुष', 'ग्राम पिपरी, पोस्ट राजगढ़, मिर्जापुर', 'राजगढ़', 'manrega-majdoor', 'active', '2023-06-12', '2027-06-12', '2023-06-12'],
            ['सुनीता देवी', 'श्रीमती शिवकुमारी', '9876501002', 'महिला', 'वार्ड 7, चुनार रोड, मिर्जापुर', 'चुनार', 'nirman-shramik', 'active', '2024-01-18', '2026-12-31', '2024-01-18'],
            ['मोहन लाल प्रजापति', 'श्री बाबूलाल प्रजापति', '9876501003', 'पुरुष', 'मुहल्ला काजीपुर, मिर्जापुर', 'सदर', 'raj-mistri', 'active', '2024-03-05', '2027-03-05', '2024-03-05'],
            ['शीला देवी', 'श्री रामप्रसाद', '9876501004', 'महिला', 'ग्राम बरकछा, पोस्ट बरकछा, मिर्जापुर', 'बरकछा', 'karyashala-shramik', 'active', '2024-07-22', '2027-07-22', '2024-07-22'],
            ['अशोक कुमार', 'श्री जगदीश', '9876501005', 'पुरुष', 'ग्राम भरुहना, पोस्ट अहरौरा, मिर्जापुर', 'अहरौरा', 'patthar-khadan-shramik', 'active', '2024-11-09', '2027-11-09', '2024-11-09'],
            ['रेखा देवी', 'श्रीमती सुमित्रा देवी', '9876501006', 'महिला', 'ग्राम पड़री, पोस्ट लालगंज, मिर्जापुर', 'लालगंज', 'anya-asangathit-shramik', 'active', '2025-02-14', '2027-02-14', '2025-02-14'],
            ['संतोष कुमार', 'श्री रामलाल', '9876501007', 'पुरुष', 'ग्राम कछवा, पोस्ट कछवा, मिर्जापुर', 'कछवा', 'nirman-shramik', 'active', '2025-06-30', '2027-06-30', '2025-06-30'],
            ['फूलमती देवी', 'श्रीमती कमला देवी', '9876501008', 'महिला', 'ग्राम दुबार, पोस्ट मड़िहान, मिर्जापुर', 'मड़िहान', 'manrega-majdoor', 'active', '2025-09-16', '2026-11-15', '2025-09-16'],
            ['विजय शंकर', 'श्री श्यामलाल', '9876501009', 'पुरुष', 'ग्राम पथरा, पोस्ट सीखड़, मिर्जापुर', 'सीखड़', 'raj-mistri', 'pending', '2026-09-28', null, '2026-09-28'],
            ['अनीता देवी', 'श्रीमती शांति देवी', '9876501010', 'महिला', 'वार्ड 3, चुनार, मिर्जापुर', 'चुनार', 'anya-asangathit-shramik', 'pending', '2026-10-04', null, '2026-10-04'],
            ['दिनेश यादव', 'श्री रामनरेश यादव', '9876501011', 'पुरुष', 'ग्राम गड़वा, पोस्ट गड़वा, मिर्जापुर', 'लालगंज', 'nirman-shramik', 'expired', '2023-02-20', '2025-02-20', '2023-02-20'],
            ['कमलेश कुमार', 'श्री रामकिशोर', '9876501012', 'पुरुष', 'ग्राम महुवर, पोस्ट राजगढ़, मिर्जापुर', 'राजगढ़', 'patthar-khadan-shramik', 'suspended', '2024-05-11', '2027-05-11', '2024-05-11'],
        ];

        $members = [];

        foreach ($rows as $index => [$name, $guardian, $phone, $gender, $address, $block, $category, $status, $from, $until, $joined]) {
            $joinedOn = Carbon::parse($joined);

            $member = Member::query()->create([
                'membership_no' => sprintf('MAKU-%d-9%04d', $joinedOn->year, $index + 1),
                'name' => $name,
                'guardian_name' => $guardian,
                'phone' => $phone,
                'email' => $index % 3 === 0 ? Str::slug($name, '.').'@example.com' : null,
                'gender' => $gender,
                'dob' => $joinedOn->copy()->subYears(30 + $index)->format('Y-m-d'),
                'address' => $address,
                'district' => 'मिर्जापुर',
                'block' => $block,
                'worker_category_id' => $categories[$category] ?? null,
                'photo' => 'demo/members/member-'.($index + 1).'.jpg',
                'aadhaar_number' => '99998888'.str_pad((string) (1000 + $index * 37), 4, '0', STR_PAD_LEFT),
                'joined_on' => $joinedOn->format('Y-m-d'),
                'valid_from' => $from,
                'valid_until' => $until,
                'status' => $status,
                'notes' => $index === 11 ? 'शिकायतों पर कार्रवाई के दौरान अस्थायी निलंबन।' : null,
                'created_by' => $admin?->id,
            ]);

            $member->forceFill(['created_at' => $joinedOn->copy()->setTime(11, 30)])->save();
            $members[] = $member;
        }

        return $members;
    }

    private function seedPayments(array $members, ?User $admin): void
    {
        $year = (int) now()->format('Y');
        $paid = ['paid', 'paid', 'paid', 'paid', 'paid', 'paid', 'paid', 'paid', 'pending', 'pending', 'failed', 'refunded'];

        foreach ($members as $index => $member) {
            $status = $paid[$index] ?? 'pending';
            $isRenewal = $index === 1 || $index === 6;
            $amount = (float) (($isRenewal ? config('maku.membership.renewal_fee') : config('maku.membership.fee')) ?: 100);
            $created = Carbon::parse($member->joined_on)->copy()->addDays($isRenewal ? 320 : 2);
            $gateway = $index % 4 === 0 ? 'upi' : ($index % 4 === 1 ? 'cashfree' : 'sandbox');
            $fee = $gateway === 'cashfree' ? round($amount * 0.02, 2) : 0.0;
            $tax = round($fee * 0.18, 2);

            $payment = MembershipPayment::query()->create([
                'member_id' => $member->id,
                'order_no' => sprintf('ORD-DEMO-%d-%03d', $year, $index + 1),
                'receipt_no' => $status === 'paid' ? sprintf('RCPT-%d-9%05d', $year, $index + 1) : null,
                'type' => $isRenewal ? 'renewal' : 'new',
                'amount' => $amount,
                'fee_amount' => $fee,
                'tax_amount' => $tax,
                'total_amount' => round($amount + $fee + $tax, 2),
                'currency' => 'INR',
                'gateway' => $gateway,
                'gateway_order_id' => $status === 'paid' ? 'demo_order_'.($index + 1) : null,
                'gateway_payment_id' => $status === 'paid' ? 'demo_pay_'.($index + 1) : null,
                'utr' => $index % 4 === 0 && $status === 'paid' ? 'UTR'.(900000000 + $index) : null,
                'receipt_path' => $gateway !== 'cashfree' && in_array($status, ['pending', 'failed'], true) ? 'demo/real/gallery-3.jpg' : null,
                'status' => $status,
                'paid_at' => $status === 'paid' ? $created->copy()->addMinutes(12) : null,
                'payload' => ['note' => 'डेमो भुगतान प्रविष्टि', 'source' => 'DemoSeeder'],
            ]);

            $payment->forceFill(['created_at' => $created, 'updated_at' => $created->copy()->addMinutes(12)])->save();

            if ($status === 'paid') {
                $validFrom = Carbon::parse($member->valid_from ?? $member->joined_on);
                MembershipRenewal::query()->create([
                    'member_id' => $member->id,
                    'payment_id' => $payment->id,
                    'valid_from' => $validFrom->format('Y-m-d'),
                    'valid_until' => Carbon::parse($member->valid_until ?? $validFrom->copy()->addYear())->format('Y-m-d'),
                    'amount' => $amount,
                    'created_by' => $admin?->id,
                ]);
            }
        }
    }

    private function seedGrievances(array $members, ?User $admin, $categories): void
    {
        $rows = [
            ['मजदूरी का भुगतान नहीं मिला', 'ठेकेदार द्वारा तीन सप्ताह की मजदूरी का भुगतान नहीं किया गया। कार्यस्थल पर कई बार शिकायत की गई, लेकिन कोई कार्रवाई नहीं हुई।', 'pending', 'high', 0, 'pending'],
            ['निर्माण स्थल पर सुरक्षा उपकरण नहीं', 'निर्माण स्थल पर हेलमेट, दस्ताने और सुरक्षा बेल्ट उपलब्ध नहीं कराए जा रहे हैं। दुर्घटना का खतरा बना हुआ है।', 'in_progress', 'urgent', 2, 'in_progress'],
            ['मनरेगा जॉब कार्ड में नाम नहीं', 'ग्राम पंचायत में जॉब कार्ड के लिए आवेदन के बावजूद नाम दर्ज नहीं हुआ। रोजगार से वंचित होना पड़ रहा है।', 'resolved', 'normal', 0, 'resolved'],
            ['पेंशन योजना का लाभ नहीं मिला', 'वृद्धावस्था पेंशन की पात्रता के बावजूद कई महीनों से भुगतान नहीं आया। बैंक खाता सक्रिय है।', 'in_progress', 'high', 5, 'in_progress'],
            ['कार्यशाला में काम के घंटे अधिक', 'कार्यशाला में प्रतिदिन 12 घंटे से अधिक काम कराया जा रहा है और ओवरटाइम नहीं दिया जा रहा।', 'pending', 'normal', 3, 'pending'],
            ['दुर्घटना में मुआवजा नहीं', 'पत्थर खदान में काम करते समय चोट लगी, लेकिन मालिक ने इलाज खर्च और मुआवजा देने से मना कर दिया।', 'rejected', 'urgent', 4, 'rejected'],
            ['सदस्यता शुल्क दो बार कटा', 'ऑनलाइन भुगतान के दौरान दो बार शुल्क कट गया। एक भुगतान की वापसी का अनुरोध है।', 'resolved', 'normal', 1, 'resolved'],
            ['घरेलू कामगार को मजदूरी नहीं', 'घरेलू काम के बदले तीन माह से वेतन नहीं दिया गया। नियोक्ता से संपर्क करने पर उपेक्षा की गई।', 'closed', 'low', 5, 'closed'],
        ];

        foreach ($rows as $index => [$subject, $description, $status, $priority, $memberIndex, $stage]) {
            $member = $members[$memberIndex];
            $created = now()->subDays(40 - $index * 4)->setTime(10, 15);
            $caseNo = sprintf('%s-%d-%05d', config('maku.grievance.prefix'), (int) $created->format('Y'), 900 + $index + 1);

            $grievance = Grievance::query()->create([
                'case_no' => $caseNo,
                'member_id' => $member->id,
                'name' => $member->name,
                'phone' => $member->phone,
                'email' => $member->email,
                'worker_category_id' => $member->worker_category_id,
                'subject' => $subject,
                'description' => $description,
                'status' => $status,
                'priority' => $priority,
                'assigned_to' => in_array($stage, ['in_progress', 'resolved', 'rejected', 'closed'], true) ? $admin?->id : null,
                'resolved_at' => in_array($status, ['resolved', 'closed'], true) ? $created->copy()->addDays(9) : null,
                'resolution_note' => in_array($status, ['resolved', 'closed'], true) ? 'मामले की जाँच की गई और संबंधित पक्षों से समन्वय कर समाधान किया गया।' : null,
                'ip' => '127.0.0.1',
            ]);

            $grievance->forceFill(['created_at' => $created])->save();

            GrievanceUpdate::query()->create([
                'grievance_id' => $grievance->id,
                'status' => 'pending',
                'note' => 'शिकायत दर्ज की गई।',
                'is_public' => true,
            ])->forceFill(['created_at' => $created])->save();

            if ($stage !== 'pending') {
                GrievanceUpdate::query()->create([
                    'grievance_id' => $grievance->id,
                    'status' => $stage,
                    'note' => match ($stage) {
                        'in_progress' => 'शिकायत की जाँच प्रारंभ कर दी गई है। संबंधित विभाग से जानकारी मांगी गई।',
                        'resolved' => 'शिकायत का समाधान कर दिया गया है। कृपया आवश्यक होने पर कार्यालय से संपर्क करें।',
                        'rejected' => 'प्रारंभिक जाँच में शिकायत के समर्थन में पर्याप्त दस्तावेज़ उपलब्ध नहीं मिले।',
                        default => 'शिकायत को अभिलेख में बंद कर दिया गया है।',
                    },
                    'user_id' => $admin?->id,
                    'is_public' => true,
                ])->forceFill(['created_at' => $created->copy()->addDays(3)])->save();
            }

            if ($index < 2) {
                $documentPath = 'demo/real/gallery-'.($index + 2).'.jpg';
                $disk = Storage::disk(config('maku.uploads.disk'));

                GrievanceDocument::query()->updateOrCreate(
                    ['grievance_id' => $grievance->id, 'original_name' => 'शिकायत-दस्तावेज़-'.($index + 1).'.jpg'],
                    [
                        'path' => $documentPath,
                        'mime' => 'image/jpeg',
                        'size' => $disk->exists($documentPath) ? $disk->size($documentPath) : 0,
                        'uploaded_by' => $admin?->id,
                    ]
                );
            }
        }
    }

    private function seedNews($categories, ?User $admin): void
    {
        $rows = [
            ['मिर्जापुर में श्रमिक अधिकार जागरूकता रैली का आयोजन', 'shramik-adhikar-jagrukta-rally', 'श्रमिक समाचार', 'published', 214, 2],
            ['नए सदस्यता अभियान में 250 श्रमिक जुड़े', 'naya-sadasya-abhiyan-250-shramik', 'संगठन समाचार', 'published', 167, 8],
            ['निर्माण श्रमिक कल्याण बोर्ड पंजीकरण शिविर 25 अक्टूबर को', 'nirman-shramik-kalyan-board-shivir', 'सूचनाएँ', 'published', 302, 1],
            ['कार्यस्थल सुरक्षा पर प्रशिक्षण कार्यक्रम संपन्न', 'suraksha-prashikshan-karyakram', 'संगठन समाचार', 'published', 98, 15],
            ['मजदूरी विवाद में श्रमिकों को सहायता', 'majduri-vivad-sahyata', 'श्रमिक समाचार', 'published', 145, 21],
            ['चुनार में निःशुल्क स्वास्थ्य जाँच शिविर', 'chunar-nishulk-swasthya-shivir', 'सूचनाएँ', 'published', 121, 28],
            ['संगठन का विस्तार: लालगंज में नई शाखा', 'lalganj-nayi-shakha', 'संगठन समाचार', 'draft', 0, 4],
        ];

        foreach ($rows as [$title, $slug, $newsCategory, $status, $views, $daysAgo]) {
            $published = now()->subDays($daysAgo);

            $news = News::query()->updateOrCreate(['slug' => $slug], [
                'news_category_id' => $categories[$newsCategory] ?? null,
                'author_id' => $admin?->id,
                'title' => $title,
                'excerpt' => 'MAKU Union द्वारा आयोजित कार्यक्रम और श्रमिकों से जुड़ी ताज़ा जानकारी।',
                'body' => '<p>मिर्जापुर असंगठित कामगार यूनियन (MAKU Union) द्वारा '.$title.' विषय पर कार्यक्रम आयोजित किया गया। कार्यक्रम में श्रमिकों, पदाधिकारियों और स्थानीय प्रशासन के प्रतिनिधियों ने भाग लिया।</p><p>संगठन श्रमिकों के अधिकारों, सामाजिक सुरक्षा योजनाओं और सुरक्षित कार्यस्थल के लिए निरंतर प्रयास कर रहा है। अधिक जानकारी के लिए कार्यालय से संपर्क करें।</p>',
                'image' => 'demo/real/news-'.(($daysAgo % 7) + 1).'.jpg',
                'status' => $status,
                'published_at' => $status === 'published' ? $published : null,
            ]);

            $news->forceFill(['views' => $views, 'created_at' => $published->copy()->subHours(2)])->save();
        }
    }

    private function seedNotices(): void
    {
        $rows = [
            ['सदस्यता नवीनीकरण शिविर 5 नवंबर से', 'सभी सदस्यों से अनुरोध है कि अपनी सदस्यता वैधता जाँचें और नवीनीकरण शिविर में भाग लें।', '/membership/lookup', 10],
            ['शिकायत निस्तारण के लिए हेल्पलाइन उपलब्ध', 'सोमवार से शनिवार प्रातः 10 से सायं 5 बजे तक हेल्पलाइन पर संपर्क करें।', '/grievance', 25],
            ['निर्माण श्रमिक पंजीकरण शिविर', 'आवश्यक दस्तावेज़ के साथ कार्यालय पहुँचें। पंजीकरण निःशुल्क है।', '/contact', 40],
            ['मजदूर दिवस समारोह की तैयारी बैठक', 'सभी क्षेत्रीय समन्वयक 1 मई से पहले कार्यालय में संपर्क करें।', null, 55],
            ['पुरानी सूचना: कार्यालय अवकाश', 'दीपावली के अवसर पर कार्यालय दो दिन बंद रहेगा।', null, 90],
        ];

        foreach ($rows as $index => [$title, $body, $url, $daysAgo]) {
            $notice = Notice::query()->updateOrCreate(['title' => $title], [
                'body' => $body,
                'url' => $url,
                'starts_at' => now()->subDays($daysAgo),
                'ends_at' => $index === 4 ? now()->subDays($daysAgo - 2) : now()->addDays(30 + $index * 5),
                'is_active' => true,
            ]);

            $notice->forceFill(['created_at' => now()->subDays($daysAgo)])->save();
        }
    }

    private function seedTeam(): void
    {
        $rows = [
            ['श्री रामनरेश यादव', 'अध्यक्ष'],
            ['श्रीमती कमला देवी', 'उपाध्यक्ष'],
            ['श्री संतोष कुमार', 'महामंत्री'],
            ['श्री मोहन लाल प्रजापति', 'कोषाध्यक्ष'],
            ['श्री अशोक कुमार', 'संगठन मंत्री'],
            ['श्रीमती सुनीता देवी', 'कार्यकारिणी सदस्य'],
            ['श्री विजय शंकर', 'क्षेत्रीय समन्वयक'],
            ['श्रीमती रेखा देवी', 'मीडिया प्रभारी'],
        ];

        foreach ($rows as $index => [$name, $position]) {
            TeamMember::query()->updateOrCreate(['name' => $name], [
                'position' => $position,
                'photo' => 'demo/real/team-'.($index + 1).'.jpg',
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);
        }
    }

    private function seedBranches(): void
    {
        $rows = [
            ['मुख्य कार्यालय, मिर्जापुर', 'MAKU Union भवन, स्टेशन रोड, मिर्जापुर, उत्तर प्रदेश - 231001', '0542-2345678', 'info@maku.local'],
            ['चुनार शाखा', 'निकट बस स्टैंड, चुनार, मिर्जापुर - 231304', '9876502001', 'chunar@maku.local'],
            ['लालगंज शाखा', 'मुख्य मार्ग, लालगंज, मिर्जापुर - 231211', '9876502002', 'lalganj@maku.local'],
            ['अहरौरा शाखा', 'बाजार रोड, अहरौरा, मिर्जापुर - 231301', '9876502003', 'ahraura@maku.local'],
        ];

        foreach ($rows as $index => [$name, $address, $phone, $email]) {
            Branch::query()->updateOrCreate(['name' => $name], [
                'address' => $address,
                'phone' => $phone,
                'email' => $email,
                'map_url' => 'https://maps.google.com/?q='.urlencode($name),
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);
        }
    }

    private function seedGallery(): void
    {
        $rows = [
            ['श्रमिक सभा, मिर्जापुर', 'image', 'demo/real/category-1.jpg'],
            ['सदस्यता शिविर, चुनार', 'image', 'demo/real/gallery-2.jpg'],
            ['जागरूकता रैली', 'image', 'demo/real/gallery-3.jpg'],
            ['श्रमिक सम्मान समारोह', 'image', 'demo/real/gallery-4.jpg'],
            ['स्वास्थ्य जाँच शिविर', 'image', 'demo/real/category-3.jpg'],
            ['प्रशिक्षण कार्यशाला', 'image', 'demo/real/gallery-6.jpg'],
            ['कार्यालय बैठक', 'image', 'demo/real/gallery-7.jpg'],
            ['मजदूर दिवस कार्यक्रम', 'image', 'demo/real/gallery-8.jpg'],
        ];

        foreach ($rows as $index => [$title, $type, $file]) {
            Gallery::query()->updateOrCreate(['title' => $title], [
                'type' => $type,
                'file' => $file,
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);
        }

        Gallery::query()->updateOrCreate(['title' => 'श्रमिक अधिकार अभियान (वीडियो)'], [
            'type' => 'video',
            'file' => null,
            'video_url' => 'https://www.youtube.com/watch?v=aqz-KE-bpKQ',
            'sort_order' => count($rows) + 1,
            'is_active' => true,
        ]);
    }

    private function seedBanners(): void
    {
        Banner::query()->where('sort_order', 1)->whereNull('image')->update(['image' => 'demo/real/banner-1.jpg']);

        $rows = [
            ['श्रमिक एकता ही शक्ति है', 'संगठित हों, अधिकार पाएँ', 'श्रमिकों की आवाज़', 'सदस्य बनें', '/membership/register', 'demo/banner-1.svg', 2],
            ['सुरक्षित कार्यस्थल, सम्मानित जीवन', 'हर श्रमिक का अधिकार', 'MAKU Union का संकल्प', 'शिकायत दर्ज करें', '/grievance', 'demo/banner-2.svg', 3],
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

            $banner->forceFill(['created_at' => now()->subDays($order * 3)])->save();
        }
    }

    private function seedContactMessages(): void
    {
        $rows = [
            ['राजेश कुमार', 'rajesh@example.com', '9876503001', 'सदस्यता के बारे में जानकारी', 'मैं निर्माण श्रमिक हूँ और सदस्यता लेना चाहता हूँ। आवश्यक दस्तावेज़ की जानकारी दें।', true, 2],
            ['सीमा देवी', 'seema@example.com', '9876503002', 'शिकायत दर्ज करने की प्रक्रिया', 'शिकायत ऑनलाइन कैसे दर्ज करें, कृपया मार्गदर्शन करें।', false, 1],
            ['अनिल यादव', null, '9876503003', 'योजना की जानकारी', 'निर्माण श्रमिक कल्याण बोर्ड की योजनाओं की जानकारी चाहिए।', false, 1],
            ['मीना देवी', 'meena@example.com', '9876503004', 'कार्यालय का समय', 'कार्यालय किस समय खुलता है और किस दिन बंद रहता है?', true, 4],
            ['सुरेश प्रजापति', null, '9876503005', 'स्वयंसेवक बनना चाहता हूँ', 'मैं संगठन के कार्यों में स्वयंसेवक के रूप में जुड़ना चाहता हूँ।', false, 0],
            ['कविता यादव', 'kavita@example.com', '9876503006', 'सदस्यता कार्ड की प्रति', 'मेरा सदस्यता कार्ड खो गया है। दूसरी प्रति कैसे मिलेगी?', true, 6],
        ];

        foreach ($rows as [$name, $email, $phone, $subject, $message, $read, $daysAgo]) {
            $model = ContactMessage::query()->updateOrCreate(['phone' => $phone, 'subject' => $subject], [
                'name' => $name,
                'email' => $email,
                'message' => $message,
                'ip' => '127.0.0.1',
                'read_at' => $read ? now()->subDays($daysAgo)->addHours(3) : null,
            ]);

            $model->forceFill(['created_at' => now()->subDays($daysAgo)])->save();
        }
    }

    private function seedTranslations(): void
    {
        $rows = [
            ['home.hero.title', 'श्रमिकों के अधिकार, सम्मान और सुरक्षा के लिए समर्पित', true],
            ['home.hero.subtitle', 'मिर्जापुर असंगठित कामगार यूनियन — हर श्रमिक की आवाज़', true],
            ['membership.form.title', 'सदस्यता पंजीकरण फॉर्म', true],
            ['membership.payment.title', 'सदस्यता शुल्क का भुगतान', false],
            ['grievance.form.title', 'श्रमिक समस्या दर्ज करें', true],
            ['contact.form.title', 'हमें संदेश भेजें', false],
            ['footer.rights', 'सर्वाधिकार सुरक्षित', true],
            ['notice.label', 'महत्वपूर्ण सूचना', false],
        ];

        foreach ($rows as [$key, $value, $reviewed]) {
            Translation::query()->updateOrCreate(['locale' => 'hi', 'key' => $key], [
                'value' => $value,
                'source' => 'manual',
                'reviewed' => $reviewed,
            ]);
        }
    }

    private function seedAuditLogs(?User $admin, array $members): void
    {
        $logs = [
            ['auth.login', null, null, [], ['email' => $admin?->email], 12],
            ['member.created', $members[0], null, [], ['membership_no' => $members[0]->membership_no], 11],
            ['member.status_updated', $members[0], null, ['status' => 'pending'], ['status' => 'active'], 10],
            ['payment.paid', $members[0], 'MembershipPayment', ['status' => 'pending'], ['status' => 'paid'], 9],
            ['grievance.created', $members[2], null, [], ['subject' => 'मनरेगा जॉब कार्ड में नाम नहीं'], 7],
            ['grievance.updated', $members[2], null, ['status' => 'pending'], ['status' => 'in_progress'], 6],
            ['news.created', null, 'News', [], ['title' => 'मिर्जापुर में श्रमिक अधिकार जागरूकता रैली'], 5],
            ['gallery.created', null, 'Gallery', [], ['title' => 'श्रमिक सभा, मिर्जापुर'], 4],
            ['settings.updated', null, null, ['membership_fee' => '50', 'membership_open' => '0'], ['membership_fee' => '100', 'membership_open' => '1'], 3],
            ['contact_message.read', null, 'ContactMessage', ['read_at' => null], ['read_at' => now()->subDays(2)->toIso8601String()], 2],
            ['auth.login_failed', null, null, [], ['email' => 'unknown@example.com'], 1],
        ];

        foreach ($logs as [$action, $model, $type, $old, $new, $daysAgo]) {
            $modelClass = $type ? 'App\\Models\\'.$type : ($model ? get_class($model) : null);
            $modelId = $type ? ($members[0]->payments()->first()?->id ?? 1) : $model?->id;

            $log = AuditLog::query()->create([
                'user_id' => $action === 'auth.login_failed' ? null : $admin?->id,
                'action' => $action,
                'auditable_type' => $modelClass,
                'auditable_id' => $modelClass ? $modelId : null,
                'old_values' => $old ?: null,
                'new_values' => $new ?: null,
                'ip' => '127.0.0.1',
                'user_agent' => 'DemoSeeder',
            ]);

            $log->forceFill(['created_at' => now()->subDays($daysAgo)])->save();
        }
    }

    private function putSvg($disk, string $path, string $contents): void
    {
        if (! $disk->exists($path)) {
            $disk->put($path, $contents);
        }
    }

    private function svg(string $title, string $from, string $to, int $width, int $height): string
    {
        $title = htmlspecialchars($title, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $initial = htmlspecialchars(mb_substr($title, 0, 1), ENT_QUOTES | ENT_XML1, 'UTF-8');
        $fontSize = (int) round(min($width, $height) / 18);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 {$width} {$height}">
<defs>
<linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="{$from}"/><stop offset="1" stop-color="{$to}"/></linearGradient>
</defs>
<rect width="{$width}" height="{$height}" fill="url(#g)"/>
<circle cx="{$width}" cy="0" r="{$height}" fill="#ffffff" opacity="0.05"/>
<circle cx="0" cy="{$height}" r="{$height}" fill="#000000" opacity="0.10"/>
<text x="50%" y="48%" fill="#ffffff" opacity="0.16" font-family="sans-serif" font-size="{$height}" font-weight="700" text-anchor="middle">{$initial}</text>
<text x="50%" y="54%" fill="#ffffff" font-family="sans-serif" font-size="{$fontSize}" font-weight="600" text-anchor="middle">{$title}</text>
<text x="50%" y="61%" fill="#ffffff" opacity="0.75" font-family="sans-serif" font-size="{$fontSize}" text-anchor="middle">MAKU · मिर्जापुर</text>
</svg>
SVG;
    }
}
