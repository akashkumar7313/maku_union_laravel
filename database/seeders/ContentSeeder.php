<?php

namespace Database\Seeders;

use App\Models\ActionCard;
use App\Models\Banner;
use App\Models\MenuItem;
use App\Models\MissionCard;
use App\Models\NewsCategory;
use App\Models\Notice;
use App\Models\Page;
use App\Models\WorkerCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['मनरेगा मजदूर', 'manrega-majdoor', 'महात्मा गांधी राष्ट्रीय ग्रामीण रोजगार गारंटी अधिनियम के अंतर्गत कार्यरत श्रमिकों के अधिकार, मजदूरी और जॉब कार्ड से जुड़ी सहायता।', 'bi-tree', 1],
            ['निर्माण श्रमिक', 'nirman-shramik', 'भवन, सड़क और अन्य निर्माण कार्यों में लगे श्रमिकों के लिए पंजीकरण, सुरक्षा और कल्याण योजनाओं की जानकारी।', 'bi-cone-striped', 2],
            ['राज मिस्त्री', 'raj-mistri', 'चिनाई, प्लास्टर और निर्माण कार्य में कुशल श्रमिकों के हितों की रक्षा एवं मार्गदर्शन।', 'bi-hammer', 3],
            ['कार्यशाला श्रमिक', 'karyashala-shramik', 'कारखानों और कार्यशालाओं में कार्यरत श्रमिकों के लिए सुरक्षित कार्यस्थल एवं श्रम अधिकार।', 'bi-tools', 4],
            ['पत्थर एवं खदान श्रमिक', 'patthar-khadan-shramik', 'पत्थर तोड़ने और खदान कार्य में लगे श्रमिकों के स्वास्थ्य, सुरक्षा और मजदूरी संबंधी सहायता।', 'bi-gem', 5],
            ['अन्य असंगठित श्रमिक', 'anya-asangathit-shramik', 'घरेलू कामगार, ठेला-रेहड़ी, परिवहन और अन्य असंगठित क्षेत्रों में कार्यरत श्रमिकों का सहयोग।', 'bi-people', 6],
        ];

        $categoryImages = [
            'manrega-majdoor' => 'demo/real/category-1.jpg',
            'nirman-shramik' => 'demo/real/category-1.jpg',
            'raj-mistri' => 'demo/real/category-1.jpg',
            'karyashala-shramik' => 'demo/real/category-4.jpg',
            'patthar-khadan-shramik' => 'demo/real/category-5.jpg',
            'anya-asangathit-shramik' => 'demo/real/category-2.jpg',
        ];

        foreach ($categories as [$name, $slug, $description, $icon, $order]) {
            WorkerCategory::updateOrCreate(['slug' => $slug], [
                'name' => $name,
                'description' => $description,
                'icon' => $icon,
                'image' => $categoryImages[$slug] ?? null,
                'sort_order' => $order,
                'is_active' => true,
            ]);
        }

        $pages = [
            ['about-us', 'हमारे बारे में', 'श्रमिकों के अधिकारों के लिए समर्पित संगठन', true],
            ['our-works', 'हमारे कार्य', 'संगठन द्वारा किए जा रहे प्रमुख कार्य', false],
            ['worker-info', 'श्रमिक जानकारी', 'श्रमिकों के लिए उपयोगी जानकारी और योजनाएँ', false],
            ['organization', 'संगठन', 'MAKU Union की संरचना, उद्देश्य और कार्यक्षेत्र', false],
            ['privacy-policy', 'गोपनीयता नीति', 'आपकी व्यक्तिगत जानकारी की सुरक्षा', true],
            ['terms', 'नियम एवं शर्तें', 'वेबसाइट एवं सदस्यता के नियम', true],
            ['refund-policy', 'भुगतान वापसी नीति', 'शुल्क एवं वापसी संबंधी नीति', true],
        ];

        $pageImages = [
            'about-us' => 'demo/real/gallery-1.jpg',
            'our-works' => 'demo/real/news-3.jpg',
            'worker-info' => 'demo/real/category-1.jpg',
            'organization' => 'demo/real/news-2.jpg',
            'privacy-policy' => 'demo/real/gallery-7.jpg',
            'terms' => 'demo/real/branch-1.jpg',
            'refund-policy' => 'demo/real/branch-2.jpg',
        ];

        foreach ($pages as [$slug, $title, $subtitle, $footer]) {
            Page::updateOrCreate(['slug' => $slug], [
                'title' => $title,
                'subtitle' => $subtitle,
                'body' => $this->pageBody($slug),
                'image' => $pageImages[$slug] ?? null,
                'is_active' => true,
                'show_in_footer' => $footer,
            ]);
        }

        $menu = [
            ['मुख्य पृष्ठ', 'home', null, 1],
            ['हमारे बारे में', null, '/about-us', 2],
            ['सदस्यता', 'membership.register', null, 3],
            ['श्रमिक सहायता', 'grievances.create', null, 4],
            ['सदस्यता खोजें', 'membership.lookup', null, 5],
            ['हमारे कार्य', null, '/our-works', 6],
            ['समाचार', 'news.index', null, 7],
            ['श्रमिक जानकारी', null, '/worker-info', 8],
            ['संगठन', null, '/organization', 9],
            ['संपर्क करें', 'contact', null, 10],
        ];

        foreach ($menu as [$label, $route, $url, $order]) {
            MenuItem::updateOrCreate(['label' => $label], [
                'route_name' => $route,
                'url' => $url,
                'sort_order' => $order,
                'is_active' => true,
            ]);
        }

        Banner::updateOrCreate(['title' => 'मजदूर का अधिकार'], [
            'highlight' => 'हमारा संकल्प',
            'subtitle' => 'असंगठित एवं निर्माण श्रमिकों के अधिकारों, सम्मान और सामाजिक सुरक्षा के लिए समर्पित संगठन।',
            'button_text' => 'सदस्य बनें',
            'button_url' => '/membership/register',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $actions = [
            ['सदस्य बनें', 'आज ही सदस्यता के लिए पंजीकरण करें', 'green', 'bi-person-plus', '/membership/register', 1],
            ['सदस्यता नवीनीकरण', 'अपनी सदस्यता का नवीनीकरण करें', 'blue', 'bi-arrow-repeat', '/membership/lookup', 2],
            ['श्रमिक समस्या दर्ज करें', 'अपनी समस्या दर्ज करें और स्थिति देखें', 'orange', 'bi-exclamation-circle', '/grievance', 3],
        ];

        foreach ($actions as [$title, $desc, $color, $icon, $url, $order]) {
            ActionCard::updateOrCreate(['title' => $title], [
                'description' => $desc,
                'color' => $color,
                'icon' => $icon,
                'url' => $url,
                'sort_order' => $order,
                'is_active' => true,
            ]);
        }

        $missions = [
            ['हमारा उद्देश्य', 'प्रत्येक श्रमिक को उसके श्रम का सम्मान, उचित मजदूरी और सामाजिक सुरक्षा का अधिकार सुनिश्चित करना।', 'bi-bullseye', 1],
            ['श्रमिकों की आवाज़', 'श्रमिकों की समस्याओं को दर्ज कर उन्हें प्रशासन और श्रम विभाग तक पहुँचाना।', 'bi-megaphone', 2],
            ['श्रमिकों के अधिकारों की रक्षा', 'श्रम कानूनों, सरकारी योजनाओं और सुरक्षा प्रावधानों के प्रति श्रमिकों को जागरूक करना।', 'bi-shield-check', 3],
        ];

        foreach ($missions as [$title, $desc, $icon, $order]) {
            MissionCard::updateOrCreate(['title' => $title], [
                'description' => $desc,
                'icon' => $icon,
                'sort_order' => $order,
                'is_active' => true,
            ]);
        }

        foreach (['श्रमिक समाचार', 'संगठन समाचार', 'सूचनाएँ'] as $index => $name) {
            NewsCategory::updateOrCreate(['name' => $name], [
                'slug' => Str::slug($name) ?: 'news-category-'.$index,
                'is_active' => true,
            ]);
        }

        Notice::updateOrCreate(['title' => 'MAKU Union की आधिकारिक वेबसाइट पर आपका स्वागत है'], [
            'body' => 'सदस्यता, शिकायत और श्रमिक जानकारी से जुड़ी सेवाएँ अब ऑनलाइन उपलब्ध हैं।',
            'url' => '/membership/register',
            'is_active' => true,
        ]);
    }

    private function pageBody(string $slug): string
    {
        return match ($slug) {
            'about-us' => <<<'HTML'
<p>मिर्जापुर असंगठित कामगार यूनियन (MAKU Union) असंगठित एवं निर्माण श्रमिकों के अधिकारों की रक्षा के लिए कार्य करने वाला श्रमिक संगठन है। संगठन का उद्देश्य श्रमिकों को उनके अधिकारों के प्रति जागरूक करना, सामाजिक सुरक्षा योजनाओं से जोड़ना और उनकी समस्याओं का समाधान कराना है।</p>
<h3>हमारा उद्देश्य</h3>
<p>प्रत्येक श्रमिक को उसके श्रम का सम्मान, न्यूनतम मजदूरी, सुरक्षित कार्यस्थल और सामाजिक सुरक्षा का अधिकार मिले।</p>
<h3>हमारा मिशन</h3>
<p>श्रमिकों को संगठित करना, उनकी समस्याओं को दर्ज करना और सरकारी योजनाओं की जानकारी उपलब्ध कराना।</p>
<h3>हमारी दृष्टि</h3>
<p>एक ऐसा समाज, जहाँ हर असंगठित श्रमिक सुरक्षित, सम्मानित और अपने अधिकारों के प्रति सजग हो।</p>
HTML,
            'our-works' => <<<'HTML'
<ul>
<li>श्रमिकों का पंजीकरण एवं सदस्यता प्रबंधन।</li>
<li>श्रमिकों की शिकायतों का दर्जीकरण, अनुसरण और समाधान।</li>
<li>सरकारी योजनाओं एवं श्रम कानूनों पर जागरूकता कार्यक्रम।</li>
<li>निर्माण श्रमिक कल्याण बोर्ड एवं अन्य योजनाओं में सहायता।</li>
<li>श्रमिकों के लिए प्रशिक्षण एवं मार्गदर्शन शिविर।</li>
</ul>
<p>यह विवरण प्रशासन द्वारा नियमित रूप से अद्यतन किया जा सकता है।</p>
HTML,
            'worker-info' => <<<'HTML'
<h3>श्रमिकों के लिए उपयोगी जानकारी</h3>
<ul>
<li>निर्माण श्रमिक पंजीकरण एवं कल्याण बोर्ड योजनाएँ।</li>
<li>मनरेगा जॉब कार्ड, मजदूरी और रोजगार से जुड़े अधिकार।</li>
<li>सामाजिक सुरक्षा, बीमा एवं पेंशन योजनाएँ।</li>
<li>सुरक्षित कार्यस्थल एवं दुर्घटना में मुआवजे की प्रक्रिया।</li>
<li>श्रम विभाग में शिकायत दर्ज करने की प्रक्रिया।</li>
</ul>
<p>योजनाओं की पात्रता एवं ताज़ा जानकारी के लिए संगठन कार्यालय से संपर्क करें।</p>
HTML,
            'organization' => <<<'HTML'
<p>MAKU Union का संगठन श्रमिक सदस्यों, क्षेत्रीय पदाधिकारियों और कार्यालय टीम के सहयोग से कार्य करता है। संगठन का केंद्र मिर्जापुर, उत्तर प्रदेश में है और आसपास के असंगठित श्रमिकों तक सहायता पहुँचाने का प्रयास करता है।</p>
<h3>संगठन की संरचना</h3>
<ul>
<li>अध्यक्ष एवं कार्यकारी समिति</li>
<li>क्षेत्रीय समन्वयक</li>
<li>सदस्यता एवं शिकायत प्रकोष्ठ</li>
<li>स्वयंसेवक एवं जागरूकता दल</li>
</ul>
<p>पदाधिकारियों और शाखाओं की जानकारी प्रशासनिक पैनल से जोड़ी जा सकती है।</p>
HTML,
            'privacy-policy' => <<<'HTML'
<p>यह गोपनीयता नीति बताती है कि MAKU Union आपकी व्यक्तिगत जानकारी कैसे एकत्र, उपयोग और सुरक्षित करता है।</p>
<h3>एकत्र की जाने वाली जानकारी</h3>
<p>सदस्यता एवं शिकायत के समय नाम, मोबाइल नंबर, पता, श्रमिक श्रेणी और आवश्यक दस्तावेज़ लिए जाते हैं।</p>
<h3>उपयोग</h3>
<p>यह जानकारी सदस्यता प्रबंधन, शिकायत निवारण, योजनाओं की जानकारी और संगठन से संपर्क के लिए उपयोग की जाती है।</p>
<h3>सुरक्षा एवं साझा करना</h3>
<p>जानकारी केवल अधिकृत पदाधिकारियों के पास रखी जाती है। कानूनी आवश्यकता के अतिरिक्त तीसरे पक्ष के साथ साझा नहीं की जाती।</p>
<h3>आपके अधिकार</h3>
<p>आप अपनी जानकारी में सुधार या हटाने का अनुरोध संगठन कार्यालय से कर सकते हैं।</p>
<p><strong>नोट:</strong> यह नीति संगठन द्वारा अंतिम समीक्षा के अधीन है और आवश्यकता अनुसार अद्यतन की जाएगी।</p>
HTML,
            'terms' => <<<'HTML'
<h3>वेबसाइट के उपयोग की शर्तें</h3>
<p>इस वेबसाइट का उपयोग करते समय आप निम्नलिखित शर्तों से सहमत होते हैं।</p>
<ul>
<li>सदस्यता एवं शिकायत फॉर्म में दी गई जानकारी सत्य और पूर्ण होनी चाहिए।</li>
<li>गलत पहचान, झूठी जानकारी या दुरुपयोग पर सदस्यता निरस्त की जा सकती है।</li>
<li>वेबसाइट पर उपलब्ध सामग्री संगठन की संपत्ति है; बिना अनुमति व्यावसायिक उपयोग वर्जित है।</li>
<li>सेवाएँ उपलब्धता के अधीन हैं और तकनीकी रखरखाव के कारण अस्थायी रूप से बंद हो सकती हैं।</li>
</ul>
<p><strong>नोट:</strong> अंतिम शर्तें संगठन द्वारा अनुमोदित संस्करण होंगी।</p>
HTML,
            'refund-policy' => <<<'HTML'
<h3>सदस्यता शुल्क एवं वापसी नीति</h3>
<p>सदस्यता शुल्क संगठन के संचालन और श्रमिक सेवाओं के लिए लिया जाता है।</p>
<ul>
<li>सदस्यता सक्रिय होने के बाद सामान्यतः शुल्क वापस नहीं किया जाता।</li>
<li>तकनीकी कारण से दोहरा भुगतान होने पर अतिरिक्त राशि वापस की जाएगी।</li>
<li>गलत या अपूर्ण विवरण के कारण अस्वीकृत सदस्यता के मामले में संगठन कार्यालय निर्णय लेगा।</li>
<li>वापसी अनुरोध संबंधित भुगतान विवरण के साथ संगठन कार्यालय में किया जाना चाहिए।</li>
</ul>
<p><strong>नोट:</strong> वास्तविक शुल्क राशि प्रशासनिक सेटिंग्स से नियंत्रित होती है और भुगतान से पहले दिखाई जाती है।</p>
HTML,
            default => '',
        };
    }
}
