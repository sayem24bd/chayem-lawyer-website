<?php
// === পেজ সেটিংস ===
$pageTitle = "উচ্চ আদালতের গুরুত্বপূর্ণ সিদ্ধান্তসমূহ - মোঃ ছায়েম";
$currentPage = "rulings"; // এই পেজের আইডি (CSS ও JS এ ব্যবহারের জন্য)

// === হেডার লোড ===
require 'partials/header.php';

// === নমুনা ডেটা (বাস্তবে ডেটাবেস থেকে আসবে) ===
$rulingsData = [
    [
        'id' => 'rule001',
        'category' => 'পারিবারিক আইন', // <-- ক্যাটাগরি ডেটা
        'icon' => 'fa-users',
        'title' => 'বিবাহবিচ্ছেদ পরবর্তী সন্তানের অভিভাবকত্ব নির্ধারণ',
        'citation' => 'XYZ বনাম ABC, 55 DLR (AD) 123',
        'summary' => 'এই মামলায় মহামান্য আপীল বিভাগ সিদ্ধান্ত প্রদান করেন যে, সন্তানের অভিভাবকত্ব নির্ধারণের ক্ষেত্রে সন্তানের সার্বিক কল্যাণ (Welfare of the Child) সর্বাধিক গুরুত্ব পাবে। শুধুমাত্র কোনো পক্ষের আইনগত অধিকার বিবেচনা করা যথেষ্ট নয়। আদালত সন্তানের বয়স, মানসিক অবস্থা, এবং পিতামাতার যোগ্যতা ও পরিবেশ বিস্তারিত পর্যালোচনা করে সিদ্ধান্ত গ্রহণ করবেন।',
        'keywords' => ['অভিভাবকত্ব', 'সন্তানের কল্যাণ', 'পারিবারিক আদালত', 'বিবাহবিচ্ছেদ']
    ],
    [
        'id' => 'rule002',
        'category' => 'ভূমি আইন', // <-- ক্যাটাগরি ডেটা
        'icon' => 'fa-map-marked-alt',
        'title' => 'বায়না চুক্তি বলবৎকরণ ও সুনির্দিষ্ট প্রতিকার',
        'citation' => 'কবির হোসেন বনাম রাষ্ট্র, 60 DLR (HCD) 456', // আগের উত্তরে ভুলবশত 'মদিনাত ইসরায়েল' ছিল, সংশোধন করা হলো
        'summary' => 'মহামান্য হাইকোর্ট বিভাগ এই সিদ্ধান্তে উল্লেখ করেন যে, রেজিস্ট্রিকৃত বায়না চুক্তি নির্ধারিত সময়ের মধ্যে বলবৎ করার জন্য সুনির্দিষ্ট প্রতিকার আইনের অধীনে মামলা দায়ের করা যায়। তবে বাদীকে অবশ্যই প্রমাণ করতে হবে যে তিনি চুক্তির নিজ অংশ পালনে ইচ্ছুক ও প্রস্তুত ছিলেন (Ready and willing to perform)। চুক্তির শর্তাবলী এবং পারিপার্শ্বিক অবস্থা আদালত বিবেচনায় নিবেন।',
        'keywords' => ['বায়না চুক্তি', 'সুনির্দিষ্ট প্রতিকার আইন', 'রেজিস্ট্রেশন', 'সম্পত্তি হস্তান্তর']
    ],
    [
        'id' => 'rule003',
        'category' => 'ফৌজদারী আইন', // <-- ক্যাটাগরি ডেটা
        'icon' => 'fa-gavel',
        'title' => 'আগাম জামিন (Anticipatory Bail) মঞ্জুরের নীতিমালা',
        'citation' => 'রাষ্ট্র বনাম অধ্যাপক ড. ইউনুস এবং অন্যান্য, 70 DLR (AD) 789',
        'summary' => 'এই গুরুত্বপূর্ণ সিদ্ধান্তে আপীল বিভাগ আগাম জামিন মঞ্জুরের ক্ষেত্রে কিছু নীতিমালা নির্ধারণ করেন। আদালত উল্লেখ করেন যে, শুধুমাত্র হয়রানি বা অবমাননার উদ্দেশ্যে মিথ্যা মামলা দায়ের করা হয়েছে - এমন সুস্পষ্ট প্রমাণ অথবা প্রাথমিক দৃষ্টিতে আবেদনকারীর বিরুদ্ধে অভিযোগের ভিত্তিহীনতা প্রতীয়মান হলেই কেবল আগাম জামিন বিবেচনা করা যেতে পারে। এটি কোনো সাধারণ নিয়ম নয়, বরং ব্যতিক্রমী প্রতিকার।',
        'keywords' => ['আগাম জামিন', 'ফৌজদারী কার্যবিধি', 'Anticipatory Bail', 'মিথ্যা মামলা', 'হয়রানি']
    ],
    [
        'id' => 'rule004',
        'category' => 'চুক্তি আইন', // <-- ক্যাটাগরি ডেটা
        'icon' => 'fa-file-signature',
        'title' => 'চুক্তির মৌলিক ত্রুটি (Fundamental Mistake)',
        'citation' => 'বেঙ্গল কর্পোরেশন বনাম আব্দুল্লাহ ট্রেডার্স, 48 DLR (HCD) 321',
        'summary' => 'চুক্তি আইনের অধীনে, যদি চুক্তিকারী উভয় পক্ষ চুক্তির কোনো মৌলিক বিষয়ে (Essential matter) ভুল ধারণার বশবর্তী হয়ে চুক্তি সম্পাদন করে, তবে সেই চুক্তি বাতিল (Void) বলে গণ্য হবে। এই মামলায় হাইকোর্ট বিভাগ ব্যাখ্যা করেন কোন বিষয়গুলো চুক্তির মৌলিক ত্রুটি হিসেবে বিবেচিত হতে পারে এবং এর আইনগত ফলাফল কী হবে।',
        'keywords' => ['চুক্তি আইন', 'ভুল ধারণা', 'Mistake of Fact', 'বাতিল চুক্তি', 'Void Agreement']
    ],
    [
        'id' => 'rule005',
        'category' => 'পারিবারিক আইন', // <-- ক্যাটাগরি ডেটা (আরও একটি উদাহরণ)
        'icon' => 'fa-female', // ভিন্ন আইকন
        'title' => 'মুসলিম আইনে স্ত্রীর ভরণপোষণ পাওয়ার অধিকার',
        'citation' => 'হেফজুর রহমান বনাম শামসুন্নাহার বেগম, 50 DLR (AD) 45',
        'summary' => 'মহামান্য আপীল বিভাগ এই মামলায় পুনর্ব্যক্ত করেন যে, মুসলিম আইন অনুযায়ী একজন স্ত্রী তার স্বামীর কাছ থেকে যথাযথ ভরণপোষণ পাওয়ার অধিকারী। স্বামী ভরণপোষণ দিতে অপারগতা প্রকাশ করলে বা অবহেলা করলে স্ত্রী আদালতের মাধ্যমে তা আদায় করতে পারবেন। ইদ্দতকালীন সময় ছাড়াও নির্দিষ্ট পরিস্থিতিতে তালাকপ্রাপ্তা স্ত্রীও ভরণপোষণ পেতে পারেন।',
        'keywords' => ['ভরণপোষণ', 'মুসলিম পারিবারিক আইন', 'স্ত্রীর অধিকার', 'Maintenance']
    ]
];

