<?php
$pageTitle = "আমাদের বিশেষায়িত সেবাসমূহ - মোঃ ছায়েম"; // এই পেজের টাইটেল
$currentPage = "services"; // এই পেজের আইডি (মেনু হাইলাইটের জন্য)
require 'partials/header.php'; // হেডার অংশ যুক্ত করা (এখন services.css সহ লোড হবে)
?>

    <!-- পেজ হেডার - ভাইব্রেন্ট গ্র্যাডিয়েন্ট -->
    <section class="page-header section-padding bg-gradient-vibrant">
         <div class="container text-center">
             <h1 class="page-title text-white">আমাদের আইনি সেবাসমূহ</h1>
             <p class="page-subtitle text-white-70">আপনার প্রতিটি আইনি প্রয়োজনে আমরা আছি আপনার পাশে</p>
         </div>
     </section>

     <!-- সেবার পরিচিতি -->
     <section class="services-intro section-padding pb-4">
        <div class="container">
            <div class="text-center mx-auto" style="max-width: 800px;">
                 <h2 class="section-title-alt mb-3"><span class="text-primary">দক্ষতা</span> ও <span class="text-primary">অভিজ্ঞতার</span> সমন্বয়ে সেরা সমাধান</h2>
                 <p class="lead text-muted mb-5">
                    আইনের প্রতিটি ক্ষেত্রে আমাদের রয়েছে গভীর জ্ঞান ও практический অভিজ্ঞতা। আমরা শুধুমাত্র আইনি প্রক্রিয়া অনুসরণ করি না, বরং আপনার পরিস্থিতি অনুযায়ী সবচেয়ে কার্যকর কৌশল নির্ধারণ করে থাকি।
                 </p>
            </div>
        </div>
     </section>

     <!-- সেবাসমূহের তালিকা - অ্যানিমেশন সহ কার্ড -->
     <section class="services-grid-section section-padding pt-0">
        <div class="container">
            <div class="services-grid animated-grid">

                <!-- সেবা কার্ড ১: পারিবারিক আইন -->
                <div class="service-card" data-delay="0s">
                    <div class="service-icon-wrapper style-1">
                        <i class="fas fa-users service-icon"></i>
                    </div>
                    <h4 class="service-title">পারিবারিক আইন</h4>
                    <p class="service-description">
                        বিবাহ, তালাক, দেনমোহর, ভরনপোষণ, সন্তানের অভিভাবকত্ব ও পরিদর্শন অধিকার সহ পারিবারিক আদালতের যাবতীয় বিষয়ে পরামর্শ ও মামলা পরিচালনা।
                    </p>
                </div>

                <!-- সেবা কার্ড ২: দেওয়ানী মামলা -->
                <div class="service-card" data-delay="0.1s">
                     <div class="service-icon-wrapper style-2">
                        <i class="fas fa-landmark service-icon"></i>
                    </div>
                    <h4 class="service-title">দেওয়ানী মামলা</h4>
                    <p class="service-description">
                        চুক্তি সংক্রান্ত বিরোধ, আর্থিক লেনদেন, পাওনা আদায়, সম্পত্তি বন্টন, নিষেধাজ্ঞা, ঘোষণামূলক মামলা সহ সকল প্রকার দেওয়ানী মোকদ্দমা।
                    </p>
                </div>

                <!-- সেবা কার্ড ৩: ফৌজদারী মামলা -->
                <div class="service-card" data-delay="0.2s">
                     <div class="service-icon-wrapper style-3">
                        <i class="fas fa-gavel service-icon"></i>
                    </div>
                    <h4 class="service-title">ফৌজদারী মামলা</h4>
                    <p class="service-description">
                        সকল প্রকার জামিন (Anticipatory/Regular), FIR, চার্জশীট, নালিশী মামলা, আপিল ও রিভিশন সহ ফৌজদারী আদালতে আপনার प्रतिरक्षा নিশ্চিতকরণ।
                    </p>
                </div>

                 <!-- সেবা কার্ড ৪: জমি-জমা সংক্রান্ত -->
                 <div class="service-card" data-delay="0s">
                    <div class="service-icon-wrapper style-4">
                        <i class="fas fa-map-marked-alt service-icon"></i>
                    </div>
                    <h4 class="service-title">জমি-জমা সংক্রান্ত আইন</h4>
                    <p class="service-description">
                        জমির মালিকানা যাচাই, দলিল বিশ্লেষণ, নামজারি (মিউটেশন), জমাভাগ, বাটোয়ারা মামলা এবং ভূমি জরিপ সংক্রান্ত যাবতীয় আইনি সহায়তা।
                    </p>
                </div>

                 <!-- সেবা কার্ড ৫: আইনি নোটিশ ও ডকুমেন্টেশন -->
                 <div class="service-card" data-delay="0.1s">
                    <div class="service-icon-wrapper style-5">
                        <i class="fas fa-file-signature service-icon"></i>
                    </div>
                    <h4 class="service-title">আইনি নোটিশ ও ডকুমেন্টেশন</h4>
                    <p class="service-description">
                         বিভিন্ন বিষয়ে আইনি নোটিশ প্রস্তুত, প্রেরণ ও জবাব প্রদান। চুক্তিপত্র, হলফনামা, পাওয়ার অফ অ্যাটর্নি এবং অন্যান্য দলিলপত্র তৈরি।
                    </p>
                </div>

                 <!-- সেবা কার্ড ৬: কোম্পানি ও বাণিজ্যিক আইন -->
                 <div class="service-card" data-delay="0.2s">
                     <div class="service-icon-wrapper style-6">
                        <i class="fas fa-briefcase service-icon"></i>
                    </div>
                    <h4 class="service-title">কোম্পানি ও বাণিজ্যিক আইন</h4>
                    <p class="service-description">
                         ব্যবসা গঠন, ট্রেড লাইসেন্স, অংশীদারি চুক্তি, কোম্পানি আইন, শ্রম আইন, আমদানি-রপ্তানি বিষয়ক পরামর্শ ও আইনি সহায়তা।
                    </p>
                </div>

            </div> <!-- /.services-grid -->
        </div> <!-- /.container -->
     </section>

      <!-- পরামর্শের জন্য যোগাযোগ সেকশন - ডিজাইন উন্নত -->
     <section class="consultation-cta-v2 section-padding">
        <div class="container consultation-cta-content">
             <div class="cta-icon">
                <i class="fas fa-headset"></i>
             </div>
             <h2 class="mb-3 cta-title">আপনার আইনি যাত্রা শুরু করতে প্রস্তুত?</h2>
             <p class="lead mb-4 cta-subtitle">
                জটিল আইনি প্রক্রিয়া সহজ করতে এবং আপনার অধিকার সুরক্ষিত রাখতে আমরা আছি। একটি কনফিডেনশিয়াল পরামর্শ সেশনের জন্য আজই যোগাযোগ করুন।
             </p>
             <a href="contact.php" class="cta-button cta-button-light">এখনই আলোচনা করুন</a>
        </div>
     </section>

<?php
require 'partials/footer.php'; // ফুটার অংশ যুক্ত করা
?>