?>

    <!-- === পেজ হেডার === -->
    <section class="page-header section-padding rulings-header bg-gradient-subtle">
         <div class="container text-center">
             <i class="fas fa-balance-scale header-icon animate__animated animate__pulse animate__infinite"></i> <!-- অ্যানিমেশন ক্লাস যোগ -->
             <h1 class="page-title">উচ্চ আদালতের সিদ্ধান্ত সংগ্রহ</h1>
             <p class="page-subtitle">আইনের গুরুত্বপূর্ণ ব্যাখ্যা ও নজির সম্পর্কে জানুন (প্রাথমিক ধারণার জন্য)</p>
         </div>
     </section>

    <!-- === ডিসক্লেইমার সেকশন === -->
    <section class="disclaimer-section section-padding bg-light-warning">
        <div class="container">
            <div class="disclaimer-box animate__animated animate__fadeInUp">
                <h3 class="disclaimer-title"><i class="fas fa-exclamation-triangle"></i> গুরুত্বপূর্ণ বিজ্ঞপ্তি</h3>
                <p>এই ওয়েবসাইটে উপস্থাপিত আদালতের সিদ্ধান্তসমূহের সারসংক্ষেপ শুধুমাত্র সাধারণ তথ্য ও শিক্ষামূলক উদ্দেশ্যে প্রদান করা হয়েছে। এটি কোনোভাবেই পূর্ণাঙ্গ বা চূড়ান্ত আইনি ব্যাখ্যা নয় এবং এটিকে কোনো নির্দিষ্ট কেসের জন্য আইনি পরামর্শ হিসেবে গ্রহণ করা উচিত নয়। আইন ও আদালতের সিদ্ধান্ত পরিবর্তনশীল। আপনার নির্দিষ্ট আইনি সমস্যার জন্য অনুগ্রহ করে একজন অভিজ্ঞ আইনজীবীর সাথে সরাসরি পরামর্শ করুন। এই ওয়েবসাইটের তথ্যের উপর ভিত্তি করে গৃহীত কোনো পদক্ষেপের জন্য কর্তৃপক্ষ দায়ী থাকবে না।</p>
            </div>
        </div>
    </section>

    <!-- === সার্চ ও ফিল্টার বার === -->
    <section class="filter-search-bar section-padding">
        <div class="container">
            <div class="search-wrapper animate__animated animate__fadeInLeft">
                 <input type="text" id="rulingSearchInput" placeholder="বিষয়, আইন, ধারা বা মামলা নম্বর দিয়ে খুঁজুন..." aria-label="সিদ্ধান্ত অনুসন্ধান">
                 <button aria-label="অনুসন্ধান"><i class="fas fa-search"></i></button>
            </div>
            <div class="filter-wrapper animate__animated animate__fadeInRight">
                <label for="categoryFilter">বিষয় অনুযায়ী দেখুন:</label>
                <select id="categoryFilter" aria-label="বিষয় অনুযায়ী ফিল্টার করুন">
                    <option value="all">সকল বিষয়</option>
                    <?php
                    // ডেটা থেকে ইউনিক ক্যাটাগরিগুলো বের করা (বাস্তবে ক্যাটাগরি তালিকা নির্দিষ্ট থাকবে)
                    $categories = array_unique(array_column($rulingsData, 'category'));
                    sort($categories); // সর্ট করা
                    foreach ($categories as $category):
                    ?>
                        <option value="<?= htmlspecialchars($category) ?>"><?= htmlspecialchars($category) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </section>

    <!-- === রুলিং ডিসপ্লে এলাকা (অ্যাকরডিয়ন) === -->
    <section class="rulings-display section-padding bg-light">
        <div class="container">
            <!-- এই কন্টেইনারটি JS এ ব্যবহৃত হয় -->
            <div class="rulings-accordion-container">

                <?php if (!empty($rulingsData)): ?>
                    <?php foreach ($rulingsData as $index => $ruling): ?>
                        <!-- data-category অ্যাট্রিবিউট যোগ করা হয়েছে ফিল্টারিংয়ের জন্য -->
                        <div class="accordion-item animate__animated animate__fadeInUp" data-category="<?= htmlspecialchars($ruling['category']) ?>" style="animation-delay: <?= $index * 0.05 ?>s;"> <!-- ডিলেড অ্যানিমেশন -->
                            <button class="accordion-header"
                                    id="header-<?= htmlspecialchars($ruling['id']) ?>"
                                    aria-expanded="false"
                                    aria-controls="content-<?= htmlspecialchars($ruling['id']) ?>">

                                <span class="header-icon-wrapper">
                                    <i class="fas <?= htmlspecialchars($ruling['icon'] ?? 'fa-landmark') ?>"></i> <!-- ডিফল্ট আইকন যদি না থাকে -->
                                </span>
                                <span class="header-title"><?= htmlspecialchars($ruling['title']) ?></span>
                                <span class="header-citation">(<?= htmlspecialchars($ruling['citation']) ?>)</span>
                                <span class="accordion-indicator"><i class="fas fa-chevron-down"></i></span>
                            </button>
                            <div id="content-<?= htmlspecialchars($ruling['id']) ?>"
                                 class="accordion-content"
                                 role="region"
                                 aria-labelledby="header-<?= htmlspecialchars($ruling['id']) ?>"
                                 hidden> <!-- শুরুতে hidden থাকবে -->
                                <div class="content-inner">
                                    <h4><i class="fas fa-book-open"></i> সারসংক্ষেপ:</h4>
                                    <p><?= nl2br(htmlspecialchars($ruling['summary'])) ?></p>
                                    <?php if (!empty($ruling['keywords'])): ?>
                                        <div class="keywords-section">
                                            <strong>কীওয়ার্ডস:</strong>
                                            <?php foreach ($ruling['keywords'] as $keyword): ?>
                                                <span class="keyword-tag"><?= htmlspecialchars($keyword) ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- যদি কোনো ডেটা না থাকে -->
                    <p class="text-center no-data-message">দুঃখিত, এই মুহূর্তে কোনো সিদ্ধান্ত তালিকাভুক্ত নেই।</p>
                <?php endif; ?>

                 <!-- No result message placeholder (JS দ্বারা যোগ/রিমুভ হবে) -->
                 <!-- <p class="no-result-message text-center text-danger" style="display: none;">আপনার অনুসন্ধান বা ফিল্টারের সাথে মিলে এমন কোনো সিদ্ধান্ত পাওয়া যায়নি।</p> -->

            </div> <!-- /.rulings-accordion-container -->
        </div> <!-- /.container -->
    </section> <!-- /.rulings-display -->


<?php
// === ফুটার লোড ===
require 'partials/footer.php';
?>